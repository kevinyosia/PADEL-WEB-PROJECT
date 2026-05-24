<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'photo',
        'banned_at',
        'banned_reason',
    ];

    /**
     * @property string $name
     * @property string $email
     * @property string|null $phone
     * @property string $password
     * @property string $role
     * @property int $points
     * @property \Illuminate\Support\Carbon|null $banned_at
     * @property string|null $banned_reason
     * @property \Illuminate\Support\Carbon|null $deleted_at
     */

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'banned_at' => 'datetime',
        ];
    }

    // ==========================================
    // ACCESSORS & SCOPES
    // ==========================================

    /**
     * Check if user is currently banned.
     */
    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    /**
     * Scope query to only customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeCustomers(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('role', 'customer');
    }

    // ==========================================
    // RELASI DATABASE (TAMBAHAN UNTUK SKRIPSI)
    // ==========================================
    public function reservations()// satu User mampu melakukan BANYAK reservasi
    {
        return $this->hasMany(Reservation::class);
    }

    public function membership()// satu user hanya boleh memiliki 1 membership
    {
        return $this->hasOne(Membership::class);
    }

    public function pointHistories()// satu user bisa memiliki BANYAK riawayat poin
    {
        return $this->hasMany(PointHistory::class);
    }

    public function feedbacks()// satu user bisa melakukan lebih dari 1 feedback
    {
        return $this->hasMany(Feedback::class);
    }

    public function coachReviews()// satu user bisa mereview coach lebih dari 1 kali
    {
        return $this->hasMany(CoachReview::class);
    }

    public function membershipPayments()
    {
        return $this->hasMany(MembershipPayment::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Reservation::class);
    }
}
