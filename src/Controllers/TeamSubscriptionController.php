<?php

namespace ZapsterStudios\TeamPay\Controllers;

use TeamPay;
use App\Team;
use Illuminate\Http\Request;

class TeamSubscriptionController extends Controller
{
    /**
     * Subscribe the team to a plan.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request, Team $team)
    {
        $this->validate($request, [
            'plan' => 'required|in_array:'.TeamPay::activePlans()->implode('id', ','),
            'type' => 'required',
            'nonce' => 'required',
        ]);

        $plan = TeamPay::activePlans()->where('id', $request->plan)->first();

        if($plan->price == 0) {
            //

            return;
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
}
