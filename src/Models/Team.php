<?php

namespace ZapsterStudios\Ally\Models;

use Ally;
use App\User;
use Validator;
use Carbon\Carbon;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use Billable, SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'suspended_at',
        'suspended_to',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'braintree_id',
        'paypal_email',
        'card_brand',
        'card_last_four',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('members', function (Builder $builder) {
            $builder->with('members');
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get all the team members.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(Ally::userModel(), 'team_members', 'team_id', 'user_id')
            ->withPivot('id', 'group', 'overwrites');
    }

    /**
     * Get the potential member count.
     *
     * @return int
     */
    public function potentialMemberCount()
    {
        return $this->members()->count() + $this->invitations()->count();
    }

    /**
     * Weather or not the plan max member limit have been reached.
     *
     * @return bool
     */
    public function maxMemberCountReached()
    {
        return $this->plan()->members !== 0 &&
            $this->potentialMemberCount() >= $this->plan()->members;
    }

    /**
     * Get all the team member invitations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invitations()
    {
        return $this->belongsTo('ZapsterStudios\Ally\Models\TeamInvitation', 'id', 'team_id');
    }

    /**
     * Get all the team member fields.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamMembers()
    {
        return $this->hasMany('ZapsterStudios\Ally\Models\TeamMember', 'team_id', 'id');
    }

    /**
     * Get the teams active plan.
     *
     * @return \ZapsterStudios\Ally\Configuration\Models\Plan
     */
    public function plan()
    {
        if ($this->subscribed()) {
            return Ally::plan($this->subscription()->braintree_plan);
        }

        return Ally::freePlan();
    }

    /**
     * Get the teams plan permissions.
     *
     * @return array
     */
    public function permissions()
    {
        return $this->plan()->permissions;
    }

    /**
     * Get a specefic teams plan permission.
     *
     * @param  array  $permission
     * @return array
     */
    public function permission($permission)
    {
        return $this->plan()->permissions[$permission];
    }

    /**
     * Generate team slug.
     *
     * @param  string  $slug
     * @param  string|null  $original
     * @param  string|bool  $current
     * @param  int  $id
     * @return string
     */
    public static function generateSlug($slug, $original = null, $current = false, $id = 1)
    {
        if (! $original) {
            $original = $slug;
        }

        if ($current && $current == $slug) {
            return $slug;
        }

        $unique = Validator::make(['slug' => $slug], [
            'slug' => 'required|unique:teams,slug',
        ]);

        return $unique->fails()
            ? self::generateSlug($original.'-'.$id, $original, $current, $id + 1)
            : $slug;
    }

    /**
     * Whatever or not the team is suspended.
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
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->slug)).'?d=identicon&s=150';
    }

    /**
     * Cancel team subscription if subscribed.
     *
     * @param  bool  $now
     * @return void
     */
    public function cancelIfSubscribed($now = false)
    {
        if (! $this->subscribed()) {
            return;
        }

        if ($now) {
            $this->subscription()->cancelNow();
        } else {
            $this->subscription()->cancel();
        }
    }

    /**
     * Remove team members.
     *
     * @return void
     */
    public function removeMembers()
    {
        $this->teamMembers()->delete();

        User::where('team_id', $this->id)->update([
            'team_id' => 0,
        ]);
    }

    /**
     * Delete a team based on configuration.
     *
     * @return void
     */
    public function performDeletion()
    {
        $skipGrace = Ally::$skipDeletionGracePeriod;
        $this->cancelIfSubscribed($skipGrace);

        if ($skipGrace) {
            $this->removeMembers();
            $this->forceDelete();

            return;
        }

        $this->delete();
    }
}
