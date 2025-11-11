<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\Branch;
use App\Models\Room;
use App\Models\User;
use App\Models\PriceList;
use App\Models\ServiceGroup;
use App\Models\Service;
use App\Models\Price;
use App\Models\Client;
use App\Models\Patient;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoOrganisationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1️⃣ Kreiraj organizaciju
            $organisation = Organisation::factory()
                ->demo()
                ->create();

            // 2️⃣ Kreiraj poslovnice (1–3)
            $branches = Branch::factory()
                ->count(rand(1, 3))
                ->for($organisation)
                ->create();

            // 3️⃣ Kreiraj sobe (3 po poslovnici)
            $branches->each(function (Branch $branch) use ($organisation) {
                Room::factory()
                    ->count(3)
                    ->for($organisation)
                    ->for($branch)
                    ->create();
            });

            // 4️⃣ Kreiraj korisnike (1 admin + 4 random)
            $adminBranch = $branches->random();

            $admin = User::factory()
                ->for($organisation)
                ->admin()
                ->create([
                    'email' => 'admin@vetlio.app',
                    'first_name' => 'Luka',
                    'last_name' => 'Cavić',
                    'name' => 'Luka Cavić',
                    'primary_branch_id' => $adminBranch->id, // ✅ dodano
                ]);

            $users = User::factory()
                ->count(4)
                ->for($organisation)
                ->make() // koristi make da možemo naknadno dodati primary_branch_id
                ->each(function ($user) use ($branches, $organisation) {
                    $branch = $branches->random();
                    $user->primary_branch_id = $branch->id;
                    $user->organisation_id = $organisation->id;
                    $user->save();

                    // ako postoji pivot veza branches()
                    if (method_exists($user, 'branches')) {
                        $assigned = $branches->random(rand(1, $branches->count()));
                        $user->branches()->attach($assigned->pluck('id'));
                    }
                });

            // poveži svakog korisnika s random poslovnicama i postavi primarnu
            $users->each(function (User $user) use ($branches) {
                $assignedBranches = $branches->random(rand(1, $branches->count()));
                $user->update([
                    'primary_branch_id' => $assignedBranches->first()->id,
                ]);
                // ako ima pivot tablicu za više lokacija
                if (method_exists($user, 'branches')) {
                    $user->branches()->attach($assignedBranches->pluck('id'));
                }
            });

            // 5️⃣ Kreiraj cjenike
            $priceLists = PriceList::factory()
                ->count(3)
                ->for($organisation)
                ->create();

            // Poveži cjenike s poslovnicama (svaka ima primarni)
            $branches->each(function (Branch $branch) use ($priceLists) {
                $linked = $priceLists->random(rand(1, 2));
                $branch->update(['price_list_id' => $linked->first()->id]);

                if (method_exists($branch, 'priceLists')) {
                    $branch->priceLists()->attach($linked->pluck('id'));
                }
            });

            // 6️⃣ Kreiraj grupe usluga i usluge
            $groups = ServiceGroup::factory()
                ->count(5)
                ->for($organisation)
                ->create();

            $services = collect();
            $groups->each(function (ServiceGroup $group) use ($organisation, &$services) {
                $created = Service::factory()
                    ->count(rand(3, 6))
                    ->for($organisation)
                    ->for($group)
                    ->create();

                $services = $services->merge($created);
            });

            // 7️⃣ Kreiraj cijene za svaku uslugu — svaka usluga MORA imati barem jednu cijenu
            $services->each(function (Service $service) use ($priceLists, $organisation) {
                // svaka usluga ima barem jednu cijenu u random cjeniku
                $primaryList = $priceLists->random();

                Price::factory()
                    ->for($organisation)
                    ->for($primaryList)
                    ->for($service, 'priceable')
                    ->create();

                // 30% šanse da dobije dodatnu cijenu u drugom cjeniku
                if (rand(1, 100) <= 30 && $priceLists->count() > 1) {
                    $secondaryList = $priceLists->where('id', '!=', $primaryList->id)->random();
                    Price::factory()
                        ->for($organisation)
                        ->for($secondaryList)
                        ->for($service, 'priceable')
                        ->create();
                }
            });

            // 8️⃣ Kreiraj klijente
            $clients = Client::factory()
                ->count(20)
                ->for($organisation)
                ->create();

            // 9️⃣ Kreiraj pacijente (1–3 po klijentu)
            $patients = collect();
            $clients->each(function (Client $client) use ($organisation, &$patients) {
                $created = Patient::factory()
                    ->count(rand(1, 3))
                    ->for($organisation)
                    ->for($client)
                    ->create();
                $patients = $patients->merge($created);
            });

            // 🔟 Kreiraj rezervacije (15–30)
            $allRooms = Room::whereIn('branch_id', $branches->pluck('id'))->get();
            $vets = $users->where('service_provider', true);

            Reservation::factory()
                ->count(rand(15, 30))
                ->for($organisation)
                ->state(function () use ($clients, $patients, $branches, $allRooms, $vets, $services) {
                    $branch = $branches->random();
                    $room = $allRooms->where('branch_id', $branch->id)->random();
                    $client = $clients->random();
                    $patient = $client->patients()->inRandomOrder()->first() ?? $patients->random();
                    $vet = $vets->random();

                    return [
                        'branch_id' => $branch->id,
                        'room_id' => $room->id,
                        'client_id' => $client->id,
                        'patient_id' => $patient->id,
                        'service_id' => $services->random()->id,
                        'user_id' => $vet->id,
                        'service_provider_id' => $vet->id,
                    ];
                })
                ->create();

            $this->command->info("✅ Demo organisation '{$organisation->name}' seeded successfully!");
        });
    }
}
