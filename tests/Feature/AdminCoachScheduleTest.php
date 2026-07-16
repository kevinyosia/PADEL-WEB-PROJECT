<?php

use App\Models\Coach;
use App\Models\User;

use function Pest\Laravel\actingAs;

// ── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Build a valid schedule payload with the new session-based format.
 *
 * @param  array<string, mixed>  $overrides
 */
function validSchedule(array $overrides = []): array
{
    $base = [
        'mon' => ['active' => true,  'sessions' => [['start' => '09:00', 'end' => '12:00']]],
        'tue' => ['active' => false, 'sessions' => []],
        'wed' => ['active' => false, 'sessions' => []],
        'thu' => ['active' => false, 'sessions' => []],
        'fri' => ['active' => false, 'sessions' => []],
    ];

    return array_merge($base, $overrides);
}

/**
 * Build a valid store-coach payload.
 *
 * @param  array<string, mixed>  $overrides
 */
function validCoachPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'John Coach',
        'email' => 'john'.uniqid().'@example.com',
        'phone' => '08123456789',
        'deskripsi_keahlian' => 'Experienced padel coach',
        'harga_per_jam' => 150000,
        'availability_status' => 'active',
        'schedule' => validSchedule(),
    ], $overrides);
}

/** Create and log-in an admin user. */
function adminUser(): User
{
    return User::factory()->admin()->create();
}

/** Create an existing coach record for update tests. */
function existingCoach(): Coach
{
    $user = User::factory()->create(['role' => 'coach']);

    return Coach::create([
        'user_id' => $user->id,
        'deskripsi_keahlian' => 'Existing coach',
        'harga_per_jam' => 100000,
        'availability_status' => 'active',
        'schedule' => [
            'mon' => ['active' => true,  'sessions' => [['start' => '08:00', 'end' => '10:00']]],
            'tue' => ['active' => false, 'sessions' => []],
            'wed' => ['active' => false, 'sessions' => []],
            'thu' => ['active' => false, 'sessions' => []],
            'fri' => ['active' => false, 'sessions' => []],
        ],
    ]);
}

// ── Store Tests ───────────────────────────────────────────────────────────────

