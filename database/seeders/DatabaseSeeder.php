<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Extra staff accounts on top of the admin RoleSeeder creates
        $pharmacist = User::firstOrCreate(
            ['email' => 'pharmacist@pharmacera.test'],
            ['name' => 'Amina Belkacem', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $pharmacist->assignRole('pharmacist');

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@pharmacera.test'],
            ['name' => 'Yacine Kaci', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $cashier->assignRole('cashier');

        // Categories
        $categories = collect(['Antibiotics', 'Painkillers', 'Vitamins', 'Cold & Flu', 'Digestive', 'Dermatology'])
            ->map(fn ($name) => Category::firstOrCreate(['name' => $name]));

        // Suppliers
        $suppliers = collect([
            ['name' => 'Biopharm Dz', 'phone' => '0555123456', 'wilaya' => 'Algiers'],
            ['name' => 'Saidal Group', 'phone' => '0661234567', 'wilaya' => 'Oran'],
            ['name' => 'Pharma Distrib', 'phone' => '0770987654', 'wilaya' => 'Constantine'],
        ])->map(fn ($s) => Supplier::firstOrCreate(['name' => $s['name']], $s));

        // Medicines
        $medicineNames = [
            ['name' => 'Amoxicilline 500mg', 'generic' => 'Amoxicillin', 'category' => 'Antibiotics', 'price' => 350],
            ['name' => 'Doliprane 1000mg', 'generic' => 'Paracetamol', 'category' => 'Painkillers', 'price' => 180],
            ['name' => 'Ibuprofene 400mg', 'generic' => 'Ibuprofen', 'category' => 'Painkillers', 'price' => 220],
            ['name' => 'Vitamine C 500mg', 'generic' => 'Ascorbic Acid', 'category' => 'Vitamins', 'price' => 450],
            ['name' => 'Rhinathiol Sirop', 'generic' => 'Carbocisteine', 'category' => 'Cold & Flu', 'price' => 590],
            ['name' => 'Smecta Sachets', 'generic' => 'Diosmectite', 'category' => 'Digestive', 'price' => 610],
            ['name' => 'Eryfluid Gel', 'generic' => 'Erythromycin', 'category' => 'Dermatology', 'price' => 780],
            ['name' => 'Augmentin 1g', 'generic' => 'Amox/Clav', 'category' => 'Antibiotics', 'price' => 920],
            ['name' => 'Aspegic 1000mg', 'generic' => 'Aspirin', 'category' => 'Painkillers', 'price' => 260],
            ['name' => 'Zovirax Creme', 'generic' => 'Acyclovir', 'category' => 'Dermatology', 'price' => 540],
        ];

        $medicines = collect($medicineNames)->map(function ($m) use ($categories) {
            return Medicine::firstOrCreate(
                ['name' => $m['name']],
                [
                    'category_id'      => $categories->firstWhere('name', $m['category'])->id,
                    'generic_name'     => $m['generic'],
                    'barcode'          => (string) fake()->unique()->ean13(),
                    'unit'             => 'box',
                    'selling_price'    => $m['price'],
                    'purchase_price'   => round($m['price'] * 0.7, 2),
                    'alert_threshold'  => 15,
                ]
            );
        });

        // Batches — a mix of safe, expiring soon, and expired stock
        $medicines->each(function (Medicine $medicine) use ($suppliers) {
            $profiles = [
                ['qty' => 60, 'days' => 400],  // safe
                ['qty' => 30, 'days' => 45],   // expiring soon
                ['qty' => 10, 'days' => -5],   // already expired
            ];

            foreach ($profiles as $p) {
                Batch::create([
                    'medicine_id'         => $medicine->id,
                    'supplier_id'         => $suppliers->random()->id,
                    'quantity'            => $p['qty'],
                    'remaining_quantity'  => $p['qty'],
                    'purchase_price'      => $medicine->purchase_price,
                    'expiry_date'         => now()->addDays($p['days']),
                ]);
            }
        });

        // Sales — last 14 days, random medicines, deducting real stock
        $users = collect([$pharmacist, $cashier]);

        for ($i = 0; $i < 40; $i++) {
            $sale = Sale::create([
                'user_id'        => $users->random()->id,
                'total'          => 0,
                'payment_method' => fake()->randomElement(['cash', 'card', 'insurance']),
                'created_at'     => now()->subDays(rand(0, 14))->subHours(rand(0, 12)),
            ]);

            $total = 0;
            $itemCount = rand(1, 3);

            foreach ($medicines->random($itemCount) as $medicine) {
                $batch = $medicine->batches()
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('expiry_date')
                    ->first();

                if (! $batch) continue;

                $qty = min(rand(1, 3), $batch->remaining_quantity);
                $subtotal = $qty * $medicine->selling_price;

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'batch_id'    => $batch->id,
                    'medicine_id' => $medicine->id,
                    'quantity'    => $qty,
                    'unit_price'  => $medicine->selling_price,
                    'subtotal'    => $subtotal,
                ]);

                $batch->decrement('remaining_quantity', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => $total]);
        }
    }
}