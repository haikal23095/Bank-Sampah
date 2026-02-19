<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // --- 1. WASTE CATEGORIES ---
        $categories = [
            ['id' => 1, 'name' => 'Plastik', 'description' => 'Botol PET, PP, HD, gelas plastik, dll.', 'created_at' => $now],
            ['id' => 2, 'name' => 'Kertas', 'description' => 'Kardus, kertas kantor, koran, majalah.', 'created_at' => $now],
            ['id' => 3, 'name' => 'Logam', 'description' => 'Besi, aluminium, kuningan, tembaga.', 'created_at' => $now],
            ['id' => 4, 'name' => 'Kaca', 'description' => 'Botol kaca utuh atau beling.', 'created_at' => $now],
            ['id' => 5, 'name' => 'Elektronik', 'description' => 'Hp rusak, kabel, komponen PC.', 'created_at' => $now],
        ];
        DB::table('waste_categories')->insert($categories);

        // --- 2. WASTE TYPES ---
        $wasteTypes = [
            ['id' => 1, 'category_id' => 1, 'name' => 'Botol PET Bersih', 'price_per_kg' => 4500, 'unit' => 'kg'],
            ['id' => 2, 'category_id' => 1, 'name' => 'Gelas Plastik PP', 'price_per_kg' => 2500, 'unit' => 'kg'],
            ['id' => 3, 'category_id' => 1, 'name' => 'Plastik Campuran', 'price_per_kg' => 1000, 'unit' => 'kg'],
            ['id' => 4, 'category_id' => 2, 'name' => 'Kardus Double Wall', 'price_per_kg' => 2200, 'unit' => 'kg'],
            ['id' => 5, 'category_id' => 2, 'name' => 'Kertas Putih HVS', 'price_per_kg' => 3000, 'unit' => 'kg'],
            ['id' => 6, 'category_id' => 3, 'name' => 'Kaleng Softdrink (Alu)', 'price_per_kg' => 12000, 'unit' => 'kg'],
            ['id' => 7, 'category_id' => 3, 'name' => 'Besi Tua', 'price_per_kg' => 4000, 'unit' => 'kg'],
            ['id' => 8, 'category_id' => 4, 'name' => 'Botol Sirup/Kecap', 'price_per_kg' => 500, 'unit' => 'kg'],
        ];
        foreach ($wasteTypes as &$wt) {
            $wt['created_at'] = $now;
            $wt['updated_at'] = $now;
        }
        DB::table('waste_types')->insert($wasteTypes);

        // --- 3. USERS (Admin, Staff, & Multiple Customers) ---
        $users = [
            ['id' => 1, 'name' => 'Super Admin', 'email' => 'admin@mail.com', 'role' => 'ADMIN'],
            ['id' => 2, 'name' => 'Siti Aminah (PETUGAS)', 'email' => 'siti@mail.com', 'role' => 'PETUGAS'],
            ['id' => 3, 'name' => 'Eko Prasetyo (PETUGAS)', 'email' => 'eko@mail.com', 'role' => 'PETUGAS'],
            ['id' => 4, 'name' => 'Budi Santoso', 'email' => 'budi@mail.com', 'role' => 'NASABAH'],
            ['id' => 5, 'name' => 'Ani Wijaya', 'email' => 'ani@mail.com', 'role' => 'NASABAH'],
            ['id' => 6, 'name' => 'Iwan Fals', 'email' => 'iwan@mail.com', 'role' => 'NASABAH'],
            ['id' => 7, 'name' => 'Siska Putri', 'email' => 'siska@mail.com', 'role' => 'NASABAH'],
        ];
        foreach ($users as &$u) {
            $u['password'] = Hash::make('password');
            $u['phone'] = '0812'.rand(10000000, 99999999);
            $u['address'] = 'Kecamatan Sukamaju No. '.rand(1, 100);
            $u['bank_name'] = 'BCA';
            $u['account_number'] = rand(100000000, 999999999);
            $u['join_date'] = $now->subMonths(2);
            $u['created_at'] = $now;
        }
        DB::table(' users')->insert($users);

        // --- 4. WALLETS ---
        $wallets = [
            ['user_id' => 4, 'balance' => 55000],
            ['user_id' => 5, 'balance' => 120000],
            ['user_id' => 6, 'balance' => 0],
            ['user_id' => 7, 'balance' => 15000],
        ];
        foreach ($wallets as &$w) {
            $w['last_updated'] = $now;
            $w['created_at'] = $now;
        }
        DB::table('wallets')->insert($wallets);

        // --- 5. TRANSACTIONS & DETAILS (Volume Data Lebih Besar) ---
        $transactionData = [
            // Transaksi 1: Budi Santoso (user_id 4)
            [
                'id' => 1, 'user_id' => 4, 'staff_id' => 2, 'date' => $now->subDays(15), 'note' => 'Setoran awal rutin',
                'details' => [
                    ['waste_type_id' => 1, 'weight' => 5, 'subtotal' => 22500], // Botol PET
                    ['waste_type_id' => 4, 'weight' => 10, 'subtotal' => 22000], // Kardus
                ],
            ],
            // Transaksi 2: Ani Wijaya (user_id 5)
            [
                'id' => 2, 'user_id' => 5, 'staff_id' => 3, 'date' => $now->subDays(12), 'note' => 'Pembersihan kantor',
                'details' => [
                    ['waste_type_id' => 5, 'weight' => 20, 'subtotal' => 60000], // Kertas HVS
                    ['waste_type_id' => 6, 'weight' => 5, 'subtotal' => 60000],  // Kaleng Alu
                ],
            ],
            // Transaksi 3: Iwan Fals (user_id 6)
            [
                'id' => 3, 'user_id' => 6, 'staff_id' => 2, 'date' => $now->subDays(8), 'note' => 'Setoran warga RT 01',
                'details' => [
                    ['waste_type_id' => 2, 'weight' => 15, 'subtotal' => 37500], // Gelas PP
                    ['waste_type_id' => 7, 'weight' => 50, 'subtotal' => 200000], // Besi Tua
                ],
            ],
            // Transaksi 4: Siska Putri (user_id 7)
            [
                'id' => 4, 'user_id' => 7, 'staff_id' => 3, 'date' => $now->subDays(5), 'note' => 'Sampah rumah tangga',
                'details' => [
                    ['waste_type_id' => 1, 'weight' => 2, 'subtotal' => 9000],   // Botol PET
                    ['waste_type_id' => 8, 'weight' => 12, 'subtotal' => 6000],  // Botol Kaca
                ],
            ],
            // Transaksi 5: Budi Santoso (Setoran Kedua)
            [
                'id' => 5, 'user_id' => 4, 'staff_id' => 2, 'date' => $now->subDays(2), 'note' => 'Tambahan kardus pindahan',
                'details' => [
                    ['waste_type_id' => 4, 'weight' => 15, 'subtotal' => 33000], // Kardus
                ],
            ],
            // Transaksi 6: Ani Wijaya (Setoran Kedua)
            [
                'id' => 6, 'user_id' => 5, 'staff_id' => 3, 'date' => $now->subHours(5), 'note' => 'Setoran harian',
                'details' => [
                    ['waste_type_id' => 1, 'weight' => 3, 'subtotal' => 13500],  // Botol PET
                    ['waste_type_id' => 2, 'weight' => 4, 'subtotal' => 10000],  // Gelas PP
                ],
            ],
        ];

        foreach ($transactionData as $t) {
            // Insert Header Transaksi
            DB::table('transactions')->insert([
                'id' => $t['id'],
                'user_id' => $t['user_id'],
                'staff_id' => $t['staff_id'],
                'date' => $t['date'],
                'admin_note' => $t['note'],
                'created_at' => $t['date'],
                'updated_at' => $t['date'],
            ]);

            // Insert Detail Transaksi
            foreach ($t['details'] as $detail) {
                DB::table('transaction_details')->insert([
                    'transaction_id' => $t['id'],
                    'waste_type_id' => $detail['waste_type_id'],
                    'weight' => $detail['weight'],
                    'subtotal' => $detail['subtotal'],
                    'created_at' => $t['date'],
                    'updated_at' => $t['date'],
                ]);
            }
        }

        // --- 6. WITHDRAWALS (Lebih Banyak & Sinkron dengan Wallet) ---
        $withdrawals = [
            // User 4: Budi Santoso (Balance awal di wallet: 55,000)
            [
                'user_id' => 4, 'staff_id' => 2, 'date' => $now->subDays(5),
                'amount' => 15000, 'status' => 'SUCCESS', 'method' => 'cash',
                'admin_note' => 'Penarikan tunai sukses', 'created_at' => $now->subDays(5),
            ],
            [
                'user_id' => 4, 'staff_id' => 2, 'date' => $now->subDays(3),
                'amount' => 100000, 'status' => 'FAILED', 'method' => 'transfer',
                'admin_note' => 'Saldo tidak mencukupi', 'created_at' => $now->subDays(3),
            ],
            [
                'user_id' => 4, 'staff_id' => 2, 'date' => $now->subDays(1),
                'amount' => 5000, 'status' => 'PENDING', 'method' => 'cash',
                'admin_note' => 'Proses verifikasi fisik', 'created_at' => $now->subDays(1),
            ],

            // User 5: Ani Wijaya (Balance awal di wallet: 120,000)
            [
                'user_id' => 5, 'staff_id' => 3, 'date' => $now->subDays(4),
                'amount' => 50000, 'status' => 'SUCCESS', 'method' => 'transfer',
                'admin_note' => 'Transfer via BCA', 'created_at' => $now->subDays(4),
            ],
            [
                'user_id' => 5, 'staff_id' => 3, 'date' => $now->subDays(2),
                'amount' => 20000, 'status' => 'SUCCESS', 'method' => 'cash',
                'admin_note' => 'Penarikan di kantor unit', 'created_at' => $now->subDays(2),
            ],
            [
                'user_id' => 5, 'staff_id' => 3, 'date' => $now->subMinutes(45),
                'amount' => 10000, 'status' => 'PENDING', 'method' => 'transfer',
                'admin_note' => 'Menunggu persetujuan admin', 'created_at' => $now,
            ],

            // User 7: Siska Putri (Balance awal di wallet: 15,000)
            [
                'user_id' => 7, 'staff_id' => 2, 'date' => $now->subDays(7),
                'amount' => 10000, 'status' => 'SUCCESS', 'method' => 'cash',
                'admin_note' => 'Penarikan rutin', 'created_at' => $now->subDays(7),
            ],
            [
                'user_id' => 7, 'staff_id' => 2, 'date' => $now->subHours(2),
                'amount' => 50000, 'status' => 'FAILED', 'method' => 'transfer',
                'admin_note' => 'Limit harian terlampaui', 'created_at' => $now,
            ],
        ];

        // Tambahkan updated_at otomatis
        foreach ($withdrawals as &$wd) {
            $wd['updated_at'] = $now;
        }

        DB::table('withdrawals')->insert($withdrawals);
    }
}
