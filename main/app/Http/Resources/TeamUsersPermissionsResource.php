<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class TeamUsersPermissionsResource extends JsonResource
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
			'name' 			=> $this->name,
			'phone' 		=> $this->phone,
			'permissions' 	=> $this->permissions->pluck('name')
		];
    }
}
