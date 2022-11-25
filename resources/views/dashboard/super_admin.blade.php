@extends('layouts.admin')
@section('page-title')
    {{__('Dashboard')}}
@endsection
@push('script-page')
    <script>
        var SalesChart = (function () {
            var $chart = $('#chart-sales');

            function init($this) {
                var salesChart = new Chart($this, {
                    type: 'line',
                    options: {
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    color: Charts.colors.gray[200],
                                    zeroLineColor: Charts.colors.gray[200]
                                },
                                ticks: {}
                            }]
                        }
                    },
                    data: {
                        labels:{!! json_encode($chartData['label']) !!},
                        datasets: [{
                            label: '{{__("Order")}}',
                            data:{!! json_encode($chartData['data']) !!}
                        }]
                    }
                });
                $this.data('chart', salesChart);
            };
            if ($chart.length) {
                init($chart);
            }
        })();
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-box height-95">
                <div class="icon-box blue-bg">{{$user['total_user']}}</div>
                <div class="number-icon">
                    <div class="card-right-title pt-2">
                        <h4 class="float-left">{{__('Total Users')}}</h4>
                        <h5 class="float-right">{{__('')}} : <span class="text-dark">{{number_format($user['total_paid_user'])}}</span></h5>
                    </div>
                </div>
                <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-box height-95">
                <div class="icon-box green-bg">{{$user['total_orders']}}</div>
                <div class="number-icon">
                    <div class="card-right-title pt-2">
                        <h4 class="float-left">{{__('Total Appointments')}}</h4>
                        <h5 class="float-right">{{__('Total ')}} : <span class="text-dark">{{number_format($user['total_orders_price'])}}</span></h5>
                    </div>
                </div>
                <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-box height-95">
                <div class="icon-box red-bg">{{$user['total_plan']}}</div>
                <div class="number-icon">
                    <div class="card-right-title pt-2">
                        <h4 class="float-left">{{__('Total cases reported')}}</h4>
                        <h5 class="float-right">{{__('')}} : <span class="text-dark">{{number_format($user['most_purchese_plan'])}}</span></h5>
                    </div>
                </div>
                <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <h4 class="h4 font-weight-400 float-left">{{__('Recent Appointment')}}</h4>
                <h6 class="last-day-text">{{__('Last 7 Days')}}</h6>
            </div>
            <div class="card bg-none">
                <canvas id="chart-sales" class="chart-canvas" height="300"></canvas>
            </div>
        </div>
    </div>
@endsection


