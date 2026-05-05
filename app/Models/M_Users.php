<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Users extends Model
{
	protected $table      = 'users';
	protected $primaryKey = 'id';
	protected $returnType = 'array';

	protected $allowedFields = [
		'id',               # Primary key (must be set to match auth.id)
        'description',      # VARCHAR(255)
        'first_name',       # VARCHAR(50)
        'last_name',        # VARCHAR(50)
        'display_name',     # VARCHAR(100)
        'status',           # Banned | Active | Away | Inactive
        'role',             # Admin | Moderator | User | Guest

		'created_at',       # TIMESTAMP
	];

    public function hasAchievement(int $userId, string $key): bool
    {
        try {
            return (bool) $this->db->table('user_achievements')
                ->where('user_id', $userId)
                ->where('achievement', $key)
                ->countAllResults();
        } catch (\Throwable $e) {
            return true; // table missing → treat as earned so dashboard doesn't crash
        }
    }

    public function grantAchievement(int $userId, string $key): void
    {
        try {
            $this->db->table('user_achievements')->insert([
                'user_id'     => $userId,
                'achievement' => $key,
            ]);
        } catch (\Throwable $e) {
            // UNIQUE KEY violation = already earned, safe to ignore
        }
    }

    public function revokeAchievement(int $userId, string $key): void
    {
        $this->db->table('user_achievements')
            ->where('user_id', $userId)
            ->where('achievement', $key)
            ->delete();
    }
}