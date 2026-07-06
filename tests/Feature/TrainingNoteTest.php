<?php

namespace Tests\Feature;

use App\Models\PersonalRecord;
use App\Models\RunningLog;
use App\Models\User;
use App\Models\UsersDetail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TrainingNoteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_training_notes_index_requires_auth(): void
    {
        $this->get(route('training-notes.index'))->assertRedirect(route('login'));
    }

    public function test_training_notes_index_ok_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('training-notes.index'))->assertOk();
    }

    public function test_cannot_access_other_users_log(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $log = RunningLog::create([
            'user_id'          => $owner->id,
            'run_date'         => now()->toDateString(),
            'distance_km'      => 5,
            'duration_seconds' => 1500,
            'is_confirmed'     => true,
        ]);

        $this->actingAs($other)
            ->get(route('training-notes.logs.show', $log))
            ->assertForbidden();
    }

    public function test_save_note_updates_user_note(): void
    {
        $user = User::factory()->create();
        $log = RunningLog::create([
            'user_id'          => $user->id,
            'run_date'         => now()->toDateString(),
            'distance_km'      => 10,
            'duration_seconds' => 3000,
            'is_confirmed'     => true,
        ]);

        $this->actingAs($user)
            ->post(route('training-notes.logs.note', $log), ['user_note' => '컨디션 좋음'])
            ->assertRedirect(route('training-notes.logs.show', $log));

        $this->assertSame('컨디션 좋음', $log->fresh()->user_note);
    }

    public function test_body_page_blocked_without_consent(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('training-notes.body'));
        $response->assertOk();
        $response->assertSee('민감정보 수집 동의', false);
    }

    public function test_body_consent_saves_timestamp(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('training-notes.body.consent'), ['consent' => '1'])
            ->assertRedirect(route('training-notes.body'));

        $detail = UsersDetail::where('user_id', $user->id)->first();
        $this->assertNotNull($detail?->body_data_consent_at);
    }

    public function test_personal_record_store_and_forbidden_delete(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->post(route('training-notes.records.store'), [
                'distance_type' => '5K',
                'record_time'   => '22:30',
                'achieved_at'   => now()->toDateString(),
            ])
            ->assertRedirect(route('training-notes.records'));

        $record = PersonalRecord::where('user_id', $user->id)->first();
        $this->assertNotNull($record);

        $this->actingAs($other)
            ->delete(route('training-notes.records.destroy', $record))
            ->assertForbidden();
    }
}
