<?php

namespace ZapsterStudios\Ally\Controllers\Subscription;

use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Controllers\Controller;

class InvoiceController extends Controller
{
    /**
     * Return a teams invoices.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function index(Request $request, Team $team)
    {
        $this->authorize('invoices', $team);

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
     * @param  Request  $request
     * @param  \App\Team  $team
     * @param  string  $invoice
     * @return Response
     */
    public function show(Request $request, Team $team, $invoice)
    {
        $this->authorize('invoices', $team);

        return $team->downloadInvoice($invoice, [
            'vendor'  => config('app.name'),
            'product' => 'Membership Subscription',
        ]);
    }
}
