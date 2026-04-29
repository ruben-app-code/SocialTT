<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\LiveAnnouncement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\LiveAnnouncementController
 */
final class LiveAnnouncementControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $liveAnnouncements = LiveAnnouncement::factory()->count(3)->create();

        $response = $this->get(route('live-announcements.index'));

        $response->assertOk();
        $response->assertViewIs('liveAnnouncement.index');
        $response->assertViewHas('liveAnnouncements', $liveAnnouncements);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('live-announcements.create'));

        $response->assertOk();
        $response->assertViewIs('liveAnnouncement.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\LiveAnnouncementController::class,
            'store',
            \App\Http\Requests\LiveAnnouncementStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $title = fake()->sentence(4);
        $scheduled_at = Carbon::parse(fake()->dateTime());

        $response = $this->post(route('live-announcements.store'), [
            'user_id' => $user->id,
            'title' => $title,
            'scheduled_at' => $scheduled_at->toDateTimeString(),
        ]);

        $liveAnnouncements = LiveAnnouncement::query()
            ->where('user_id', $user->id)
            ->where('title', $title)
            ->where('scheduled_at', $scheduled_at)
            ->get();
        $this->assertCount(1, $liveAnnouncements);
        $liveAnnouncement = $liveAnnouncements->first();

        $response->assertRedirect(route('liveAnnouncements.index'));
        $response->assertSessionHas('liveAnnouncement.id', $liveAnnouncement->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $liveAnnouncement = LiveAnnouncement::factory()->create();

        $response = $this->get(route('live-announcements.show', $liveAnnouncement));

        $response->assertOk();
        $response->assertViewIs('liveAnnouncement.show');
        $response->assertViewHas('liveAnnouncement', $liveAnnouncement);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $liveAnnouncement = LiveAnnouncement::factory()->create();

        $response = $this->get(route('live-announcements.edit', $liveAnnouncement));

        $response->assertOk();
        $response->assertViewIs('liveAnnouncement.edit');
        $response->assertViewHas('liveAnnouncement', $liveAnnouncement);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\LiveAnnouncementController::class,
            'update',
            \App\Http\Requests\LiveAnnouncementUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $liveAnnouncement = LiveAnnouncement::factory()->create();
        $user = User::factory()->create();
        $title = fake()->sentence(4);
        $scheduled_at = Carbon::parse(fake()->dateTime());

        $response = $this->put(route('live-announcements.update', $liveAnnouncement), [
            'user_id' => $user->id,
            'title' => $title,
            'scheduled_at' => $scheduled_at->toDateTimeString(),
        ]);

        $liveAnnouncement->refresh();

        $response->assertRedirect(route('liveAnnouncements.index'));
        $response->assertSessionHas('liveAnnouncement.id', $liveAnnouncement->id);

        $this->assertEquals($user->id, $liveAnnouncement->user_id);
        $this->assertEquals($title, $liveAnnouncement->title);
        $this->assertEquals($scheduled_at->timestamp, $liveAnnouncement->scheduled_at);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $liveAnnouncement = LiveAnnouncement::factory()->create();

        $response = $this->delete(route('live-announcements.destroy', $liveAnnouncement));

        $response->assertRedirect(route('liveAnnouncements.index'));

        $this->assertModelMissing($liveAnnouncement);
    }
}
