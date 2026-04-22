<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
    ];

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
        ];
    }
    // ==========================================
    // RELASI DATABASE (TAMBAHAN UNTUK SKRIPSI)
    // ==========================================
    public function reservations()//satu User mampu melakukan BANYAK reservasi
    {
        return $this->hasMany(Reservation::class);
    }
    public function membership()//satu user hanya boleh memiliki 1 membership
    {
        return $this->hasOne(Membership::class);
    }
    public function pointHistories()//satu user bisa memiliki BANYAK riawayat poin
    {
        return $this->hasMany(PointHistory::class);
    }
    public function feedbacks()//satu user bisa melakukan lebih dari 1 feedback
    {
        return $this->hasMany(Feedback::class);
    }
    public function coachReviews()//satu user bisa mereview coach lebih dari 1 kali
    {
        return $this->hasMany(CoachReview::class);
    }
}
