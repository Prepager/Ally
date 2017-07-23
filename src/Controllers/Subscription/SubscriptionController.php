<?php

namespace ZapsterStudios\TeamPay\Controllers\Subscription;

use TeamPay;
use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\TeamPay\Events\Subscriptions\SubscriptionCreated;
use ZapsterStudios\TeamPay\Events\Subscriptions\SubscriptionResumed;
use ZapsterStudios\TeamPay\Events\Subscriptions\SubscriptionSwapped;
use ZapsterStudios\TeamPay\Events\Subscriptions\SubscriptionCancelled;

class SubscriptionController extends Controller
{
    /**
     * Subscribe the team to a plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function subscription(Request $request, Team $team)
    {
        $this->authorize('billing', $team);
        $this->validate($request, [
            'plan' => 'required|in:'.TeamPay::plans()->implode('id', ','),
            'nonce' => 'required',
        ]);

        $plan = TeamPay::activePlans()->where('id', $request->plan)->first();

        if ($plan->id === TeamPay::freePlan()->id) {
            $team->subscription()->cancel();

            event(new SubscriptionCancelled($team));

            return response()->json([
                'message' => 'Paid subscription cancelled',
            ]);
        }

        if ($team->subscribed()) {
            $subscription = $team->subscription()->swap($plan->id);

            event(new SubscriptionSwapped($team, $subscription));

            return response()->json($subscription);
        }

        $subscription = $team->newSubscription('default', $plan->id)
            ->withCoupon($request->coupon ?? null)
            ->create($request->nonce);

        event(new SubscriptionCreated($team, $subscription));

        return response()->json($subscription);
    }

    /**
     * Cancel a teams subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Team $team)
    {
        $this->authorize('billing', $team);

        $subscription = $team->subscription()->cancel();
        event(new SubscriptionCancelled($team));

        return response()->json($subscription);
    }

    /**
     * Resume a teams subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function resume(Request $request, Team $team)
    {
        $this->authorize('billing', $team);

        $subscription = $team->subscription()->resume();
        event(new SubscriptionResumed($team));

        return response()->json($subscription);
    }
}
