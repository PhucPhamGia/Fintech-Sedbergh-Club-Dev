<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Users extends Model
{
	protected $table      = 'users';
	protected $primaryKey = 'id';
	protected $returnType = 'array';

	protected $allowedFields = [
        'description',      # VARCHAR(255)
        'first_name',       # VARCHAR(50)
        'last_name',        # VARCHAR(50)
        'display_name',     # VARCHAR(100)
        'status',           # Banned | Active | Away | Inactive
        'role',             # Admin | Moderator | User | Guest
		'created_at',       # TIMESTAMP
	];
}