@extends('multiauth::layouts.admin')
@section('content')
<div class="container pages-content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">{{ ucfirst(config('multiauth.prefix')) }} Dashboard</div> --}}
                <div class="card-body">
                    @include('multiauth::message')

                    <div class="row justify-content-between page-row">
                        <div class="col-sm">
                            <div class="alert alert-warning" role="alert">
                                @lang('The following data is for the last 30 days compared to the previous 30.')
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-xl-3 col-lg-6">
                            <div class="card l-bg-green-dark">
                                <div class="card-statistic-3 p-4">
                                    <div class="card-icon card-icon-large"><i class="fas fa-truck-loading"></i></div>
                                    <div class="mb-4">
                                        <h5 class="card-title mb-0">@lang('Trips')</h5>
                                    </div>
                                    <div class="row align-items-center mb-2 d-flex">
                                        <div class="col-8">
                                            <h2 class="d-flex align-items-center mb-0">
                                                {{ $trips }}
                                            </h2>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span>
                                                @if ($difTrips > 0)+@endif{{ $difTrips }}
                                                @if ($difTrips > 0)
                                                <i class="fa fa-arrow-up"></i>
                                                @endif
                                                @if ($difTrips < 0) <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                    @if ($difTrips == 0)
                                                    <i class="fa fa-equals"></i>
                                                    @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card l-bg-cherry">
                                <div class="card-statistic-3 p-4">
                                    <div class="card-icon card-icon-large"><i class="fas fa-car-crash"></i></div>
                                    <div class="mb-4">
                                        <h5 class="card-title mb-0">@lang('Crashes')</h5>
                                    </div>
                                    <div class="row align-items-center mb-2 d-flex">
                                        <div class="col-8">
                                            <h2 class="d-flex align-items-center mb-0">
                                                {{ $crashes }}
                                            </h2>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span>
                                                @if ($difCrashes > 0)+@endif{{ $difCrashes }}
                                                @if ($difCrashes > 0)
                                                <i class="fa fa-arrow-up"></i>
                                                @endif
                                                @if ($difCrashes < 0) <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                    @if ($difCrashes == 0)
                                                    <i class="fa fa-equals"></i>
                                                    @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card l-bg-blue-dark">
                                <div class="card-statistic-3 p-4">
                                    <div class="card-icon card-icon-large"><i class="fas fa-tools"></i></div>
                                    <div class="mb-4">
                                        <h5 class="card-title mb-0">@lang('Maintenance Costs')</h5>
                                    </div>
                                    <div class="row align-items-center mb-2 d-flex">
                                        <div class="col-8">
                                            <h2 class="d-flex align-items-center mb-0">
                                                {{ $maint }}€
                                            </h2>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span>
                                                @if ($difMaint > 0)+@endif{{ $difMaint }}€
                                                @if ($difMaint > 0)
                                                <i class="fa fa-arrow-up"></i>
                                                @endif
                                                @if ($difMaint < 0) <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                    @if ($difMaint == 0)
                                                    <i class="fa fa-equals"></i>
                                                    @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card l-bg-orange-dark">
                                <div class="card-statistic-3 p-4">
                                    <div class="card-icon card-icon-large"><i class="fas fa-gas-pump"></i>"></i></div>
                                    <div class="mb-4">
                                        <h5 class="card-title mb-0">@lang('Fuel Costs')</h5>
                                    </div>
                                    <div class="row align-items-center mb-2 d-flex">
                                        <div class="col-8">
                                            <h2 class="d-flex align-items-center mb-0">
                                                {{ $fuel }}€
                                            </h2>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span>
                                                @if ($difFuel > 0)+@endif{{ $difFuel }}€
                                                @if ($difFuel > 0)
                                                <i class="fa fa-arrow-up"></i>
                                                @endif
                                                @if ($difFuel < 0) <i class="fa fa-arrow-down"></i>
                                                    @endif
                                                    @if ($difFuel == 0)
                                                    <i class="fa fa-equals"></i>
                                                    @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.row-->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection