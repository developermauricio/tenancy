<div class="table-responsive mt-3">
    <!-- .table -->
    @if(auth()->user()->subscription('main'))
        <table class="table subscription-table">
            <thead>
            <tr>
                <th class="text-center" scope="col">{{ __("Plan") }}</th>
                <th class="text-center" scope="col">{{ __("Alta") }}</th>
                <th class="text-center" scope="col">{{ auth()->user()->subscription('main')->ends_at ? __("Finaliza en") : __("Estado") }}</th>
                <th class="text-center" scope="col">{{ __("Cancelar / Reanudar") }}</th>
            </tr>
            </thead>

            <tbody>
            <td class="text-center">{{ strtoupper(auth()->user()->subscription('main')->stripe_plan) }}</td>
            <td class="text-center">{{ auth()->user()->subscription('main')->created_at->format('d/m/Y') }}</td>

            {{-- si la suscripción no está activa por stripe probablemente es que se deba confirmar el pago SCA --}}
            @if(auth()->user()->hasIncompletePayment('main'))
                <td class="text-center">{!! __("Pendiente de confirmación, pulsa <a href=':link'>aquí</a> para confirmar", [
                        "link" => route('cashier.payment', auth()->user()->subscription('main')->latestPayment()->id)
                    ]) !!}
                </td>
            @else
                <td class="text-center">{{ auth()->user()->subscription('main')->ends_at ? auth()->user()->subscription('main')->ends_at->format('d/m/Y') : __("Suscripción activa") }}</td>
            @endif

            <td class="text-center">
                @if(auth()->user()->subscription('main')->ends_at)
                    @if( ! auth()->user()->subscribed('main'))
                        {{ __("El plan ya no está vigente, ¡contrata uno nuevo!") }}
                    @else
                        @if(auth()->user()->hasIncompletePayment('main'))
                            <button class="btn btn-info" disabled>
                                {{ __("Pendiente de confirmación") }}
                            </button>
                        @else
                            <form action="{{ route('plans.resume') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan" value="{{ auth()->user()->subscription('main')->name }}" />
                                <button class="btn btn-success">
                                    {{ __("Reanudar") }}
                                </button>
                            </form>
                        @endif
                    @endif
                @else
                    @if(auth()->user()->hasIncompletePayment('main'))
                        <button class="btn btn-info" disabled>
                            {{ __("Pendiente de confirmación") }}
                        </button>
                    @else
                        <form action="{{ route('plans.cancel') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ auth()->user()->subscription('main')->name }}" />
                            <button class="btn btn-danger">
                                {{ __("Cancelar renovación automática") }}
                            </button>
                        </form>
                    @endif
                @endif
            </td>
            </tbody>
        </table>
    @else
        <div class="alert alert-danger text-center">
            {{ __("Actualmente no tienes contratada ninguna suscripción") }}
        </div>
    @endif
</div>
