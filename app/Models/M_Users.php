<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Users extends Model
{
	protected $table      = 'users';
	protected $primaryKey = 'id';
	protected $returnType = 'array';

	protected $allowedFields = [
        'description',
        'first_name',
        'last_name',
        'display_name',
        'status',           # Banned | Active | Away | Inactive
        'role',             # Admin | Moderator | User | Guest
		'created_at',
	];
}