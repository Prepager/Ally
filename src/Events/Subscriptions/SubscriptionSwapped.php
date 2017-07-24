<?php

namespace ZapsterStudios\Ally\Events\Subscriptions;

use App\Team;
use Laravel\Cashier\Subscription;
use Illuminate\Queue\SerializesModels;

class SubscriptionSwapped
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \App\Team  $team
     * @param  \Laravel\Cashier\Subscription  $subscription
     * @return void
     */
    public function __construct(Team $team, Subscription $subscription)
    {
        $this->team = $team;
        $this->subscription = $subscription;
    }
}
