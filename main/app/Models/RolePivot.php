<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePivot extends Pivot
{
	public function role() {
		return $this->hasOne(Role::Class,'id','role_id');
	}
}
