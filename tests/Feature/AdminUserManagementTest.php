<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── ACCESS CONTROL ──────────────────────────────────────────────

test('admin can access user management index', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertViewIs('admin.users.index');
});

test('non-admin cannot access user management index', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)->get(route('admin.users.index'));

    $response->assertForbidden();
});

test('guest cannot access user management index', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});

// ─── INDEX & SEARCH ──────────────────────────────────────────────

test('index shows only customer users', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create(['name' => 'John Customer']);
    $anotherAdmin = User::factory()->admin()->create(['name' => 'Jane Admin']);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertViewHas('users', function ($users) use ($customer, $anotherAdmin) {
        return $users->contains($customer) && ! $users->contains($anotherAdmin);
    });
});

test('index search filters users by name', function () {
    $admin = User::factory()->admin()->create();
    $john = User::factory()->customer()->create(['name' => 'John Doe']);
    $jane = User::factory()->customer()->create(['name' => 'Jane Smith']);

    $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'John']));

    $response->assertOk();
    $response->assertViewHas('users', function ($users) use ($john, $jane) {
        return $users->contains($john) && ! $users->contains($jane);
    });
});

test('index search filters users by email', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->customer()->create(['email' => 'specific@example.com']);
    User::factory()->customer()->create(['email' => 'other@test.com']);

    $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'specific@example']));

    $response->assertOk();
    $response->assertViewHas('users', function ($users) use ($target) {
        return $users->contains($target) && $users->count() === 1;
    });
});

test('index provides correct stats', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->customer()->count(3)->create();
    User::factory()->customer()->banned()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertViewHas('stats', function ($stats) {
        return $stats['total'] === 5
            && $stats['active'] === 3
            && $stats['banned'] === 2;
    });
});

// ─── SHOW DETAIL ─────────────────────────────────────────────────

test('admin can view user detail', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($admin)->get(route('admin.users.show', $customer));

    $response->assertOk();
    $response->assertViewIs('admin.users.show');
    $response->assertViewHas('user', function ($user) use ($customer) {
        return $user->id === $customer->id;
    });
});

test('show provides transaction summary', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($admin)->get(route('admin.users.show', $customer));

    $response->assertViewHas('transactionSummary', function ($summary) {
        return array_key_exists('total_reservations', $summary)
            && array_key_exists('total_spent', $summary)
            && array_key_exists('total_court_spending', $summary)
            && array_key_exists('total_coach_spending', $summary)
            && array_key_exists('total_equipment_spending', $summary)
            && array_key_exists('total_membership_spending', $summary);
    });
});

// ─── BAN USER ────────────────────────────────────────────────────

test('admin can ban a user with reason', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($admin)->patch(route('admin.users.ban', $customer), [
        'reason' => 'Spamming reservations',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $customer->refresh();
    expect($customer->banned_at)->not->toBeNull();
    expect($customer->banned_reason)->toBe('Spamming reservations');
    expect($customer->isBanned())->toBeTrue();
});

test('ban requires a reason', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($admin)->patch(route('admin.users.ban', $customer), [
        'reason' => '',
    ]);

    $response->assertSessionHasErrors('reason');
});

// ─── UNBAN USER ──────────────────────────────────────────────────

test('admin can unban a user', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->banned()->create();

    expect($customer->isBanned())->toBeTrue();

    $response = $this->actingAs($admin)->patch(route('admin.users.unban', $customer));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $customer->refresh();
    expect($customer->banned_at)->toBeNull();
    expect($customer->banned_reason)->toBeNull();
    expect($customer->isBanned())->toBeFalse();
});

// ─── ANONYMIZE USER ──────────────────────────────────────────────

test('admin can anonymize a customer user', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create([
        'name' => 'Real Name',
        'email' => 'real@email.com',
        'phone' => '08123456789',
    ]);
    $customerId = $customer->id;

    $response = $this->actingAs($admin)->delete(route('admin.users.anonymize', $customer));

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success');

    // User should be soft-deleted
    $this->assertSoftDeleted('users', ['id' => $customerId]);

    // PII should be scrubbed
    $deletedUser = User::withTrashed()->find($customerId);
    expect($deletedUser->name)->toBe('Deleted User #'.$customerId);
    expect($deletedUser->email)->toContain('@anonymized.local');
    expect($deletedUser->phone)->toBeNull();
});

test('admin cannot anonymize another admin account', function () {
    $admin = User::factory()->admin()->create();
    $anotherAdmin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete(route('admin.users.anonymize', $anotherAdmin));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Admin should not be deleted
    $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
});

// ─── BANNED MIDDLEWARE ───────────────────────────────────────────

test('banned user is logged out on next request', function () {
    $customer = User::factory()->customer()->banned()->create();

    $response = $this->actingAs($customer)->get(route('dashboard'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('active user can access protected routes normally', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)->get(route('dashboard'));

    $response->assertOk();
});
