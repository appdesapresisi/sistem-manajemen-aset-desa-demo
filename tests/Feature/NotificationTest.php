<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $role = Role::firstOrCreate(['nama' => 'super_admin'], ['tugas' => 'SA', 'deskripsi' => 'Full']);

        $this->user = User::create([
            'nama' => 'Notif User', 'username' => 'notif_usr_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $role->id, 'is_aktif' => 1,
        ]);

        $this->otherUser = User::create([
            'nama' => 'Other User', 'username' => 'other_usr_' . uniqid(),
            'password' => bcrypt('pw'), 'role_id' => $role->id, 'is_aktif' => 1,
        ]);
    }

    private function createNotification(array $overrides = []): Notification
    {
        return Notification::create(array_merge([
            'user_id' => $this->user->id,
            'type' => 'inventaris_pending',
            'title' => 'Test Notification',
            'body' => 'Body notifikasi test',
            'is_read' => false,
        ], $overrides));
    }

    // ─── Index ───────────────────────────────────────────────

    public function test_user_can_view_notifications_index(): void
    {
        $this->createNotification();

        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertOk();
        $response->assertViewHas('notifications');
        $response->assertViewHas('grouped');
        $response->assertViewHas('unreadCount');
    }

    public function test_notification_index_shows_only_own_notifications(): void
    {
        $this->createNotification(['title' => 'My Notif']);
        Notification::create([
            'user_id' => $this->otherUser->id,
            'type' => 'inventaris_pending',
            'title' => 'Other Notif',
            'body' => 'Not mine',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertOk();
        $response->assertSee('My Notif');
        $response->assertDontSee('Other Notif');
    }

    // ─── Dropdown ────────────────────────────────────────────

    public function test_dropdown_returns_json_with_unread_count(): void
    {
        $this->createNotification();
        $this->createNotification(['title' => 'Second Notif']);

        $response = $this->actingAs($this->user)->getJson(route('notifications.dropdown'));
        $response->assertOk();
        $response->assertJsonStructure(['unread', 'items']);
        $this->assertEquals(2, $response->json('unread'));
    }

    public function test_dropdown_returns_max_5_items(): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->createNotification(['title' => "Notif $i"]);
        }

        $response = $this->actingAs($this->user)->getJson(route('notifications.dropdown'));
        $response->assertOk();
        $this->assertLessThanOrEqual(5, count($response->json('items')));
    }

    // ─── Read ────────────────────────────────────────────────

    public function test_reading_notification_marks_as_read(): void
    {
        $notif = $this->createNotification();
        $this->assertFalse($notif->is_read);

        $response = $this->actingAs($this->user)->get(route('notifications.read', $notif->id));

        $notif->refresh();
        $this->assertTrue($notif->is_read);
    }

    public function test_reading_notification_redirects_to_url(): void
    {
        $notif = $this->createNotification(['url' => route('dashboard')]);

        $response = $this->actingAs($this->user)->get(route('notifications.read', $notif->id));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_reading_notification_without_url_redirects_to_index(): void
    {
        $notif = $this->createNotification(['url' => null]);

        $response = $this->actingAs($this->user)->get(route('notifications.read', $notif->id));
        $response->assertRedirect(route('notifications.index'));
    }

    public function test_cannot_read_other_users_notification(): void
    {
        $otherNotif = Notification::create([
            'user_id' => $this->otherUser->id,
            'type' => 'inventaris_approved',
            'title' => 'Not mine',
            'body' => 'Body',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.read', $otherNotif->id));
        $response->assertNotFound();
    }

    // ─── Mark All Read ───────────────────────────────────────

    public function test_mark_all_read_works(): void
    {
        $this->createNotification();
        $this->createNotification();
        $this->createNotification();

        $this->actingAs($this->user)->postJson(route('notifications.mark-all'));

        $unreadCount = Notification::where('user_id', $this->user->id)
            ->where('is_read', false)
            ->count();
        $this->assertEquals(0, $unreadCount);
    }

    public function test_mark_all_read_json_response(): void
    {
        $this->createNotification();

        $response = $this->actingAs($this->user)->postJson(route('notifications.mark-all'));
        $response->assertOk();
        $response->assertJsonPath('success', true);
    }

    public function test_mark_all_read_does_not_affect_other_users(): void
    {
        $this->createNotification();
        $otherNotif = Notification::create([
            'user_id' => $this->otherUser->id,
            'type' => 'inventaris_pending',
            'title' => 'Other',
            'body' => 'Body',
            'is_read' => false,
        ]);

        $this->actingAs($this->user)->postJson(route('notifications.mark-all'));

        $otherNotif->refresh();
        $this->assertFalse($otherNotif->is_read);
    }

    public function test_delete_all_read_only_deletes_read_notifications_of_current_user(): void
    {
        $myRead = $this->createNotification(['is_read' => true, 'title' => 'My Read']);
        $myUnread = $this->createNotification(['is_read' => false, 'title' => 'My Unread']);
        $otherRead = Notification::create([
            'user_id' => $this->otherUser->id,
            'type' => 'inventaris_pending',
            'title' => 'Other Read',
            'body' => 'Body',
            'is_read' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('notifications.delete-read'));
        $response->assertRedirect();

        $this->assertDatabaseMissing('notifications', ['id' => $myRead->id]);
        $this->assertDatabaseHas('notifications', ['id' => $myUnread->id]);
        $this->assertDatabaseHas('notifications', ['id' => $otherRead->id]);
    }

    // ─── Destroy ─────────────────────────────────────────────

    public function test_user_can_delete_own_notification(): void
    {
        $notif = $this->createNotification();
        $notifId = $notif->id;

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $notifId));

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['id' => $notifId]);
    }

    public function test_user_cannot_delete_other_users_notification(): void
    {
        $otherNotif = Notification::create([
            'user_id' => $this->otherUser->id,
            'type' => 'inventaris_pending',
            'title' => 'Protected',
            'body' => 'Body',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->delete(route('notifications.destroy', $otherNotif->id));
        $response->assertNotFound();
    }

    // ─── Notification::send Helper ───────────────────────────

    public function test_notification_send_helper_creates_record(): void
    {
        $notif = Notification::send(
            $this->user->id,
            'inventaris_approved',
            'Test Send',
            'Body test',
            ['inventaris_id' => 1, 'actor_id' => $this->otherUser->id],
            'http://example.com'
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'user_id' => $this->user->id,
            'type' => 'inventaris_approved',
            'title' => 'Test Send',
            'is_read' => false,
        ]);
    }

    public function test_notification_send_to_many_creates_multiple(): void
    {
        Notification::sendToMany(
            [$this->user->id, $this->otherUser->id],
            'inventaris_pending',
            'Bulk Test',
            'Body'
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'title' => 'Bulk Test',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->otherUser->id,
            'title' => 'Bulk Test',
        ]);
    }

    public function test_notification_send_to_roles(): void
    {
        Notification::sendToRoles(
            ['super_admin'],
            'inventaris_pending',
            'Role Notif',
            'Body role'
        );

        // Both users are super_admin
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'title' => 'Role Notif',
        ]);
    }

    public function test_notification_send_to_all_active_users(): void
    {
        Notification::sendToAll('inventaris_pending', 'All Users', 'Body all');

        $allActive = User::where('is_aktif', true)->count();
        $notifsCreated = Notification::where('title', 'All Users')->count();
        $this->assertEquals($allActive, $notifsCreated);
    }

    // ─── Notification Accessors ──────────────────────────────

    public function test_notification_icon_accessor(): void
    {
        $notif = $this->createNotification(['type' => 'inventaris_pending']);
        $this->assertEquals('clock', $notif->icon);

        $notif2 = $this->createNotification(['type' => 'inventaris_approved']);
        // Approval-related icons have been removed; default to 'bell'
        $this->assertEquals('bell', $notif2->icon);

        $notif3 = $this->createNotification(['type' => 'inventaris_rejected']);
        $this->assertEquals('bell', $notif3->icon);
    }

    public function test_notification_color_accessor(): void
    {
        $notif = $this->createNotification(['type' => 'inventaris_approved']);
        // Approval-related colors removed; default to neutral
        $this->assertEquals('#6b7280', $notif->color);
    }

    public function test_notification_bg_color_accessor(): void
    {
        $notif = $this->createNotification(['type' => 'inventaris_rejected']);
        $this->assertEquals('#f3f4f6', $notif->bg_color);
    }

    public function test_reading_notification_blocks_external_open_redirect(): void
    {
        $notif = $this->createNotification(['url' => 'https://evil.com/phishing']);
        $response = $this->actingAs($this->user)->get(route('notifications.read', $notif->id));
        $response->assertRedirect(route('notifications.index'));
    }

    public function test_reading_notification_blocks_javascript_scheme_and_protocol_relative_url(): void
    {
        $notifJs = $this->createNotification(['url' => 'javascript:alert(1)']);
        $responseJs = $this->actingAs($this->user)->get(route('notifications.read', $notifJs->id));
        $responseJs->assertRedirect(route('notifications.index'));

        $notifRelative = $this->createNotification(['url' => '//evil.com/payload']);
        $responseRel = $this->actingAs($this->user)->get(route('notifications.read', $notifRelative->id));
        $responseRel->assertRedirect(route('notifications.index'));
    }
}
