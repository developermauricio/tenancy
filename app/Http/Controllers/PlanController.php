<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\User;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Exceptions\IncompletePayment;

use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     *
     * LISTADO DE PLANES PARA CONTRATAR
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $plans = Plan::all();
        $currentPlan = auth()->user()->subscription('main');
        $priceCurrentPlan = null;
        if ($currentPlan) {
            if ($currentPlan->active()) {
                $plan = Plan::whereSlug($currentPlan->stripe_plan)->first();
                $priceCurrentPlan = $plan->amount;
            }
        }
        return view("system.plans", compact("plans", "priceCurrentPlan"));
    }

    /**
     *
     * FORMULARIO ALTA PLANES
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        return view("system.plans_form");
    }

    /**
     *
     * CREAR NUEVOS PLANES EN STRIPE Y BD
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function store() {
        $this->validate(request(), [
            'plan_name' => 'required|unique:plans,nickname|string|max:200',
            'plan_price' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $plan = \Stripe\Plan::create([
                'currency' => env("CASHIER_CURRENCY"),
                'interval' => env("CASHIER_INTERVAL"),
                "product" => [
                    "name" => request('plan_name')
                ],
                'nickname' => request('plan_name'),
                'id' => Str::slug(request('plan_name')),
                'amount' => request('plan_price') * 100,
            ]);
            if ($plan) {
                Plan::create([
                    'product' => $plan->product,
                    'nickname' => request('plan_name'),
                    'amount' => request('plan_price'),
                    'slug' => $plan->id,
                    's3' => request('s3') ? true : false
                ]);
            }
            DB::commit();
            session()->flash('message', ['success', __('Plan dado de alta correctamente')]);
            return redirect(route('plans.index'));
        } catch (\Exception $exception) {
            DB::rollBack();
            $plan = \Stripe\Plan::retrieve(Str::slug(request('plan_name')));
            if ($plan) {
                $plan->delete();
            }
            session()->flash('message', ['danger', $exception->getMessage()]);
            return back()->withInput();
        }
    }

    /**
     *
     * CONTRATAR NUEVAS SUSCRIPCIONES
     *
     * @param $hash
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function purchase () {
//        if ( ! auth()->user()->hasPaymentMethod()) {
//            return back()->with('message', ['danger', __('No sabemos cómo has llegado hasta aquí, ¡añade una tarjeta para contratar un plan!')]);
//        }

        $planId = (int) request("plan");
        $fqdn = sprintf('%s.%s', request('fqdn'), env('APP_DOMAIN'));
        $this->validate(request(), [
            'plan' => 'required',
            'fqdn' => ['required', 'string', 'min:2', 'max:20', Rule::unique('hostnames')->where(function ($query) use ($fqdn) {
                return $query->where('fqdn', $fqdn);
            })],
        ],[
            'fqdn.required' => "El dominio es requerido",
            'fqdn.unique' => "Ese nombre de dominio ya está en uso"
        ]);

        //obtenemos el plan que se está intentando contratar
        $plan = Plan::find($planId);

        try {
            //nos aseguramos que el plan a contratar es el correcto
            if ($planId === $plan->id) {
                $user = User::find(auth()->id());
                $user->fqdn = request('fqdn');
                $user->save();
//                $user->newSubscription('main', $plan->slug)->create();

                $website = new Website;
                $website->uuid = $user->fqdn;
                app(WebsiteRepository::class)->create($website);
                $hostname = new Hostname;
                $hostname->fqdn = $fqdn;
                $hostname->user_id = $user->id;
                $hostname = app(HostnameRepository::class)->create($hostname);
                app(HostnameRepository::class)->attach($hostname, $website);

                return redirect(route("plans.index"))->with('message', ['info', __('Te has suscrito al plan ' . $plan->nickname . ' correctamente, recuerda revisar tu correo electrónico por si es necesario confirmar el pago')]);
            } else {
                return back()->with('message', ['info', __('El plan seleccionado parece no estar disponible')]);
            }
        } catch (IncompletePayment $exception) {
            session()->flash('message', ['success', __('Te has suscrito al plan ' . $plan->nickname . ' correctamente, ya puedes disfrutar de todas las ventajas')]);
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('plans.index')]
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }

        return abort(401);
    }


    /**
     *
     * REAUNUDAR SUSCRIPCIONES CANCELADAS PREVIAMENTE
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resumeSubscription () {
        $subscription = request()->user()->subscription(request('plan'));
        if ($subscription->cancelled()) {
            request()->user()->subscription(request('plan'))->resume();
            return back()->with('message', ['success', __("Has reanudado tu suscripción correctamente")]);
        }
        return back()->with('message', ['danger', __("La suscripción no se puede reanudar, consulta con el administrador")]);
    }

    /**
     *
     * CANCELAR SUSCRIPCIONES PARA QUE NO SE RENUEVEN AUTOMÁTICAMENTE
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription () {
        auth()->user()->subscription(request('plan'))->cancel();
        return back()->with('message', ['success', __("La suscripción se ha cancelado correctamente")]);
    }
}
