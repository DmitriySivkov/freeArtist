<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Team */
class UserTeamResource extends JsonResource
{
	public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
			'id' 			=> $this->id,
			'user_id' 		=> $this->user_id,
			'detailed_id' 	=> $this->detailed_id,
			'detailed_type' => $this->detailed_type,
			'detailed' 		=> $this->detailed,
			'role_id' 		=> $this->pivot->role_id,
			'permissions' 	=> collect($this->permissions)->map(fn($permission) =>
				[
					'id' => $permission->id,
					'name' => $permission->name
				])
		];
    }
}
