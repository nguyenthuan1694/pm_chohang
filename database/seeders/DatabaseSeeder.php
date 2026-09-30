<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kilometer;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->raw([
                'name' => 'Test User',
            ]),
        );

        $kilometers = [
            ['date' => '2026-09-23', 'address' => '12 Nguyễn Huệ', 'ward' => 'Bến Nghé', 'district' => 'Quận 1', 'distance_km' => 3.50, 'carrier_fee' => 25000, 'motorbike_driver' => 'Nguyễn Văn An', 'package_note' => 'Giao trong ngày'],
            ['date' => '2026-09-22', 'address' => '86 Phan Văn Trị', 'ward' => '10', 'district' => 'Gò Vấp', 'distance_km' => 8.20, 'carrier_fee' => 45000, 'motorbike_driver' => 'Trần Minh Khoa', 'package_note' => 'Đã gọi trước khi giao'],
            ['date' => '2026-09-21', 'address' => '210 Lê Văn Sỹ', 'ward' => '14', 'district' => 'Quận 3', 'distance_km' => 5.75, 'carrier_fee' => 35000, 'motorbike_driver' => 'Lê Hoàng Nam', 'package_note' => ''],
        ];

        foreach ($kilometers as $kilometer) {
            Kilometer::firstOrCreate(
                ['date' => $kilometer['date'], 'address' => $kilometer['address']],
                $kilometer,
            );
        }

        $employees = [
            ['name' => 'Nguyễn Văn An', 'started_at' => '2025-01-15', 'address' => 'Quận 1, TP. Hồ Chí Minh'],
            ['name' => 'Trần Minh Khoa', 'started_at' => '2025-03-02', 'address' => 'Gò Vấp, TP. Hồ Chí Minh'],
            ['name' => 'Lê Hoàng Nam', 'started_at' => '2025-06-10', 'address' => 'Quận 3, TP. Hồ Chí Minh'],
        ];

        foreach ($employees as $employee) {
            Employee::firstOrCreate(
                ['name' => $employee['name']],
                $employee,
            );
        }
    }
}
