<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\ScheduleController
 */
final class ScheduleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $schedules = Schedule::factory()->count(3)->create();

        $response = $this->get(route('schedules.index'));

        $response->assertOk();
        $response->assertViewIs('schedule.index');
        $response->assertViewHas('schedules', $schedules);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('schedules.create'));

        $response->assertOk();
        $response->assertViewIs('schedule.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\ScheduleController::class,
            'store',
            \App\Http\Requests\ScheduleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $days = fake()->;
        $time = fake()->time();

        $response = $this->post(route('schedules.store'), [
            'user_id' => $user->id,
            'days' => $days,
            'time' => $time,
        ]);

        $schedules = Schedule::query()
            ->where('user_id', $user->id)
            ->where('days', $days)
            ->where('time', $time)
            ->get();
        $this->assertCount(1, $schedules);
        $schedule = $schedules->first();

        $response->assertRedirect(route('schedules.index'));
        $response->assertSessionHas('schedule.id', $schedule->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->get(route('schedules.show', $schedule));

        $response->assertOk();
        $response->assertViewIs('schedule.show');
        $response->assertViewHas('schedule', $schedule);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->get(route('schedules.edit', $schedule));

        $response->assertOk();
        $response->assertViewIs('schedule.edit');
        $response->assertViewHas('schedule', $schedule);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\ScheduleController::class,
            'update',
            \App\Http\Requests\ScheduleUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $schedule = Schedule::factory()->create();
        $user = User::factory()->create();
        $days = fake()->;
        $time = fake()->time();

        $response = $this->put(route('schedules.update', $schedule), [
            'user_id' => $user->id,
            'days' => $days,
            'time' => $time,
        ]);

        $schedule->refresh();

        $response->assertRedirect(route('schedules.index'));
        $response->assertSessionHas('schedule.id', $schedule->id);

        $this->assertEquals($user->id, $schedule->user_id);
        $this->assertEquals($days, $schedule->days);
        $this->assertEquals($time, $schedule->time);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $schedule = Schedule::factory()->create();

        $response = $this->delete(route('schedules.destroy', $schedule));

        $response->assertRedirect(route('schedules.index'));

        $this->assertModelMissing($schedule);
    }
}
