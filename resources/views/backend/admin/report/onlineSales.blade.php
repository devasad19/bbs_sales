@extends('backend.layout.master')
<style>
    @media print {
        #buttons, #kt_subheader, .noprint {
            display: none !important;
        }
    }
</style>
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Page Heading-->
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <!--begin::Page Title-->
                        <h5 class="text-dark font-weight-bold my-1 mr-5">Report</h5>
                        <!--end::Page Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.index') }}" class="text-muted">Dashboard</a>
                            </li>
                            @can('report')
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.report.onlineSales') }}" class="text-muted">Online Sales</a>
                                </li>
                            @endcan
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--session msg-->
                @include('alerts.alerts')
                <!--begin::Card-->
                <div class="row">
                    <div class="col-12">
                        <!--begin::Card-->
                        <div class="card card-custom example example-compact">

                            <div class="card-header">
								<h3 class="card-title">Online Sales Report</h3>
                                {{-- <div class="d-flex align-items-center">                                    
                                    <input type="text" data-url="{{ route('admin.searchAjax',['type'=> 'dataSales']) }}" class="form-control form-control-lg form-control-solid ajax-data-search" name="q" placeholder="Service/Item Name">
                                </div> --}}
							</div>

                            <div class="form-group card-body mb-0 pb-0">
                    
                                <form class="form-inline" action="{{route('admin.report.onlineSalesFilter')}}" method="POST">
                                    @csrf
                                    @if (Auth::user()->role_id == 11)
                                        <div class="form-group">
                                            <label class="pr-2 pl-2" for="fromDate">From</label>
                                            <input class="form-control" type="date" name="fromDate" id="fromDate">
                                        </div>
                                        <div class="form-group">
                                            <label class="pr-2 pl-2" for="toDate">To</label>
                                            <input class="form-control" type="date" name="toDate" id="toDate">
                                        </div>
                                        <button type="submit" class="btn btn-success ml-2">Submit</button>
                                    @else
                                        <div class="form-group">
                                            <label class="mr-3 mb-0 d-none d-md-block">Service Name:</label>
                                            <select class="form-control select2 ajax-data-search2" name="service_id">
                                                <option value="">--Select Service--</option>
                                                @foreach ($services as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name_en }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="pr-2 pl-2" for="fromDate">From</label>
                                            <input class="form-control" type="date" name="fromDate" id="fromDate">
                                        </div>
                                        <div class="form-group">
                                            <label class="pr-2 pl-2" for="toDate">To</label>
                                            <input class="form-control" type="date" name="toDate" id="toDate">
                                        </div>
                                        <button type="submit" class="btn btn-success ml-2">Submit</button>
                                    @endif
                                    
                                </form>
                            </div>
                            <style>
                                td{
                                    white-space: nowrap;
                                }
                            </style>
                            <div class="card-body">
								<div class="table-responsive ajax-data-container pt-3">
									<table class="table table-separate table-head-custom table-checkable table-striped">
                                        <thead>
                                            <tr>
                                                <th >Application ID</th>
                                                <th>Application Date</th>
                                                <th width="200">Sale Year</th>
                                                <th class="text-left">Client Name</th>
                                                {{-- <th class="text-left">Service Name</th> --}}
                                                {{-- <th>Approve Date</th> --}}
                                                <td>Address</td>
                                                <td>Sold Micro Data</td>
                                                <th>Amount Sol </th>
                                                <th>Month</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                                $totalBDT = 0;
                                                $totalUSD = 0;
                                            @endphp
                                            @foreach ($applications as $application)
                                                @php
                                                    $country = $application->user ? $application->user->country_id : '';
                                                @endphp
                                                <tr>
                                                    {{-- <td>{{$i}}</td> --}}
                                                    <td>{{ $application->application_id }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($application->created_at)) }}</td>
                                                    <td>{{$application->created_at->format('Y')}}</td>
                                                    <td align="left">
                                                        {{ $application->user ? $application->user->first_name .' '. $application->user->middle_name .' '. $application->user->last_name : '' }}
                                                    </td>
                                                    <td>{{$application->user ? $application->user->present_address : ''}}</td>
                                                    <td>
                                                        @foreach ($application->allApplicationServices as $item)
                                                            {{$item->serviceItem ? $item->serviceItem->item_name_en : ''}}
                                                        @endforeach

                                                    </td>
                                                    {{-- <td align="left">
                                                        {{ 
                                                            $application->applicationServices ? 
                                                            $application->applicationServices->service ? 
                                                            $application->applicationServices->service->name_en : '' : '' 
                                                        }}
                                                    </td> --}}
                                                    {{-- <td>{{ date('d-m-Y', strtotime($application->updated_at)) }}</td> --}}
                                                    @if ($country == 19)
                                                        <td>{{$application->final_total}} BDT</td>
                                                        @php
                                                            $totalBDT += $application->final_total;
                                                        @endphp
                                                    @else
                                                        <td>{{$application->final_total}} USD</td>
                                                        @php
                                                            $totalUSD += $application->final_total;
                                                        @endphp
                                                    @endif
                                                    <td>{{$application->created_at->format('M')}}</td>

                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Basic example">
                                                            <a class="btn btn-sm btn-warning" href="{{route('admin.application.show', $application->id)}}">Details</a>
                                                            <a class="btn btn-sm btn-primary" href="{{route('admin.application.invoice', $application->id)}}">Invoice</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @php
                                                    $i++;
                                                @endphp
                                            @endforeach
                                            <tr>
                                                <td colspan="6" align="right">Total BDT</td>
                                                <td>{{$totalBDT}} TK</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" align="right">Total USD</td>
                                                <td>{{$totalUSD}} $</td>
                                            </tr>
                                        </tbody>
                                    </table>
								</div>
                                {{$applications->links()}}
							</div>

                            <div class="card-footer">
								{{-- <a href="{{route('admin.report.digitalDataPreview')}}" class="btn btn-secondary float-right">Preview</a> --}}
                                <button type="button" id="noprintbtn" class="btn btn-primary font-weight-bold float-right"
                                        onclick="return printDiv('printReport');">Print</button>
							</div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    {{-- Print section --}}
    <div class="content d-flex flex-column flex-column-fluid" style="display:none !important;" id="printReport">
        <!--begin::Subheader-->
        
        <!--end::Subheader-->
        <div class="container-fluid">
            <!-- begin::Card-->
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <!-- begin: Invoice-->
                <!-- begin: Invoice header-->
                <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                    <div class="col-md-9">
                        <div class="d-flex justify-content-between">
                            
                            <img class="display-4 font-weight-boldest mb-10 mr-10" height="100%" width="15%" src="{{ asset('assets/media/logos/logo2.png') }}" alt="">
                            
                            
                            <span class="pt-10" style="font-size: 18px;">
                                গণপ্রজাতন্ত্রী বাংলাদেশ সরকার
                                <br>
                                বাংলাদেশ পরিসংখ্যান ব্যুরো
                                <br>
                                মিরপুর, ঢাকা - ১২১৬
                                <br>
                                বাংলাদেশ ।
                            </span>
                        </div>
                        <div class="border-bottom w-100"></div>
                        <div class="d-flex justify-content-between pt-6">
                            <span> <span style="font-weight: bold">Report Name:</span> Digital Data Downloads </span>
                            <span>Date: {{date('d-M-Y')}}</span>
                        </div>
                    </div>
                </div>
                <!-- end: Invoice header-->
                
                <!-- begin: Invoice footer-->
                <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table table-separate table-head-custom table-checkable table-striped">
                                <thead>
                                    <tr>
                                        <th >Application ID</th>
                                        <th width="200">Sale Year</th>
                                        <th class="text-left">Client Name</th>
                                        {{-- <th class="text-left">Service Name</th> --}}
                                        {{-- <th>Approve Date</th> --}}
                                        <td>Address</td>
                                        <td>Sold Micro Data</td>
                                        <th>Amount Sol </th>
                                        <th>Month</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                        $totalBDT = 0;
                                        $totalUSD = 0;
                                    @endphp
                                    @foreach ($applications as $application)
                                        @php
                                            $country = $application->user ? $application->user->country_id : '';
                                        @endphp
                                        <tr>
                                            {{-- <td>{{$i}}</td> --}}
                                            <td>{{ $application->application_id }}</td>
                                            <td>{{$application->created_at->format('Y')}}</td>
                                            <td align="left">
                                                {{ $application->user ? $application->user->first_name .' '. $application->user->middle_name .' '. $application->user->last_name : '' }}
                                            </td>
                                            <td>{{$application->user ? $application->user->present_address : ''}}</td>
                                            <td>
                                                @foreach ($application->allApplicationServices as $item)
                                                    {{$item->serviceItem ? $item->serviceItem->item_name_en : ''}}
                                                @endforeach

                                            </td>
                                            {{-- <td align="left">
                                                {{ 
                                                    $application->applicationServices ? 
                                                    $application->applicationServices->service ? 
                                                    $application->applicationServices->service->name_en : '' : '' 
                                                }}
                                            </td> --}}
                                            {{-- <td>{{ date('d-m-Y', strtotime($application->updated_at)) }}</td> --}}
                                            @if ($country == 19)
                                                <td>{{$application->final_total}} BDT</td>
                                                @php
                                                    $totalBDT += $application->final_total;
                                                @endphp
                                            @else
                                                <td>{{$application->final_total}} USD</td>
                                                @php
                                                    $totalUSD += $application->final_total;
                                                @endphp
                                            @endif
                                            <td>{{$application->created_at->format('M')}}</td>
                                        </tr>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="6" align="right">Total BDT</td>
                                        <td>{{$totalBDT}} TK</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" align="right">Total USD</td>
                                        <td>{{$totalUSD}} $</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- {{$items->links()}} --}}
                    </div>
                </div>
                <!-- end: Invoice footer-->
                <!-- begin: Invoice action-->
                {{-- <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0" id="buttons">
                    <div class="col-md-9">
                        <div class="d-flex justify-content-between float-right">
                            <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Print</button>
                        </div>
                    </div>
                </div> --}}
                <!-- end: Invoice action-->
                <!-- end: Invoice-->
            </div>
        </div>
        <!-- end::Card-->
        </div>
    </div>

@endsection


@push('stackScript')
    <script type="text/javascript">
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            // document.body.innerHTML = originalContents;
        }
    </script>
@endpush