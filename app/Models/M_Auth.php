<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Auth extends Model
{
	protected $table      = 'auth';
	protected $primaryKey = 'id';
	protected $returnType = 'array';

	protected $allowedFields = [
		'username',			# VARCHAR(25)
		'email',			# youremail@yourwebsite.yourdomain
		'password',			# VARCHAR(255)
        'last_login',		# TIMESTAMP
		'created_at',		# TIMESTAMP
        'verified',			# BOOLEAN
        'verification_token', # VARCHAR(100)
        'verification_expires_at', # TIMESTAMP
		'remember_selector', # VARCHAR(50)
		'remember_hash', 		# VARCHAR(100)
		'remember_expires_at', # TIMESTAMP
	];
}