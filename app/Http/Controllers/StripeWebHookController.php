<?php

namespace App\Http\Controllers;


use App\Notifications\TenantCreated;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Laravel\Cashier\Subscription;
use Stripe\PaymentIntent as StripePaymentIntent;


class StripeWebHookController extends WebhookController
{
    /**
     *
     * WEBHOOK que se encarga de eliminar la suscripción del usuario en la plataforma
     * customer.subscription.deleted
     *
     * @param array $payload
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionDeleted(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->each(function ($subscription) {
                $subscription->markAsCancelled();
            });
        }
        return new Response('Webhook Handled', 200);
    }

    public function handleInvoicePaymentSucceeded($payload)
    {
        try {
            return new Response('Webhook Handled: {handleInvoicePaymentSucceeded}', 200);
        } catch (\Exception $exception) {
            Log::debug("Excepción Webhook {handleInvoicePaymentSucceeded}: " . $exception->getMessage() . ", Line: " . $exception->getLine() . ', File: ' . $exception->getFile());
            return new Response('Webhook Unhandled: {handleInvoicePaymentSucceeded}', 500);
        }
    }
    /**
     *
     * WEBHOOK que se encarga de obtener un evento al hacer la devolución de una suscripción desde Stripe
     * charge.refunded
     *
     * @param array $payload
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handleChargeRefunded ($payload)
    {
        try {
            $user = $this->getUserByStripeId($payload['data']['object']['customer']);
            if ($user) {
                if ($user->subscription('main') && $user->subscription('main')->active()) {
                    $user->subscription('main')->cancelNow();
                }
                return new Response('Webhook Handled: {handleChargeRefunded}', 200);
            }
        } catch (\Exception $exception) {
            Log::debug("Excepción Webhook {handleChargeRefunded}: " . $exception->getMessage() . ", Line: " . $exception->getLine() . ', File: ' . $exception->getFile());
            return new Response('Webhook Handled with error: {handleChargeRefunded}', 400);
        }
    }

    /**
     * WEBHOOK que se encarga de manejar el SCA notificando al usuario por correo electrónico
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentActionRequired(array $payload)
    {
        $subscription = Subscription::whereStripeId($payload['data']['object']['subscription'])->first();
        if ($subscription) {
            $subscription->stripe_status = "incomplete";
            $subscription->save();
        }

        if (is_null($notification = config('cashier.payment_notification'))) {
            return $this->successMethod();
        }

        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            if (in_array(Notifiable::class, class_uses_recursive($user))) {
                $payment = new \Laravel\Cashier\Payment(StripePaymentIntent::retrieve(
                    $payload['data']['object']['payment_intent'],
                    Cashier::stripeOptions()
                ));
                $user->notify(new $notification($payment));
            }
        }
        return $this->successMethod();
    }
}
