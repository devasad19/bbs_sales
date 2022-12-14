@extends('backend.layout.master')
@section('content')
<style>
    td,p {
        font-size: 1.5em;
        font-weight: 500;
    }
</style>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="noprintbtn">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Farmers Data Listing</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.index')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="text-muted">Farmers Data Listing</a>
                        </li>
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
                @include('alerts.alerts')
                <!--begin::Card-->
                <div class="row">
                    <div class="col-lg-12">
                        <!--begin::Card-->
                        <div class="card card-custom m-9">
                            <div class="card-header mt-5">
                                
                                <div class="col-lg-6 offset-lg-3 text-center mt-4" style="line-height: 100%">
                                    <p class="font-weight-bold">?????????????????????????????????????????? ???????????????????????? ???????????????</p>
                                    <p class="font-weight-bold">??????????????????????????? ?????????????????????????????????</p>
                                    <p class="font-weight-bold">???????????????????????? ?????????????????????????????? ??????????????????</p>
                                    <p class="font-weight-bold">????????????????????????????????? ?????????</p>
                                    <p class="font-weight-bold">?????????????????????????????? ?????????</p>
                                    <p class="font-weight-bold">???-??????/??? ????????????????????????, ????????????-????????????</p>
                                    <p class="font-weight-bold mt-4">???????????? ?????????????????? ?????????</p>
                                    <p class="mt-4">(Farmer Listing Form)</p>
                                </div>
                                <div class="col-3">
                                    <p class="font-weight-bold text-right">??????????????? ????????? - ???</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive ajax-data-container pt-3">
                                    <p style="font-size: 1.3em; font-weight: 500">??????????????? ?????????????????????:</p>
                                    <table class="table">
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">???????????????: <b>{{ $list->division ? $list->division->name_bn : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left">????????????: <b>{{ $list->district ? $list->district->name_bn : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left">??????????????????: <b>{{ $list->upazila ? $list->upazila->name_bd : '' }}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">?????????: <b>{{ $list->division ? $list->division->division_bbs_code : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left">?????????: <b>{{ $list->district ? $list->district->district_bbs_code : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left">?????????: <b>{{ $list->upazila ? $list->upazila->upazila_bbs_code : '' }}</b></td>
                                        </tr>
                                        <tr>
                                            {{-- <td style="border: 1px solid #000" align="left">???????????????????????? ????????????????????? ??????????????? ?????????: {{ $list->mouza ? $list->mouza->name_bn : '' }}</td> --}}
                                            <td style="border: 1px solid #000" align="left">?????? (????????????????????????): {{ $list->year }}</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">?????????????????? ??????????????? ???????????? ?????????: {{ $list->surveyNotification ? $list->surveyNotification->notification_start_data_field : '..............' }} ??????????????? ????????? {{ $list->surveyNotification ? $list->surveyNotification->notification_end_data_field : '..............' }}</td>
                                        </tr>
                                    </table>
                                    <table class="table">
                                        
                                        <tr>
                                            <td style="border: 1px solid #000"></td>
                                            <td style="border: 1px solid #000"></td>
                                            <td style="border: 1px solid #000"></td>
                                            <td colspan="3" style="border: 1px solid #000">?????????????????? ?????????????????? ???????????????</td>
                                            <td colspan="3" style="border: 1px solid #000"></td>
                                            <td colspan="3" style="border: 1px solid #000"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000">?????????????????? ??????</td>
                                            <td style="border: 1px solid #000">??????????????? ??????????????????: <br> ???????????? ????????????-??? <br> ??????????????? ????????????-???</td>
                                            <td style="border: 1px solid #000">???????????? ???????????????????????? ?????????, ??????????????? ????????? ??? ?????????????????? ???????????????</td>
                                            <td style="border: 1px solid #000">?????????/????????????????????? <br>????????????/???????????? <br> (???.?????? - ???.?????? ?????????)</td>
                                            <td style="border: 1px solid #000">?????????????????? <br> ????????????/???????????? <br> (???.?????? - ???.?????? ?????????)</td>
                                            <td style="border: 1px solid #000">??????/???????????? <br>????????????/???????????? <br> (???.?????? ??? ???????????????????????? ????????? ?????????)</td>
                                            
                                            <td style="border: 1px solid #000">????????????????????? ?????????</td>
                                            <td style="border: 1px solid #000">?????????????????? ?????????</td>
                                            <td style="border: 1px solid #000">????????????????????? ?????????</td>
                                            <td style="border: 1px solid #000">??????????????? ??????????????? ??????</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">???</td>
                                            <td style="border: 1px solid #000">??????</td>
                                        </tr>
                                        @php
                                            $count = 0;
                                        @endphp
                                        @foreach ($farmersDatas as $data)
                                            
                                            <tr>
                                                <td style="border: 1px solid #000">{{ $loop->index + 1}}</td>
                                                <td style="border: 1px solid #000">{{ $data->food_type == 1 ? 'Agriculture' : 'Non-Agriculture' }}</td>
                                                <td style="border: 1px solid #000"><b>Name: {{ ucfirst($data->farmers_name) }}</b>, <br> <b>Mobile: {{ $data->farmers_mobile }}</b></td>
                                                <td style="border: 1px solid #000">
                                                    @if ($data->farmers_class_division_type == 1)
                                                    {{ $data->land_amount }} acres 
                                                    @else 
                                                    -
                                                    @endif
                                                </td>
                                                <td style="border: 1px solid #000">
                                                    @if ($data->farmers_class_division_type == 2)
                                                    {{ $data->land_amount }} acres 
                                                    @else 
                                                    -
                                                    @endif
                                                </td>
                                                <td style="border: 1px solid #000">
                                                    @if ($data->farmers_class_division_type == 3)
                                                    {{ $data->land_amount }} acres 
                                                    @else 
                                                    -
                                                    @endif
                                                </td>
                                                

                                                <td style="border: 1px solid #000">
                                                    {{ $data->village_name }}
                                                </td>
                                                <td style="border: 1px solid #000">
        
                                                    @php
                                                        $values = explode(',', $data->permanent_crop_ids);
                                                    @endphp
                                                    @foreach ($values as $crop)
                                                        {{ $data->cropName($crop) }}
                                                        @if ($loop->last)
                                                        @else
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td style="border: 1px solid #000">
        
                                                    @php
                                                        $values = explode(',', $data->temporary_crop_ids);
                                                    @endphp
                                                    @foreach ($values as $crop)
                                                        {{ $data->cropName($crop) }}
                                                        @if ($loop->last)
                                                        @else
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td style="border: 1px solid #000">
                                                    @if($data->food_type == 1)
                                                    
                                                        {{ $count = $count + 1 }}
                                                    @else 
                                                    -
                                                    @endif
                                                </td>

                                            </tr>
                                            
                                        @endforeach
                                    </table>                                    
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mt-20">
                                        <div class="row">
                                            <div class="col-lg-4 ml-8">
                                                <p>{{ $list->surveyBy ? ucfirst($list->surveyBy->first_name). ' ' . ucfirst($list->surveyBy->middle_name). ' ' . ucfirst($list->surveyBy->last_name) : '' }}</p>
                                                @if ($list->surveyBy)
                                                    
                                                <img src="{{ asset('storage/signatures/'.$list->surveyBy->signature) }}" width="100" height="70">
                                                @endif
                                                
                                                <p>???????????? ????????????????????????????????? ????????????????????????, ??????????????? ????????? ??? ????????????</p>
                                                <p>??????????????? : {{ $list->updated_at->format('d-m-Y') }}</p>
                                            </div>
                                            <div class="col-lg-3"></div>
                                            <div class="col-lg-4 text-right mr-8">
                                                
                                                <p>{{ $list->createdBy ? ucfirst($list->createdBy->first_name). ' ' . ucfirst($list->createdBy->middle_name). ' ' . ucfirst($list->createdBy->last_name) : '' }}</p>
                                                @if ($list->createdBy)
                                                    
                                                <img src="{{ asset('storage/signatures/'.$list->createdBy->signature) }}" width="100" height="70">
                                                @endif
                                                
                                                <p>?????????????????????????????? ???????????????????????? ??? ?????????</p>
                                                <p>??????????????? : {{ $list->created_at->format('d-m-Y') }}</p>
                                            </div>
                                        </div>
                                        
                                        <p class="text-center">?????????????????? ????????? ?????????????????? ????????????????????????????????? ??? ??????????????? ???????????? ???????????????????????? ??????????????????</p>
                                        <p>???. ???????????????????????? ????????????????????? ????????????????????? ?????????????????? ????????? ?????? ????????? ??????????????? ???????????????????????? ????????? ?????????????????? ????????? ????????? ??????????????? ????????????????????? ???????????? ???????????? ???????????????????????? ??????????????? ????????? ????????????????????? ???????????????????????? ?????? ??????????????? ????????? ??????????????? ????????????????????? ?????? ????????? ???????????? ???????????? ???????????????????????? ???????????? ???????????? ??????????????????????????? ??????????????? ????????? ???????????? ????????????????????? ???????????????????????? ??????????????? </p>
                                        <p>???. ?????????????????? ????????? ???????????? ???????????? ?????? ????????? ????????? ??? ????????? ??????????????? ???????????? ????????? ??? ????????? ????????? ??????????????? ?????????????????? ???????????? ??? ??? ???????????? ???????????????????????? ????????? ??? ?????????????????? ????????????????????? ?????????????????? ???????????? ???,??? ??? ??? ??? ??????????????? ????????????????????????????????? ???????????? ???????????? ?????????????????? ???????????? ???????????????????????? ????????? ?????????????????? ?????????????????? ????????? ???????????? ??????????????? ???????????? ?????????????????? ???.?????? ????????? ??? ??????????????? ???.?????? ??????????????? ???????????? </p>
                                        <p>???. ??? ???????????? ???????????????????????? ????????? ??????????????? ??? ?????????????????? ???????????? (???????????????-???) ????????? ????????????????????? ????????? ?????????????????? ???????????? (???????????????-???) ?????? ???????????? ????????????????????? ????????? ???????????? </p>
                                        <button class="float-right btn btn-primary" id="noprintbtn" onclick="window.print()">Print</button>
                                    </div>
                                    

                                    
                                </div>

                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
</div>
@endsection