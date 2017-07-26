<?php

namespace ZapsterStudios\Ally\Models;

use Ally;
use App\Team;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'suspended_at',
        'suspended_to',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'avatar', 'password', 'country', 'email_verified', 'email_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_token',
    ];

    /**
     * Get the users team invitations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany('ZapsterStudios\Ally\Models\TeamInvitation', 'email', 'email');
    }

    /**
     * Get the users active team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne('App\Team', 'id', 'team_id');
    }

    /**
     * Get all the users teams.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany('App\Team', 'team_members', 'user_id', 'team_id');
    }

    /**
     * Get all the users owned teams.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedTeams()
    {
        return $this->hasMany('App\Team', 'user_id', 'id');
    }

    /**
     * Get the users first team id or 0.
     *
     * @return \App\Team|int
     */
    public function firstTeam()
    {
        $team = $this->teams()->first();

        return $team ? $team->id : 0;
    }

    /**
     * Check if user is on team.
     *
     * @param  \App\Team  $team
     * @return bool
     */
    public function onTeam($team)
    {
        return $this->teams->contains($team);
    }

    /**
     * Check if user owns team.
     *
     * @param  \App\Team  $team
     * @return bool
     */
    public function ownsTeam($team)
    {
        return $this->id === $team->user_id;
    }

    /**
     * Check if a user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isVerified() && Ally::isAdmin($this->email);
    }

    /**
     * Check if a user has verified their email.
     *
     * @return bool
     */
    public function isVerified()
    {
        return $this->email_verified;
    }

    /**
     * Return a users team member on a team.
     *
     * @param  \App\Team  $team
     * @return mixed
     */
    public function teamMember(Team $team)
    {
        $member = $team->members->first(function ($user) {
            return $this->id === $user->id;
        });

        return $member ? $member->pivot : null;
    }

    /**
     * Return all permissions for a users team.
     *
     * @param  \App\Team  $team
     * @return Collection
     */
    public function groupPermissions(Team $team)
    {
        $member = $this->teamMember($team);
        if (! $member) {
            return [];
        }

        return collect(Ally::group($member->group)->permissions)
            ->merge($member->overwrites);
    }

    /**
     * Return a single permission for a users team.
     *
     * @param  \App\Team  $team
     * @param  string  $permission
     * @return bool
     */
    public function groupPermission(Team $team, $permission)
    {
        return $this->groupPermissions($team)->first(function ($perm) use ($permission) {
            return fnmatch($perm, $permission);
        }) ? true : false;
    }

    /**
     * Short alias of groupPermission.
     *
     * @param  mixed  ...$params
     * @return bool
     */
    public function groupCan(...$params)
    {
        return $this->groupPermission(...$params);
    }

    /**
     * Whatever or not the user is suspended.
     *
     * @return bool
     */
    public function suspended()
    {
        return $this->suspended_at && (
            ! $this->suspended_to
            || $this->suspended_to->toDateTimeString() >= Carbon::now()->toDateTimeString()
        );
    }

    /**
     * Return a users custom avatar or Gravatar.
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return empty($avatar) ? $this->gravatar() : Storage::url($avatar);
    }

    /**
     * Return a users Gravatar.
     *
     * @return string
     */
    public function gravatar()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'?d=identicon&s=150';
    }
}
