<?php

namespace ZapsterStudios\Ally\Controllers\Subscription;

use Ally;
use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Controllers\Controller;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionCreated;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionResumed;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionSwapped;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionCancelled;

class SubscriptionController extends Controller
{
    /**
     * Subscribe the team to a plan.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function subscription(Request $request, Team $team)
    {
        $this->authorize('billing', $team);
        $this->validate($request, [
            'plan' => 'required|in:'.Ally::plans()->implode('id', ','),
            'nonce' => 'required',
        ]);

        $plan = Ally::activePlans()->where('id', $request->plan)->first();

        if (Ally::freePlan() && $plan->id === Ally::freePlan()->id) {
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
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
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
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function resume(Request $request, Team $team)
    {
        $this->authorize('billing', $team);

        $subscription = $team->subscription()->resume();
        event(new SubscriptionResumed($team));

        return response()->json($subscription);
    }
}
