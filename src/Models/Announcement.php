<?php

namespace ZapsterStudios\Ally\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'message', 'visit',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('author', function (Builder $builder) {
            $builder->with('author');
        });
    }

    /**
     * Get the announcement author.
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
