<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Definisikan konstanta untuk peran agar mudah dikelola
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ITJEN = 'itjen';
    public const ROLE_SEKJEN = 'sekjen';
    public const ROLE_BINAPENTA = 'binapenta';
    public const ROLE_BINALAVOTAS = 'binalavotas';
    public const ROLE_BINWASNAKER = 'binwasnaker';
    public const ROLE_PHI = 'phi';
    public const ROLE_BARENBANG = 'barenbang';
    public const ROLE_USER = 'user'; // Peran default jika ada

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Tambahkan role
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    /**
     * Check if the user is a Superadmin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPERADMIN);
    }

    // Anda bisa menambahkan helper method lain seperti ini untuk setiap peran
    public function isItjen(): bool
    {
        return $this->hasRole(self::ROLE_ITJEN);
    }

    public function isSekjen(): bool
    {
        return $this->hasRole(self::ROLE_SEKJEN);
    }
    // ... dan seterusnya untuk peran lain
}
