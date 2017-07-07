<?php

namespace ZapsterStudios\TeamPay\Controllers;

use TeamPay;
use App\Team;
use Illuminate\Http\Request;

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
        $this->authorize('update', $team);
        $this->validate($request, [
            'plan' => 'required',
            'type' => 'required',
            'nonce' => 'required',
        ]);

        $plan = TeamPay::activePlans()->where('id', $request->plan)->first();
        if (! $plan) {
            return response()->json([
                'plan' => ['Unavailable plan.'],
            ]);
        }

        if ($plan->id === TeamPay::freePlan()->id) {
            $team->subscription()->cancel();

            return response()->json([
                'message' => 'Subscription cancelled',
            ]);
        }

        if ($team->subscribed()) {
            $subscription = $team->subscription()->swap($plan->id);

            return response()->json($subscription);
        }

        $subscription = $team->newSubscription('default', $plan->id)
            ->withCoupon($request->coupon ?? null)
            ->create($request->nonce);

        return response()->json($subscription);
    }

    /**
     * Cancel a teams subscription.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function cancel(Team $team)
    {
        $this->authorize('update', $team);

        $subscription = $team->subscription()->cancel();

        return response()->json($subscription);
    }

    /**
     * Resume a teams subscription.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function resume(Team $team)
    {
        $this->authorize('update', $team);

        $subscription = $team->subscription()->resume();

        return response()->json($subscription);
    }
}
