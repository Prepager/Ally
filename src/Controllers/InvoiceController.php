<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Return a teams invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Team $team)
    {
        abort_unless($request->user()->tokenCan('view-invoices'), 401);
        $this->authorize('update', $team);

        $invoices = [];
        if ($team->hasBraintreeId()) {
            $invoices = $team->invoices(true)->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'status' => $invoice->status,
                    'total' => $invoice->total(),
                    'created_at' => $invoice->date()->toDateTimeString(),
                ];
            });
        }

        return response()->json($invoices);
    }

    /**
     * Return a specefic team invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Team $team, $invoice)
    {
        abort_unless($request->user()->tokenCan('view-invoices'), 401);
        $this->authorize('update', $team);

        return $team->downloadInvoice($invoice, [
            'vendor'  => config('app.name'),
            'product' => 'Membership Subscription',
        ]);
    }
}