describe('AdminCoachController@store', function () {
    it('stores a coach with a valid single session', function () {
        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload())
            ->assertRedirect(route('admin.coaches.index'))
            ->assertSessionHas('success');

        $coach = Coach::latest()->first();

        expect($coach->schedule['mon']['active'])->toBeTrue()
            ->and($coach->schedule['mon']['sessions'])->toHaveCount(1)
            ->and($coach->schedule['mon']['sessions'][0])->toMatchArray(['start' => '09:00', 'end' => '12:00']);
    });

    it('stores a coach with multiple valid sessions on one day', function () {
        $schedule = validSchedule([
            'mon' => [
                'active' => true,
                'sessions' => [
                    ['start' => '08:00', 'end' => '10:00'],
                    ['start' => '13:00', 'end' => '16:00'],
                    ['start' => '17:00', 'end' => '19:00'],
                ],
            ],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertRedirect(route('admin.coaches.index'));

        expect(Coach::latest()->first()->schedule['mon']['sessions'])->toHaveCount(3);
    });

    it('saves inactive days with empty sessions array', function () {
        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload())
            ->assertRedirect();

        $coach = Coach::latest()->first();

        foreach (['tue', 'wed', 'thu', 'fri'] as $day) {
            expect($coach->schedule[$day]['active'])->toBeFalse()
                ->and($coach->schedule[$day]['sessions'])->toBeEmpty();
        }
    });

    it('rejects store when no day is active', function () {
        $schedule = [
            'mon' => ['active' => false, 'sessions' => []],
            'tue' => ['active' => false, 'sessions' => []],
            'wed' => ['active' => false, 'sessions' => []],
            'thu' => ['active' => false, 'sessions' => []],
            'fri' => ['active' => false, 'sessions' => []],
        ];

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors('schedule');
    });

    it('rejects store when active day has no sessions', function () {
        $schedule = validSchedule([
            'mon' => ['active' => true, 'sessions' => []],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors();
    });

    it('rejects store when end time is before start time', function () {
        $schedule = validSchedule([
            'mon' => ['active' => true, 'sessions' => [['start' => '14:00', 'end' => '12:00']]],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors();
    });

    it('rejects store when session duration is less than 1 hour', function () {
        $schedule = validSchedule([
            'mon' => ['active' => true, 'sessions' => [['start' => '09:00', 'end' => '09:30']]],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors();
    });

    it('rejects store when session duration exceeds 4 hours', function () {
        $schedule = validSchedule([
            'mon' => ['active' => true, 'sessions' => [['start' => '09:00', 'end' => '14:00']]],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors();
    });

    it('rejects store when more than 3 sessions are submitted for one day', function () {
        $schedule = validSchedule([
            'mon' => [
                'active' => true,
                'sessions' => [
                    ['start' => '07:00', 'end' => '08:00'],
                    ['start' => '09:00', 'end' => '10:00'],
                    ['start' => '11:00', 'end' => '12:00'],
                    ['start' => '13:00', 'end' => '14:00'],
                ],
            ],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors();
    });

    it('rejects store when sessions overlap within a day', function () {
        $schedule = validSchedule([
            'mon' => [
                'active' => true,
                'sessions' => [
                    ['start' => '09:00', 'end' => '12:00'],
                    ['start' => '11:00', 'end' => '13:00'], // overlaps with first
                ],
            ],
        ]);

        actingAs(adminUser())
            ->post(route('admin.coaches.store'), validCoachPayload(['schedule' => $schedule]))
            ->assertSessionHasErrors();
    });
});

// ── Update Tests ──────────────────────────────────────────────────────────────

describe('AdminCoachController@update', function () {
    it('updates coach schedule successfully', function () {
        $coach = existingCoach();

        $newSchedule = validSchedule([
            'mon' => ['active' => false, 'sessions' => []],
            'fri' => ['active' => true,  'sessions' => [['start' => '13:00', 'end' => '16:00']]],
        ]);

        actingAs(adminUser())
            ->patch(route('admin.coaches.update', $coach), [
                'deskripsi_keahlian' => $coach->deskripsi_keahlian,
                'harga_per_jam' => $coach->harga_per_jam,
                'availability_status' => $coach->availability_status,
                'schedule' => $newSchedule,
            ])
            ->assertRedirect(route('admin.coaches.index'))
            ->assertSessionHas('success');

        $coach->refresh();

        expect($coach->schedule['mon']['active'])->toBeFalse()
            ->and($coach->schedule['fri']['active'])->toBeTrue()
            ->and($coach->schedule['fri']['sessions'][0])->toMatchArray(['start' => '13:00', 'end' => '16:00']);
    });

    it('rejects update with overlapping sessions', function () {
        $coach = existingCoach();

        $schedule = validSchedule([
            'mon' => [
                'active' => true,
                'sessions' => [
                    ['start' => '09:00', 'end' => '11:00'],
                    ['start' => '10:00', 'end' => '12:00'],
                ],
            ],
        ]);

        actingAs(adminUser())
            ->patch(route('admin.coaches.update', $coach), [
                'deskripsi_keahlian' => $coach->deskripsi_keahlian,
                'harga_per_jam' => $coach->harga_per_jam,
                'availability_status' => $coach->availability_status,
                'schedule' => $schedule,
            ])
            ->assertSessionHasErrors();
    });
});

// ── Coach Model Tests ─────────────────────────────────────────────────────────

describe('Coach model helpers', function () {
    beforeEach(function () {
        $this->coach = existingCoach();
    });

    it('isAvailableOnDay returns true for active days', function () {
        expect($this->coach->isAvailableOnDay('mon'))->toBeTrue()
            ->and($this->coach->isAvailableOnDay('tue'))->toBeFalse();
    });

    it('getSessionsForDay returns sessions array', function () {
        expect($this->coach->getSessionsForDay('mon'))->toHaveCount(1)
            ->and($this->coach->getSessionsForDay('tue'))->toBeEmpty();
    });

    it('getSessionCount returns total sessions across all active days', function () {
        expect($this->coach->getSessionCount())->toBe(1);
    });

    it('getActiveDaysCount returns number of active days', function () {
        expect($this->coach->getActiveDaysCount())->toBe(1);
    });

    it('isTimeInSession returns true when time falls within a session', function () {
        expect($this->coach->isTimeInSession('mon', '08:30'))->toBeTrue()
            ->and($this->coach->isTimeInSession('mon', '10:00'))->toBeFalse() // at or after end
            ->and($this->coach->isTimeInSession('tue', '09:00'))->toBeFalse(); // inactive day
    });
});
