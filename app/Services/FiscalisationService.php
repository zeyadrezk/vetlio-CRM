<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceFiscalisation;
use App\Models\Organisation;
use Exception;
use Illuminate\Support\Facades\Log;
use Nticaric\Fiskalizacija\Fiskalizacija;
use Nticaric\Fiskalizacija\Generators\BrojRacunaType;
use Nticaric\Fiskalizacija\Generators\PorezType;
use Nticaric\Fiskalizacija\Generators\RacunType;
use Nticaric\Fiskalizacija\Generators\RacunZahtjev;
use Nticaric\Fiskalizacija\Generators\ZaglavljeType;
use Nticaric\Fiskalizacija\QRGenerator;
use Nticaric\Fiskalizacija\XMLSerializer;

class FiscalisationService
{
    protected bool $naknadnaDostava;
    protected bool $demoMode;

    public function __construct(?bool $naknadnaDostava = false, ?bool $demoMode = null)
    {
        $this->naknadnaDostava = $naknadnaDostava ?? false;
        $this->demoMode = $demoMode ?? env('FISKALIZACIJA_DEMO', true);
    }

    public function fiscalize(Invoice $invoice): array
    {
        try {
            $user = $invoice->user;
            $organisation = $invoice->organisation;
            $branch = $invoice->branch;

            $demo = $this->demoMode ?? ($organisation->fiscalisation_test_mode);

            //First test if all values are set correctly
            $hasErrors = $this->validateFiscalizationData($organisation, $user, $branch);
            if ($hasErrors) {
                return [
                    'success' => false,
                    'error' => $hasErrors
                ];
            }

            $fis = new Fiskalizacija(
                storage_path("app/private/{$organisation->certificate_path}"),
                $organisation->certificate_password,
                'TLS',
                $demo
            );

            $billNumber = new BrojRacunaType(
                $invoice->code,
                $branch->branch_mark, //Poslovni prostor
                "1"
            );

            $pdvGrouped = collect($invoice->invoiceItems)
                ->groupBy(fn($item) => $item->tax_rate)
                ->map(function ($group, $rate) {
                    $osnovica = $group->sum('net_amount');
                    $iznosPdv = $group->sum('tax_amount');
                    return new PorezType(
                        (float)$rate,
                        (float)$osnovica,
                        (float)$iznosPdv,
                        null
                    );
                })->values()->toArray();


            $bill = new RacunType();
            $bill->setOib($organisation->oib);
            $bill->setOznSlijed($organisation->sequence_mark); // primjer
            $bill->setUSustPdv($organisation->in_vat_system);;
            $bill->setDatVrijeme(now()->format('d.m.Y\TH:i:s'));
            $bill->setBrRac($billNumber);
            $bill->setPdv($pdvGrouped ?? []);
            $bill->setIznosUkupno((float)$invoice->total);
            $bill->setNacinPlac($invoice->payment_method_code ?? 'G');
            $bill->setOibOper($user->oib);
            $bill->setNakDost($this->naknadnaDostava);

            $zastKod = $bill->generirajZastKod(
                $fis->getPrivateKey(),
                $bill->getOib(),
                $bill->getDatVrijeme(),
                $billNumber->getBrOznRac(),
                $billNumber->getOznPosPr(),
                $billNumber->getOznNapUr(),
                $bill->getIznosUkupno()
            );
            $bill->setZastKod($zastKod);

            $billRequest = new RacunZahtjev();
            $billRequest->setRacun($bill);
            $billRequest->setZaglavlje(new ZaglavljeType());

            $res = $fis->signAndSend($billRequest);
            $jir = $res->getJir();

            InvoiceFiscalisation::updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'request_xml' => null,
                    'response_xml' => $res->document()->textContent ?? null,
                    'zki' => $zastKod,
                    'jir' => $jir,
                    'qrcode' => $this->generateQrCode($jir, $bill->getDatVrijeme(), $bill->getIznosUkupno()),
                    'error_message' => null,
                ]
            );

            $invoice->update([
                'fiscalization_at' => now(),
                'zki' => $zastKod,
                'jir' => $jir,
                'qrcode' => $this->generateQrCode($jir, $bill->getDatVrijeme(), $bill->getIznosUkupno())
            ]);

            return [
                'success' => true,
                'zki' => $zastKod,
                'jir' => $jir,
                'qrcode' => $this->generateQrCode($jir, $bill->getDatVrijeme(), $bill->getIznosUkupno()),
            ];

        } catch (Exception $e) {
            Log::error("Fiscalisation failed for invoice {$invoice->id}: " . $e->getMessage());

            InvoiceFiscalisation::updateOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'request_xml' => null ?? null,
                    'response_xml' => null ?? null,
                    'zki' => null,
                    'jir' => null,
                    'error_message' => $e->getMessage(),
                ]
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    function objectToArray($data)
    {
        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        } elseif (is_object($data)) {
            return $this->objectToArray(get_object_vars($data));
        } else {
            return $data;
        }
    }
    public function testCertificate(string $certificatePath, string $password): array
    {
        try {
            if (!file_exists($certificatePath)) {
                return ['success' => false, 'error' => 'Certifikat ne postoji: ' . $certificatePath];
            }

            $certData = file_get_contents($certificatePath);
            if (!$certData) {
                return ['success' => false, 'error' => 'Nije moguće učitati certifikat'];
            }

            $certs = [];
            if (!openssl_pkcs12_read($certData, $certs, $password)) {
                return ['success' => false, 'error' => 'Neispravna lozinka ili oštećen certifikat'];
            }

            $info = openssl_x509_parse($certs['cert']);
            return [
                'success' => true,
                'details' => [
                    'subject' => $info['subject'] ?? [],
                    'issuer' => $info['issuer'] ?? [],
                    'valid_from' => date('Y-m-d H:i:s', $info['validFrom_time_t'] ?? 0),
                    'valid_to' => date('Y-m-d H:i:s', $info['validTo_time_t'] ?? 0),
                    'serial_number' => $info['serialNumber'] ?? null,
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function testOrganisationCertificate(Organisation $organisation): array
    {
        $path = storage_path('private/' . $organisation->certificate);
        $password = $organisation->certificate_password;

        return $this->testCertificate($path, $password);
    }

    private function generateQrCode(string $jir, string $datVrijeme, float $total): string
    {
        $qrGenerator = new QRGenerator($jir, $datVrijeme, $total);
        return $qrGenerator->getQrCode();
    }

    private function validateFiscalizationData(mixed $organisation, mixed $user, mixed $branch): ?string
    {
        if ($organisation->certificate_path == null || $organisation->certificate_password == null) {
            return "Certifikat nije konfiguriran";
        }

        if ($organisation->oib == null) {
            return "OIB organizacije nije konfiguriran";
        }

        if ($organisation->sequence_mark == null) {
            return "Nivo fiskalizacije nije konfiguriran";
        }

        if ($user->oib == null) {
            return "OIB korisnika nije konfiguriran";
        }

        if ($branch->branch_mark == null) {
            return "Poslovni prostor nije konfiguriran";
        }

        return null;
    }
}
