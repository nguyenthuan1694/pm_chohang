<?php

namespace Tests\Feature;

use App\Models\CargoDelivery;
use App\Models\Employee;
use App\Models\Group;
use App\Models\Kilometer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CargoDeliveryOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_cargo_delivery_is_appended_to_the_bottom_of_the_list(): void
    {
        Carbon::setTestNow('2026-10-02 10:00:00');
        [$user, $employee, $kilometer, $group] = $this->createDeliveryContext();
        $existingDelivery = $this->createDelivery($employee, $kilometer, $group, 'Phiếu cũ');

        Carbon::setTestNow('2026-10-03 16:00:00');
        $response = $this->actingAs($user)->postJson(route('cargo-deliveries.store'), [
            'group_id' => $group->id,
            'employee_id' => $employee->id,
            'delivery_date' => '2026-10-03',
            'trip_count' => 1,
            'package_count' => '10',
            'weight_kg' => 25,
            'note' => '',
        ])->assertOk()->assertJsonPath('success', true);

        $newDeliveryId = $response->json('delivery.id');
        $listResponse = $this->get(route('cargo-deliveries.index'))->assertOk();
        $content = (string) $listResponse->getContent();

        $this->assertLessThan(
            strpos($content, 'id="delivery-row-' . $newDeliveryId . '"'),
            strpos($content, 'id="delivery-row-' . $existingDelivery->id . '"'),
        );
        $listResponse
            ->assertSee('rowToInsertBefore', false)
            ->assertSee('tbody.insertBefore(newRow, rowToInsertBefore || null)', false);
    }

    public function test_delivery_status_update_moves_the_record_to_the_bottom(): void
    {
        Carbon::setTestNow('2026-10-02 10:00:00');
        [$user, $employee, $kilometer, $group] = $this->createDeliveryContext();
        $deliveryToUpdate = $this->createDelivery($employee, $kilometer, $group, 'Phiếu cần cập nhật');

        Carbon::setTestNow('2026-10-02 10:01:00');
        $deliveryStayingAbove = $this->createDelivery($employee, $kilometer, $group, 'Phiếu khác');

        Carbon::setTestNow('2026-10-03 16:00:00');
        $this->actingAs($user)->patch(route('cargo-deliveries.status', $deliveryToUpdate), [
            'delivery_status' => 'delivered',
        ])->assertRedirect(route('cargo-deliveries.index'));

        $content = (string) $this->get(route('cargo-deliveries.index'))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($content, 'id="delivery-row-' . $deliveryToUpdate->id . '"'),
            strpos($content, 'id="delivery-row-' . $deliveryStayingAbove->id . '"'),
        );
        $this->assertDatabaseHas('cargo_deliveries', [
            'id' => $deliveryToUpdate->id,
            'delivery_status' => 'delivered',
        ]);
    }

    public function test_cargo_deliveries_are_ordered_by_trip_count_ascending(): void
    {
        Carbon::setTestNow('2026-10-02 10:00:00');
        [$user, $employee, $kilometer, $group] = $this->createDeliveryContext();
        $threeTrips = $this->createDelivery($employee, $kilometer, $group, 'Ba chuyến', 3);

        Carbon::setTestNow('2026-10-02 10:01:00');
        $oneTrip = $this->createDelivery($employee, $kilometer, $group, 'Một chuyến', 1);

        Carbon::setTestNow('2026-10-02 10:02:00');
        $twoTrips = $this->createDelivery($employee, $kilometer, $group, 'Hai chuyến', 2);

        $content = (string) $this->actingAs($user)->get(route('cargo-deliveries.index'))->assertOk()->getContent();
        $oneTripPosition = strpos($content, 'id="delivery-row-' . $oneTrip->id . '"');
        $twoTripsPosition = strpos($content, 'id="delivery-row-' . $twoTrips->id . '"');
        $threeTripsPosition = strpos($content, 'id="delivery-row-' . $threeTrips->id . '"');

        $this->assertNotFalse($oneTripPosition);
        $this->assertNotFalse($twoTripsPosition);
        $this->assertNotFalse($threeTripsPosition);
        $this->assertLessThan($twoTripsPosition, $oneTripPosition);
        $this->assertLessThan($threeTripsPosition, $twoTripsPosition);
    }

    private function createDeliveryContext(): array
    {
        $user = User::factory()->create(['role' => 'admin']);
        $kilometer = Kilometer::create([
            'owner_user_id' => $user->id,
            'date' => '2026-10-01',
            'address' => 'Địa chỉ thử nghiệm',
            'ward' => 'Phường 1',
            'district' => 'Quận 1',
            'distance_km' => 12.5,
            'carrier_fee' => 35000,
            'motorbike_driver' => 'Tài xế thử nghiệm',
            'package_note' => 'Ghi bao thử nghiệm',
        ]);
        $group = Group::create([
            'owner_user_id' => $user->id,
            'kilometer_id' => $kilometer->id,
            'group_date' => '2026-10-01 08:00:00',
            'invoice_name' => 'Phiếu thử nghiệm',
            'group_name' => 'Nhóm thử nghiệm',
            'address' => $kilometer->address,
            'ward' => $kilometer->ward,
            'district' => $kilometer->district,
            'distance_km' => $kilometer->distance_km,
            'carrier_fee' => $kilometer->carrier_fee,
            'motorbike_driver' => $kilometer->motorbike_driver,
            'package_note' => $kilometer->package_note,
            'status' => 'active',
        ]);
        $employee = Employee::create([
            'name' => 'Nhân viên thử nghiệm',
            'started_at' => '2026-01-01',
            'address' => 'Địa chỉ nhân viên',
        ]);

        return [$user, $employee, $kilometer, $group];
    }

    private function createDelivery(Employee $employee, Kilometer $kilometer, Group $group, string $invoiceName, int $tripCount = 1): CargoDelivery
    {
        return CargoDelivery::create([
            'group_id' => $group->id,
            'employee_id' => $employee->id,
            'kilometer_id' => $kilometer->id,
            'delivery_date' => now(),
            'invoice_name' => $invoiceName,
            'group_name' => $group->group_name,
            'address' => $kilometer->address,
            'ward' => $kilometer->ward,
            'district' => $kilometer->district,
            'trip_count' => $tripCount,
            'package_count' => '10',
            'weight_kg' => 25,
            'distance_km' => $kilometer->distance_km,
            'carrier_fee' => $kilometer->carrier_fee,
            'motorbike_driver' => $kilometer->motorbike_driver,
            'package_note' => $kilometer->package_note,
            'delivery_status' => 'pending',
        ]);
    }
}
