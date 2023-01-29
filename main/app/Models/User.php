<?php

namespace App\Models;

use App\Notifications\EmailVerificationNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;


class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	const LOCATION_RANGE_NEARBY = 1;
	const LOCATION_RANGE_ALL = 2;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function outgoingRelationRequests()
	{
		return $this->morphMany(RelationRequest::class, 'from');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function incomingRelationRequests()
	{
		return $this->morphMany(RelationRequest::class, 'to');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function ownProducer()
	{
		return $this->hasOne(Team::class)
			->where('detailed_type', Producer::class)
			->where('user_id', $this->id);
	}

	/**
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function scopeNonRelatedTeams()
	{
		return Team::whereNotIn(
			'id',
			$this->rolesTeams->pluck('id')
		);
	}

    /**
     * We override the default verification and use our own
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification($this));
    }
}
