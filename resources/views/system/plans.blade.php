@extends('layouts.app')

@section('content')
    <div class="container py-5">
        @if(!$currentWebsite && !auth()->user()->isAdmin())
            @if(! auth()->user()->hasPaymentMethod())
                <div class="alert alert-danger text-center">
                    <span class="fas fa-exclamation-circle"></span> {{ __("Todavía no has vinculado ninguna tarjeta a tu cuenta") }} <a href="{{ route('billing.credit_card_form') }}">{{ __("Házlo ahora") }}</a>
                </div>
            @endif
        @endif
        @if(count($plans))
            <section class="pricing py-5">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="container">
                    <div class="row">
                        @foreach($plans as $plan)
                            <div class="col-lg-4">
                                <div class="card mb-5 mb-lg-0">
                                    <form action="{{ route("plans.purchase") }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $plan->id }}">

                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <input id="fqdn" class="form-control" name="fqdn" value="{{ old('fqdn') }}" placeholder="Nombre de dominio">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title text-muted text-uppercase text-center">{{ __($plan->nickname) }}</h5>
                                            <h6 class="card-price text-center">{{ __(":amount€", ["amount" => $plan->amount]) }}<span class="period">{{ __("/mensual") }}</span></h6>
                                            <hr>
                                            <ul class="fa-ul">
                                                <li><span class="fa-li"><i class="fas fa-check"></i></span>{{ __("Tu propio subdominio") }}</li>
                                                <li><span class="fa-li"><i class="fas fa-check"></i></span>{{ __("Tu propia base de datos") }}</li>
                                                @if($plan->s3)
                                                    <li><span class="fa-li"><i class="fas fa-check"></i></span>{{ __("Almacenamiento en S3") }}</li>
                                                @else
                                                    <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>{{ __("Almacenamiento en S3") }}</li>
                                                @endif

                                                @if($plan->slug === 'pro')
                                                    <li><span class="fa-li"><i class="fas fa-check"></i></span>{{ __("Soporte Premium") }}</li>
                                                @else
                                                    <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>{{ __("Soporte Premium") }}</li>
                                                @endif
                                            </ul>

                                            @if(!$currentWebsite && !auth()->user()->isAdmin())
                                                @if( ! auth()->user()->hasIncompletePayment('main'))
                                                    @if(auth()->user()->subscribed('main'))
                                                        @if(auth()->user()->subscription('main')->stripe_plan === $plan->slug)
                                                            <button type="button" disabled class="btn btn-block btn-primary text-uppercase">{{ __("Tu plan actual") }}</button>
                                                        @else
                                                            @if($priceCurrentPlan < $plan->amount)
                                                                <button disabled class="btn btn-block btn-dark text-uppercase">{{ __("No es posible cambiar de plan") }}</button>
                                                            @else
                                                                <button type="button" disabled class="btn btn-block btn-dark text-uppercase">{{ __("No es posible bajar") }}</button>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <button type="submit" class="btn btn-block btn-dark text-uppercase">{{ __("Suscribirme") }}</button>
                                                    @endif
                                                @else
                                                    @if(auth()->user()->subscription('main')->stripe_plan === $plan->slug)
                                                        <a class="btn btn-block btn-info text-uppercase" href="{{ route('cashier.payment', auth()->user()->subscription('main')->latestPayment()->id) }}">
                                                            {{ __("Confirma tu pago aquí") }}
                                                        </a>
                                                    @else
                                                        <button type="button" disabled class="btn btn-block btn-primary text-uppercase">{{ __("Esperando...") }}</button>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            @if(!$currentWebsite && auth()->user()->isAdmin())
                <div class="alert alert-danger text-center">
                    <span class="fas fa-exclamation-circle"></span> {{ __("No hay ningún plan disponible todavía") }} <a href="{{ route('plans.create') }}">{{ __("Crea uno ahora ahora") }}</a>
                </div>
            @endif
        @endif

        @if(!$currentWebsite && !auth()->user()->isAdmin())
            {{-- tabla suscripción actual! --}}
            @include('system.current_subscription')
        @endif
    </div>
@endsection
