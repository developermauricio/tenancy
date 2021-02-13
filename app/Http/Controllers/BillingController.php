<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function creditCardForm() {
        return view("system.credit_card_form");
    }

    public function processCreditCardForm() {
        $this->validate(request(), [
            'card_number' => 'required',
            'card_exp_year' => 'required',
            'card_exp_month' => 'required',
            'cvc' => 'required'
        ]);

        try {
            DB::beginTransaction();
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if ( ! auth()->user()->hasPaymentMethod()) {
                auth()->user()->createAsStripeCustomer();
            }

            $paymentMethod = \Stripe\PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'number' => request('card_number'),
                    'exp_month' => request('card_exp_month'),
                    'exp_year' => request('card_exp_year'),
                    'cvc' => request('cvc'),
                ]
            ]);
            auth()->user()->updateDefaultPaymentMethod($paymentMethod->id);
            auth()->user()->save();
            DB::commit();
            return back()->with('message', ['success', __('Tarjeta actualizada correctamente')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->with('message', ['danger', $exception->getMessage()]);
        }
    }
}
