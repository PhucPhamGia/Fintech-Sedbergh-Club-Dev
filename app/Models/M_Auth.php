<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Auth extends Model
{
	protected $table      = 'auth';
	protected $primaryKey = 'id';
	protected $returnType = 'array';

	protected $allowedFields = [
		'username',
		'email',			# youremail@yourwebsite.yourdomain
		'password',
        'last_login',
		'created_at',
        'verified',
        'verification_token',
        'verification_expires_at',
		'remember_selector',
		'remember_hash',
		'remember_expires_at',
	];
}