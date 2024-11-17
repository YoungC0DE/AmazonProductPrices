<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class BaseModel extends Model
{
	public const CREATED_AT = 'createdAt';
	public const UPDATED_AT = 'updatedAt';
}
