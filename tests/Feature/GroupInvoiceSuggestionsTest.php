<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Kilometer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupInvoiceSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_form_renders_existing_invoice_names_and_delivery_details(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $kilometer = $this->createKilometer($user);
        $group = $this->createGroup($user, $kilometer, 'Phiếu đã lưu');

        $this->actingAs($user)->get(route('groups.index'))
            ->assertOk()
            ->assertSee('group-directory-table', false)
            ->assertSee('data-group-invoice-input', false)
            ->assertSee('value="' . $group->invoice_name . '"', false)
            ->assertSee('data-kilometer-id="' . $kilometer->id . '"', false)
            ->assertSee($kilometer->address)
            ->assertSee($kilometer->package_note)
            ->assertDontSee('<datalist', false)
            ->assertSee('autocomplete="off"', false)
            ->assertSee('optionsList.hidden = terms.length === 0', false);
    }

    public function test_group_kilometer_options_include_searchable_delivery_details(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $kilometer = $this->createKilometer($user);

        $this->actingAs($user)->get(route('groups.index'))
            ->assertOk()
            ->assertSee('quận Quận 1', false)
            ->assertSee('ghi bao Giao tại cổng', false)
            ->assertSee('12,50 km', false)
            ->assertSee('12 Đường Thử Nghiệm', false);
    }

    public function test_group_user_only_sees_invoice_suggestions_from_their_group(): void
    {
        $user = User::factory()->create([
            'name' => 'Nhóm hiện tại',
            'role' => 'group',
            'group_id' => 21,
            'permissions' => ['groups' => ['view' => true]],
        ]);
        $ownKilometer = $this->createKilometer($user);
        $this->createGroup($user, $ownKilometer, 'Phiếu cùng nhóm', 21);

        $otherUser = User::factory()->create(['role' => 'admin']);
        $otherKilometer = $this->createKilometer($otherUser);
        $this->createGroup($otherUser, $otherKilometer, 'Phiếu nhóm khác', 22);

        $this->actingAs($user)->get(route('groups.index'))
            ->assertOk()
            ->assertSee('Phiếu cùng nhóm')
            ->assertDontSee('Phiếu nhóm khác');
    }

    public function test_group_creation_uses_the_kilometer_selected_from_an_existing_invoice_suggestion(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $kilometer = $this->createKilometer($user);
        $this->createGroup($user, $kilometer, 'Tên xuất phiếu đã tồn tại');

        $this->actingAs($user)->post(route('groups.store'), [
            'invoice_name' => 'Tên xuất phiếu đã tồn tại',
            'kilometer_id' => $kilometer->id,
            'group_name' => 'Nhóm được chọn',
        ])->assertRedirect(route('groups.index'));

        $this->assertDatabaseHas('groups', [
            'invoice_name' => 'Tên xuất phiếu đã tồn tại',
            'kilometer_id' => $kilometer->id,
            'address' => $kilometer->address,
            'ward' => $kilometer->ward,
            'district' => $kilometer->district,
            'distance_km' => $kilometer->distance_km,
            'carrier_fee' => $kilometer->carrier_fee,
            'package_note' => $kilometer->package_note,
        ]);
    }

    public function test_cargo_delivery_modal_does_not_use_group_invoice_autocomplete(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get(route('cargo-deliveries.index'))
            ->assertOk()
            ->assertDontSee('data-group-invoice-input', false)
            ->assertDontSee('data-cargo-invoice-input', false);
    }

    public function test_group_activated_later_is_shown_at_the_bottom_of_the_cargo_delivery_list(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $kilometer = $this->createKilometer($user);
        $existingActiveGroup = $this->createGroup($user, $kilometer, 'Nhóm đã Active');
        $groupToActivate = $this->createGroup($user, $kilometer, 'Nhóm vừa Active');
        $groupToActivate->update(['status' => 'inactive']);

        $this->actingAs($user)->patch(route('groups.status', $groupToActivate), [
            'status' => 'active',
        ])->assertRedirect(route('groups.index'));

        $response = $this->get(route('cargo-deliveries.index'))->assertOk();
        $content = (string) $response->getContent();
        $existingActivePosition = strpos($content, 'value="' . $existingActiveGroup->id . '"');
        $newActivePosition = strpos($content, 'value="' . $groupToActivate->id . '"');

        $this->assertNotFalse($existingActivePosition);
        $this->assertNotFalse($newActivePosition);
        $this->assertLessThan($newActivePosition, $existingActivePosition);
    }

    private function createKilometer(User $user): Kilometer
    {
        return Kilometer::create([
            'owner_user_id' => $user->id,
            'date' => '2026-10-03',
            'address' => '12 Đường Thử Nghiệm',
            'ward' => 'Phường 1',
            'district' => 'Quận 1',
            'distance_km' => 12.5,
            'carrier_fee' => 35000,
            'motorbike_driver' => 'Tài xế thử nghiệm',
            'package_note' => 'Giao tại cổng',
        ]);
    }

    private function createGroup(User $user, Kilometer $kilometer, string $invoiceName, ?int $groupId = null): Group
    {
        return Group::create([
            'owner_user_id' => $user->id,
            'group_id' => $groupId,
            'kilometer_id' => $kilometer->id,
            'group_date' => '2026-10-03 08:00:00',
            'invoice_name' => $invoiceName,
            'group_name' => $groupId ? 'Nhóm ' . $groupId : 'Nhóm thử nghiệm',
            'address' => $kilometer->address,
            'ward' => $kilometer->ward,
            'district' => $kilometer->district,
            'distance_km' => $kilometer->distance_km,
            'carrier_fee' => $kilometer->carrier_fee,
            'motorbike_driver' => $kilometer->motorbike_driver,
            'package_note' => $kilometer->package_note,
            'status' => 'active',
        ]);
    }
}
