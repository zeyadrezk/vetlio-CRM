<?php

namespace App\Services;

use Illuminate\Support\Collection;

class InvoiceCalculator
{
    public static function calculateItemTotals(
        float  $price,
        float  $priceWithTax,
        float  $taxRate,
        float  $quantity = 1,
        ?float $discount = 0
    ): array
    {
        $discount = $discount ?? 0;

        // Ako je cijena 0, izračunaj iz priceWithTax
        if ($price == 0 && $priceWithTax > 0) {
            $price = $priceWithTax / (1 + $taxRate / 100);
        }

        // Popust po jedinici
        $discountAmountPerUnit = $price * ($discount / 100);
        $priceAfterDiscount = $price - $discountAmountPerUnit;

        // Cijena s PDV-om nakon popusta
        $priceWithTaxAfterDiscount = $priceAfterDiscount * (1 + $taxRate / 100);

        // Ukupne vrijednosti
        $baseTotal = $priceAfterDiscount * $quantity;
        $taxAmount = $baseTotal * ($taxRate / 100);
        $totalWithTax = $baseTotal + $taxAmount;

        // Ukupni popust (bez PDV-a)
        $discountTotal = $discountAmountPerUnit * $quantity;

        return [
            'base_price' => round($price, 2),
            'base_price_with_tax' => round($priceWithTax, 2),
            'tax_rate' => $taxRate,
            'quantity' => $quantity,
            'discount_percent' => $discount,
            'discount_amount' => round($discountTotal, 2),
            'base_total' => round($baseTotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_with_tax' => round($totalWithTax, 2),
            'unit_discounted_price' => round($priceAfterDiscount, 2),
            'unit_discounted_price_with_tax' => round($priceWithTaxAfterDiscount, 2),
        ];
    }


    public static function calculateInvoiceTotals(Collection|array $items, ?float $globalDiscountPercent = 0): array
    {
        $globalDiscountPercent = $globalDiscountPercent ?? 0;

        $baseTotal = 0.0;
        $taxTotal = 0.0;
        $discountTotal = 0.0;
        $totalWithTax = 0.0;

        foreach ($items as $item) {
            // očekujemo da svaka stavka već ima:
            // 'base_total', 'tax_amount', 'discount_amount', 'total_with_tax'
            $baseTotal += $item['base_total'] ?? 0;
            $taxTotal += $item['tax_amount'] ?? 0;
            $discountTotal += $item['discount_amount'] ?? 0;
            $totalWithTax += $item['total_with_tax'] ?? 0;
        }

        // primjena globalnog popusta
        if ($globalDiscountPercent > 0) {
            $globalDiscountAmount = $baseTotal * ($globalDiscountPercent / 100);
            $baseTotal -= $globalDiscountAmount;

            // proporcionalno smanji porez
            $taxTotal = $taxTotal * ((100 - $globalDiscountPercent) / 100);

            $discountTotal += $globalDiscountAmount;

            // ukupno za naplatu
            $totalWithTax = $baseTotal + $taxTotal;
        }

        return [
            'base_total' => round($baseTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'discount_total' => round($discountTotal, 2),
            'total_with_tax' => round($totalWithTax, 2),
            'global_discount_percent' => $globalDiscountPercent,
        ];
    }
}
