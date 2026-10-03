<?php

namespace Tests\Feature;

use App\Models\Kilometer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KilometerCopyCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_modal_can_create_a_new_kilometer_with_the_current_record_details(): void
    {
        Carbon::setTestNow('2026-10-03 15:00:00');
        $user = User::factory()->create(['role' => 'admin']);
        $kilometer = Kilometer::create([
            'owner_user_id' => $user->id,
            'date' => '2026-09-15',
            'address' => '12 Đường Thử Nghiệm',
            'ward' => 'Phường 1',
            'district' => 'Quận 1',
            'distance_km' => 12.5,
            'carrier_fee' => 35000,
            'motorbike_driver' => 'Tài xế thử nghiệm',
            'package_note' => 'Giao tại cổng',
        ]);

        $response = $this->actingAs($user)->get(route('kilometers.index'));

        $response
            ->assertOk()
            ->assertSee('data-kilometer-create-copy', false)
            ->assertSee('data-create-action="' . route('kilometers.store') . '"', false)
            ->assertSee('data-system-date="2026-10-03"', false)
            ->assertSee('kilometer-directory-table-admin', false);
        $this->assertSame(1, substr_count((string) $response->getContent(), 'data-kilometer-create-copy'));

        $this->actingAs($user)->post(route('kilometers.store'), [
            'date' => '2026-10-03',
            'address' => $kilometer->address,
            'ward' => $kilometer->ward,
            'district' => $kilometer->district,
            'distance_km' => $kilometer->distance_km,
            'carrier_fee' => $kilometer->carrier_fee,
            'motorbike_driver' => $kilometer->motorbike_driver,
            'package_note' => $kilometer->package_note,
        ])->assertRedirect(route('kilometers.index'));

        $this->assertDatabaseHas('kilometers', [
            'id' => $kilometer->id,
            'date' => '2026-09-15 00:00:00',
            'address' => $kilometer->address,
        ]);
        $this->assertDatabaseHas('kilometers', [
            'date' => '2026-10-03 00:00:00',
            'address' => $kilometer->address,
            'ward' => $kilometer->ward,
            'district' => $kilometer->district,
            'distance_km' => $kilometer->distance_km,
            'carrier_fee' => $kilometer->carrier_fee,
            'motorbike_driver' => $kilometer->motorbike_driver,
            'package_note' => $kilometer->package_note,
        ]);
        $this->assertDatabaseCount('kilometers', 2);
        Carbon::setTestNow();
    }
}
