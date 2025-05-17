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

    // --- ROLE BARU UNTUK AKSES READ-ONLY ---
    public const ROLE_USER = 'user'; // Tetap ada, akan kita berikan akses read-only
    public const ROLE_MENTERI = 'menteri';
    public const ROLE_WAKIL_MENTERI = 'wakil_menteri';
    public const ROLE_STAFF_KHUSUS = 'staff_khusus';
    // ---------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    // Helper method untuk peran Eselon I (yang bisa CRUD)
    public function isItjen(): bool { return $this->hasRole(self::ROLE_ITJEN); }
    public function isSekjen(): bool { return $this->hasRole(self::ROLE_SEKJEN); }
    public function isBinapenta(): bool { return $this->hasRole(self::ROLE_BINAPENTA); }
    public function isBinalavotas(): bool { return $this->hasRole(self::ROLE_BINALAVOTAS); }
    public function isBinwasnaker(): bool { return $this->hasRole(self::ROLE_BINWASNAKER); }
    public function isPhi(): bool { return $this->hasRole(self::ROLE_PHI); }
    public function isBarenbang(): bool { return $this->hasRole(self::ROLE_BARENBANG); }


    // --- HELPER METHOD UNTUK ROLE READ-ONLY (OPSIONAL) ---
    public function isUserBiasa(): bool { return $this->hasRole(self::ROLE_USER); }
    public function isMenteri(): bool { return $this->hasRole(self::ROLE_MENTERI); }
    public function isWakilMenteri(): bool { return $this->hasRole(self::ROLE_WAKIL_MENTERI); }
    public function isStaffKhusus(): bool { return $this->hasRole(self::ROLE_STAFF_KHUSUS); }

    /**
     * Check if the user has one of the Eselon I roles or Superadmin.
     * These roles typically have CRUD permissions.
     * @return bool
     */
    public function canPerformCRUD(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPERADMIN,
            self::ROLE_ITJEN,
            self::ROLE_SEKJEN,
            self::ROLE_BINAPENTA,
            self::ROLE_BINALAVOTAS,
            self::ROLE_BINWASNAKER,
            self::ROLE_PHI,
            self::ROLE_BARENBANG,
        ]);
    }

    /**
     * Check if the user has one of the read-only roles.
     * @return bool
     */
    public function isReadOnlyUser(): bool
    {
        return in_array($this->role, [
            self::ROLE_USER,
            self::ROLE_MENTERI,
            self::ROLE_WAKIL_MENTERI,
            self::ROLE_STAFF_KHUSUS,
        ]);
    }
}