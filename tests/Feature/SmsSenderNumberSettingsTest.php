<?php

namespace Tests\Feature;

use App\Filament\Pages\SmsSenderNumberSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SmsSenderNumberSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_page_is_inaccessible_for_non_super_admin(): void
    {
        $user = User::factory()->create(['role' => 'region_admin']);

        $this->actingAs($user)
            ->get('/admin/sms-sender-number-settings')
            ->assertForbidden();
    }

    public function test_register_verify_and_delete_flow_with_core_mock(): void
    {
        config(['services.core_api.url' => 'http://core.test']);

        $user = User::factory()->create(['role' => 'super_admin']);

        Http::fake([
            'http://core.test/api/sms/senders?all=1' => Http::sequence()
                ->push(['senders' => []])
                ->push([
                    'senders' => [
                        ['phoneNumber' => '01012345678', 'status' => 'ACTIVE'],
                    ],
                ])
                ->push(['senders' => []]),
            'http://core.test/api/sms/senders/register' => Http::response([
                'ok'           => true,
                'phone_number' => '01012345678',
            ]),
            'http://core.test/api/sms/senders/verify' => Http::response([
                'ok'           => true,
                'phone_number' => '01012345678',
                'status'       => 'ACTIVE',
            ]),
            'http://core.test/api/sms/senders/01012345678' => Http::response([
                'ok'           => true,
                'phone_number' => '01012345678',
            ]),
        ]);

        Livewire::actingAs($user)
            ->test(SmsSenderNumberSettings::class)
            ->assertSet('senders', [])
            ->set('data.register_phone', '010-1234-5678')
            ->call('requestRegisterCode')
            ->assertSet('data.verify_phone', '01012345678')
            ->set('data.certification_code', '123456')
            ->call('verifyRegisterCode')
            ->assertSet('senders.0.phoneNumber', '01012345678')
            ->call('deleteSender', '01012345678')
            ->assertSet('senders', []);
    }
}
