@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <ul role="tablist" class="nav bg-light nav-pills rounded-pill nav-fill mb-3">
                        <li class="nav-item">
                            <a data-toggle="pill" href="#nav-tab-card" class="nav-link active rounded-pill">
                                <i class="fa fa-gem"></i>
                                {{ __("Crea un nuevo plan en :app", ["app" => env("APP_NAME")]) }}
                            </a>
                        </li>
                    </ul>
                    <!-- End -->

                    <div class="tab-content">
                        <div id="nav-tab-card" class="tab-pane fade show active">
                            <form action="{{ route("plans.store") }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="plan_name">{{ __("Nombre del plan") }}</label>
                                    <div class="input-group">
                                        <input
                                                type="text"
                                                name="plan_name"
                                                placeholder="{{ __("Nombre del plan") }}"
                                                class="form-control"
                                                required
                                                value="{{
                                                old('plan_name') ? old('plan_name') : ""
                                            }}"
                                        />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plan_price">{{ __("Precio del plan") }}</label>
                                    <div class="input-group">
                                        <input
                                                type="text"
                                                name="plan_price"
                                                placeholder="{{ __("Precio del plan") }}"
                                                class="form-control"
                                                required
                                                value="{{
                                                old('plan_price') ? old('plan_price') : ""
                                            }}"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="s3" id="s3" {{ old('s3') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="s3">
                                            {{ __('Disco S3') }}
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="subscribe btn btn-primary btn-block rounded-pill shadow-sm">{{ __("Crear nuevo plan") }}</button>
                            </form>
                        </div>
                        <!-- End -->
                    </div>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>
@endsection
