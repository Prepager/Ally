<?php

namespace ZapsterStudios\TeamPay\Models;

use TeamPay;
use Validator;
use Carbon\Carbon;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use Billable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug',
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
     */
    public function members()
    {
        return $this->belongsToMany('App\User', 'team_members', 'team_id', 'user_id')
            ->withPivot('id', 'group', 'overwrites');
    }

    /**
     * Get all the team member fields.
     */
    public function teamMembers()
    {
        return $this->hasMany('ZapsterStudios\TeamPay\Models\TeamMember', 'team_id', 'id');
    }

    /**
     * Get the teams active plan.
     */
    public function plan()
    {
        if ($this->subscribed()) {
            return TeamPay::plan($this->subscription()->braintree_plan);
        }

        return TeamPay::freePlan();
    }

    /**
     * Get the teams plan permissions.
     */
    public function permissions()
    {
        return $this->plan()->permissions;
    }

    /**
     * Get a specefic teams plan permission.
     */
    public function permission($permission)
    {
        return $this->plan()->permissions[$permission];
    }

    /**
     * Generate team slug.
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
}
