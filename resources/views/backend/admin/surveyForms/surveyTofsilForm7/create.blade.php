
@extends('backend.layout.master')

@push('css')
    <style> 
        .table th, .table td{
            border-top: none !important;
            font-size: 1.5em;
        }
        .table thead th{
            font-size: 1.5em;
        }
        tbody#tbl_posts_body label {
            height: 40px;
        }
    </style>
@endpush

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
                        <h5 class="text-dark font-weight-bold my-1 mr-5">প্রধান ফসলের মূল্য ও উৎপাদন খরচ জরিপ তফসিল (তফসিল-৭)</h5>
                        <!--end::Page Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.index') }}" class="text-muted">ড্যাশবোর্ড</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="text-muted">প্রধান ফসলের মূল্য ও উৎপাদন খরচ জরিপ তফসিল (তফসিল-৭)</a>
                            </li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->
            </div>
        </div>

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid">
                @include('alerts.alerts')
                <div class="row">
                    <div class="col-lg-12">
                        <!--begin::Card-->
                        <div class="card card-custom example example-compact">
                            <div class="card card-custom gutter-b example example-compact">
                                <div class="card-header">
                                    <h3 class="card-title">প্রধান ফসলের মূল্য ও উৎপাদন খরচ জরিপ তফসিল (তফসিল-৭)</h3>
                                    
                                    <div class="card-toolbar">

                                    </div>
                                </div>
                                <!--begin::Form-->
                                <form action="{{ route('admin.surveyTofsilForm7.store') }}" method="post">
                                    @csrf

                                    {{-- hidden fields --}}
                                    @if (!empty($processList))
                                        <input type="hidden" name="survey_process_list_id" value="{{ $processListId }}" />
                                        <input type="hidden" name="survey_notification_id" value="{{ $processList->survey_notification_id }}" />
                                        <input type="hidden" name="division_id" value="{{ $processList->division_id }}" />
                                        <input type="hidden" name="district_id" value="{{ $processList->district_id }}" />
                                        <input type="hidden" name="upazila_id" value="{{ $processList->upazila_id }}" />
                                        <input type="hidden" name="union_id" value="{{ $processList->union_id }}" />
                                        <input type="hidden" name="mouja_id" value="{{ $processList->mouja_id }}" />
                                    @endif

                                    @if (isset($surveyNotification))
                                        <input type="hidden" id="crop_name" value="{{ $surveyNotification->crop ? $surveyNotification->crop->name_en : '' }}">
                                    @endif
                                    
                                    <div class="card-body">
                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">সাধারণ তথ্য</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">

                                                        <div class="form-group row">
                                                            <label for="farmer_id"style="font-size: 1.5em; font-weight: bold;"><b>বর্তমান ভূমি খন্ড ব্যাবহারকারী/চাষির নাম : </b><span class="text-danger">*</span></label>

                                                            <select class="select2 form-control form-control-lg" id="farmer_id" name="farmer_id" required>
                                                                <option value="">--ব্যাবহারকারী/চাষির নাম নির্বাচন করুন--</option>

                                                                @if (isset($surveyList->surveyCompilationCollectForms))
                                                                    @foreach ($surveyList->surveyCompilationCollectForms as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->farmers_name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">

                                                        <div class="form-group row">
                                                            <label for="pot_of_land"style="font-size: 1.5em; font-weight: bold;"><b> ভূমি খন্ডের সংক্ষেত নংঃ </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="ভূমি খন্ডের সংক্ষেত নং" name="pot_of_land" id="pot_of_land" required/>
                                                        
                                                        </div>

                                                    </div>

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;"> 
                                            
                                                        <div class="form-group row">
                                                            <label for="crops_id" style="font-size: 1.5em; font-weight: bold;"><b> ফসলের নামঃ </b><span class="text-danger">*</span></label>
                                                            
                                                            @if (isset($surveyNotification))
                                                                @if ($surveyNotification->crop_id != '')
                                                                    <input type="hidden" id="crops_id" name="crops_id" value="{{ $surveyNotification->crop_id }}">
                                                                    <input type="text" class="form-control form-control-lg" value="{{ $surveyNotification->crop ? $surveyNotification->crop->name_bn : '' }}" readonly>
                                                                @else
                                                                    <select class="select2 form-control form-control-lg" id="crops_id" name="crops_id" required>
                                                                        <option value="">--ফসল নির্বাচন করুন--</option>

                                                                        @foreach ($crops as $crop)
                                                                            <option value="{{ $crop->id }}">{{ $crop->name_en }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @endif

                                                            @else
                                                                <select class="select2 form-control form-control-lg" id="crops_id" name="crops_id" required>
                                                                    <option value="">--ফসল নির্বাচন করুন--</option>

                                                                    @foreach ($crops as $crop)
                                                                        <option value="{{ $crop->id }}">{{ ucfirst($crop->name_en) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">

                                                        <div class="form-group row">
                                                            <label for="crop_varieties" style="font-size: 1.5em; font-weight: bold;"><b>ফসলের জাতঃ </b><span class="text-danger">*</span></label>

                                                            <select class="form-control" id="crop_varieties" name="crop_varieties" required>
                                                                <option value="">--বাছাই করুন--</option>
                                                                <option value="1">দেশি</option>
                                                                <option value="2">উফশী</option>
                                                                <option value="3">হাইব্রিড</option>
                                                            </select>
                                                        </div>

                                                    </div>

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">

                                                        <div class="form-group row">
                                                            <label for="collection_start_date" style="font-size: 1.5em; font-weight: bold;"><b>তথ্য সংগ্রহের শুরু সময়কালঃ </b><span class="text-danger">*</span></label>
                                                           
                                                            <input class="form-control" type="date" placeholder="তথ্য সংগ্রহের শুরু সময়কাল" name="collection_start_date" id="collection_start_date" required/>
                                                           
                                                        </div> 
                                                    </div> 
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">

                                                        <div class="form-group row">
                                                            <label for="collection_end_date" style="font-size: 1.5em; font-weight: bold;"><b>তথ্য সংগ্রহের শেষ সময়কালঃ </b><span class="text-danger">*</span></label>
                                                           
                                                            <input class="form-control" type="date" placeholder="তথ্য সংগ্রহের শেষ সময়কাল" name="collection_end_date" id="collection_end_date" required/>
                                                           
                                                        </div> 

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">জমির ভাড়া/লিজ খরচ</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="amount_of_cultivable_land_cluster_or_outside_cluster"style="font-size: 1.5em; font-weight: bold;"><b> দাগগুচ্ছ/দাগগুচ্ছ বহির্ভূত আবাদকৃত জমির পরিমাণ (একরে): </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="দাগগুচ্ছ/দাগগুচ্ছ বহির্ভূত আবাদকৃত জমির পরিমাণ (একরে)" name="amount_of_cultivable_land_cluster_or_outside_cluster" id="amount_of_cultivable_land_cluster_or_outside_cluster" />
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="land_type" style="font-size: 1.5em; font-weight: bold;"><b>জমির ধরন: </b><span class="text-danger">*</span></label>

                                                            <select class="form-control" id="land_type" name="land_type" >
                                                                <option value="">--জমির ধরন বাছাই করুন--</option>
                                                                <option value="1">নিজস্ব</option>
                                                                <option value="2">বর্গা</option>
                                                                <option value="3">ভাড়া/লিজকৃত</option>
                                                            </select>
                                                        </div>
                                                    </div>  

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="land_rent_amount"style="font-size: 1.5em; font-weight: bold;"><b> আনুমানিক কত টাকা ভাড়া হতে পারে? :</b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="আনুমানিক কত টাকা ভাড়া হতে পারে?" name="land_rent_amount" id="land_rent_amount" />
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="approximate_rent_amount"style="font-size: 1.5em; font-weight: bold;"><b> শুধু এ ফসলের জন্য আনুমানিক ভাড়া কত? (টাকা) : </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="শুধু এ ফসলের জন্য আনুমানিক ভাড়া কত? (টাকা)" name="approximate_rent_amount" id="approximate_rent_amount" />
                                                        
                                                        </div>
                                                    </div>  

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="area_rent_amount_per_year"style="font-size: 1.5em; font-weight: bold;"><b> এলাকায় একর প্রতি বছরে ভাড়া (টাকা): </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="এলাকায় একর প্রতি বছরে ভাড়া (টাকা)" name="area_rent_amount_per_year" id="area_rent_amount_per_year" />
                                                        
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">জমির কর্ষ/চাষ করা</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">

                                                        <table class="table table-bordered"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th style="text-align: left">জমি কর্ষণ/চাষের ধরন</th>
                                                                    <th>সংখ্যা</th>
                                                                    <th>খরচ (টাকা)</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td align="left">নিজস্ব হাল/বলদ</td>
                                                                    <td><input class="form-control" type="number" name="own_korshon_quantity" id="own_korshon_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="own_korshon_amount" id="own_korshon_amount" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td align="left">ভাড়া করা হাল/বলদ</td>
                                                                    <td><input class="form-control" type="number" name="rent_korshon_quantity" id="rent_korshon_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="rent_korshon_amount" id="rent_korshon_amount" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td align="left">পাওয়ার টিলার/ট্রাক্টর ভাড়া</td>
                                                                    <td><input class="form-control" type="number" name="power_tiler_korshon_quantity" id="power_tiler_korshon_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="power_tiler_korshon_amount" id="power_tiler_korshon_amount" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td align="left">শ্রমিক মজুরী (কর্ষণের/চাষের কাজে)</td>
                                                                    <td><input class="form-control" type="number" name="sromik_mojuri_quantity" id="sromik_mojuri_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="sromik_mojuri_amount" id="sromik_mojuri_amount" /></td>
                                                                    
                                                                </tr>

                                                                <tr> 
                                                                    <td align="left">পারিবারিক শ্রমিক (কর্ষণের/চাষের সময় কাজ)</td>
                                                                    <td><input class="form-control" type="number" name="paribarik_sromik_quantity" id="paribarik_sromik_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="paribarik_sromik_amount" id="paribarik_sromik_amount" /></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>   
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">বীজ/চারার খরচ</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">
                                                        <table class="table table-bordered"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th rowspan="2">বীজ/চারার ধরন</th>
                                                                    <th colspan="4">বীজ/চারার উৎস</th>
                                                                    <th colspan="2">বীজ/চারার মোট পরিমাণ</th>
                                                                    <th colspan="2">মূল্য (টাকা)</th>
                                                                </tr>

                                                                <tr>
                                                                    <th>নিজস্ব</th>
                                                                    <th>ক্রয়ক্রিত</th>
                                                                    <th>প্রনোদনা</th>
                                                                    <th>দান</th>

                                                                    <th>কেজি</th>
                                                                    <th>আটি</th>

                                                                    <th>বীজ</th>
                                                                    <th>চারা</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>বীজ (কেজি)</td>
                                                                    <td><input class="form-control" type="number" name="seeds_own_source" id="seeds_own_source" /></td>
                                                                    <td><input class="form-control" type="number" name="seeds_sellable_source" id="seeds_sellable_source" /></td>
                                                                    <td><input class="form-control" type="number" name="seeds_incentive_source" id="seeds_incentive_source" /></td>
                                                                    <td><input class="form-control" type="number" name="seeds_donated_source" id="seeds_donated_source" /></td>

                                                                    <td><input class="form-control" type="number" name="seed_quantity" id="seed_quantity" /></td>
                                                                    <td>------</td>
                                                                    
                                                                    <td><input class="form-control" type="number" name="seed_value" id="seed_value" /></td>
                                                                    <td>------</td>
                                                                </tr>
                                                                
                                                                <tr> 
                                                                    <td>চারা (আটি সংখ্যা)</td>
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="seedlings_sellable_source" id="seedlings_sellable_source" /></td>
                                                                    <td><input class="form-control" type="number" name="seedlings_incentive_source" id="seedlings_incentive_source" /></td>
                                                                    <td><input class="form-control" type="number" name="seedlings_donated_source" id="seedlings_donated_source" /></td>
                                                                    
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="seedling_quantity" id="seedling_quantity" /></td>
                                                                    
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="seedlings_value" id="seedlings_value" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>বীজ নিজস্ব হলে চারা উৎপাদনের খরচ </td>
                                                                    <td>------</td>
                                                                    <td>------</td>
                                                                    <td>------</td>
                                                                    <td>------</td>
                                                                    
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="when_own_seed_quantity" id="when_own_seed_quantity" /></td>
                                                                    
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="when_own_seed_value" id="when_own_seed_value" /></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">সার বাবদ খরচ</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">
                                                        <table class="table table-bordered"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th>অজৈব সার</th>
                                                                    <th>পরিমাণ (কেজি)</th>
                                                                    <th>খরচ (টাকা)</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>ইউরিয়া</td>
                                                                    <td><input class="form-control" type="number" name="uriya_quantity" id="uriya_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="uriya_cost" id="uriya_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>টিএসপি</td>
                                                                    <td><input class="form-control" type="number" name="tsp_quantity" id="tsp_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="tsp_cost" id="tsp_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>এমওপি</td>
                                                                    <td><input class="form-control" type="number" name="mop_quantity" id="mop_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="mop_cost" id="mop_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>ডিএপি</td>
                                                                    <td><input class="form-control" type="number" name="dap_quantity" id="dap_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="dap_cost" id="dap_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>জিপসাম</td>
                                                                    <td><input class="form-control" type="number" name="gypsum_quantity" id="gypsum_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="gypsum_cost" id="gypsum_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>নিক্সসার</td>
                                                                    <td><input class="form-control" type="number" name="nixar_quantity" id="nixar_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="nixar_cost" id="nixar_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>অন্যান্য অজৈব সার</td>
                                                                    <td><input class="form-control" type="number" name="other_inorganic_quantity" id="other_inorganic_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="other_inorganic_cost" id="other_inorganic_cost" /></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>

                                                        <table class="table table-bordered mt-2"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th>জৈব সার</th>
                                                                    <th>পরিমাণ (কেজি)</th>
                                                                    <th>খরচ (টাকা)</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>গোবর</td>
                                                                    <td><input class="form-control" type="number" name="gobor_quantity" id="gobor_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="gobor_cost" id="gobor_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>ছাই</td>
                                                                    <td><input class="form-control" type="number" name="ash_quantity" id="ash_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="ash_cost" id="ash_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>সবুজ সার</td>
                                                                    <td><input class="form-control" type="number" name="green_quantity" id="green_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="green_cost" id="green_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>অন্যান্য অজৈব সার</td>
                                                                    <td><input class="form-control" type="number" name="other_organic_quantity" id="other_organic_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="other_organic_cost" id="other_organic_cost" /></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">সেচ পদ্ধতি</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">
                                                        <table class="table table-bordered"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th>খরচের খাত</th>
                                                                    <th>শ্রমিক সংখ্যা</th>
                                                                    <th>খরচ (টাকা)</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>সেচ পদ্ধতি (ডিপ/শ্যালো টিউবওয়েল, পাওয়ার পাম্প)</td>
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="tubewell_cost" id="tubewell_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>চাপকল/দোন</td>
                                                                    <td><input class="form-control" type="number" name="chapcol_worker_list" id="chapcol_worker_list" /></td>
                                                                    <td><input class="form-control" type="number" name="chapcol_cost" id="chapcol_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>সেচের জন্য জ্বালানী/ডিজেল/পেট্রোল/বিদ্যুৎ</td>
                                                                    <td>------</td>
                                                                    <td><input class="form-control" type="number" name="jalani_cost" id="jalani_cost" /></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">শ্রমিক খরচ</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">
                                                        <table class="table table-bordered"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th rowspan="2">শ্রমিক খরচের বিবরণ</th>
                                                                    <th colspan="2">শ্রমিকের সংখ্যা</th>
                                                                    <th rowspan="2">মোট শ্রমিক মজুরী (টাকা)</th>
                                                                </tr>

                                                                <tr> 
                                                                    <th>পারিবারিক শ্রমিক</th>
                                                                    <th>ভাড়া শ্রমিক</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>চারা রোপন/বপন</td>
                                                                    <td><input class="form-control" type="number" name="seedling_family_worker_no" id="seedling_family_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="seedling_hired_worker_no" id="seedling_hired_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="seedling_total_worker_cost" id="seedling_total_worker_cost" /></td>
                                                                </tr>
                                                                
                                                                <tr> 
                                                                    <td>নিড়ানি</td>
                                                                    <td><input class="form-control" type="number" name="nirani_family_worker_no" id="nirani_family_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="nirani_hired_worker_no" id="nirani_hired_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="nirani_total_worker_cost" id="nirani_total_worker_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>ফসল কর্তন</td>
                                                                    <td><input class="form-control" type="number" name="crop_cutting_family_worker_no" id="crop_cutting_family_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="crop_cutting_hired_worker_no" id="crop_cutting_hired_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="crop_cutting_total_worker_cost" id="crop_cutting_total_worker_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>ফসল মারাই/ঝারাই</td>
                                                                    <td><input class="form-control" type="number" name="crop_marai_family_worker_no" id="crop_marai_family_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="crop_marai_hired_worker_no" id="crop_marai_hired_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="crop_marai_total_worker_cost" id="crop_marai_total_worker_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>পাট ফসলের জাক দেয়া/কাঠি থেকে আঁশ ছাড়ানো</td>
                                                                    <td><input class="form-control" type="number" name="jute_family_worker_no" id="jute_family_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="jute_hired_worker_no" id="jute_hired_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="jute_total_worker_cost" id="jute_total_worker_cost" /></td>
                                                                </tr>

                                                                <tr> 
                                                                    <td>অন্যান্য</td>
                                                                    <td><input class="form-control" type="number" name="other_family_worker_no" id="other_family_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="other_hired_worker_no" id="other_hired_worker_no" /></td>
                                                                    <td><input class="form-control" type="number" name="other_total_worker_cost" id="other_total_worker_cost" /></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>  
 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">কীটনাশক ও বালাইনাশক খরচ</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">
                                                        <table class="table table-bordered"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th>কীটনাশকের নাম</th>
                                                                    <th>পরিমাণ (মিলি লিটারে লিখুন)</th>
                                                                    <th>খরচ (টাকা)</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>সুমিথিয়ন</td>
                                                                    <td><input class="form-control" type="number" name="sumithion_quantity" id="sumithion_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="sumithion_cost" id="sumithion_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>ম্যালাথিয়ন</td>
                                                                    <td><input class="form-control" type="number" name="malathion_quantity" id="malathion_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="malathion_cost" id="malathion_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>বাসুডিন</td>
                                                                    <td><input class="form-control" type="number" name="basudin_quantity" id="basudin_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="basudin_cost" id="basudin_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>ফুরাডান</td>
                                                                    <td><input class="form-control" type="number" name="furadon_quantity" id="furadon_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="furadon_cost" id="furadon_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>ফুরানল</td>
                                                                    <td><input class="form-control" type="number" name="furanol_quantity" id="furanol_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="furanol_cost" id="furanol_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>অন্যান্য</td>
                                                                    <td><input class="form-control" type="number" name="other_quantity" id="other_quantity" /></td>
                                                                    <td><input class="form-control" type="number" name="other_cost" id="other_cost" /></td>
                                                                <tr> 
                                                            </tbody>
                                                        </table>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">পরিবহন খরচ</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-12" style="padding-left: 35px; padding-right:35px;">
                                                        <table class="table table-bordered mt-2"> 
                                                            <thead> 
                                                                <tr>
                                                                    <th>খাতের নাম</th>
                                                                    <th>খরচ (টাকা)</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody> 
                                                                <tr> 
                                                                    <td>জমির কর্ষ/চাষ করার সামগ্রী পরিবহন</td>
                                                                    <td><input class="form-control" type="number" name="land_things_transport_cost" id="land_things_transport_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>বীজ/চারা জমিতে নিয়ে আসার জন্য পরিবহন</td>
                                                                    <td><input class="form-control" type="number" name="seed_transport_cost" id="seed_transport_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>সার পরিবহন</td>
                                                                    <td><input class="form-control" type="number" name="fertilizer_transport_cost" id="fertilizer_transport_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>সেচ সংক্রান্ত সরঞ্জামাদি পরিবহন</td>
                                                                    <td><input class="form-control" type="number" name="irrigation_transport_cost" id="irrigation_transport_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>কীটনাশক ও বালাইনশক পরিবহন</td>
                                                                    <td><input class="form-control" type="number" name="pesticide_transport_cost" id="pesticide_transport_cost" /></td>
                                                                </tr>
                                                                <tr> 
                                                                    <td>অন্যান্য</td>
                                                                    <td><input class="form-control" type="number" name="other_transport_cost" id="other_transport_cost" /></td>
                                                                <tr> 
                                                            </tbody>
                                                        </table>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">কৃষক কর্তৃক দাগগুচ্ছ/দাগগুচ্ছ বহির্ভূত আবাদকৃত জমিতে ফসলের উৎপাদন ও তার মূল্য সংক্রান্ত তথ্যাদি </h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="arable_land"style="font-size: 1.5em; font-weight: bold;"><b> মোট আবাদি জমির পরিমান (একর): </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="মোট আবাদি জমির পরিমান (একর)" name="arable_land" id="arable_land" required/>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="total_production"style="font-size: 1.5em; font-weight: bold;"><b> মোট উৎপাদন (কেজি): </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="মোট উৎপাদন (কেজি)" name="total_production" id="total_production" required/>
                                                        
                                                        </div>
                                                    </div>  

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="crops_total_value_tk"style="font-size: 1.5em; font-weight: bold;"><b> ফসলের মোট মূল্য: </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="মোট আবাদি জমির পরিমান (একর)" name="crops_total_value_tk" id="crops_total_value_tk" required/>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="sub_varieties_crop_quantity"style="font-size: 1.5em; font-weight: bold;"><b> ফসলের উপজাতের পরিমান (কেজি/আটি): </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="text" placeholder="মোট উৎপাদন (কেজি)" name="sub_varieties_crop_quantity" id="sub_varieties_crop_quantity" required/>
                                                        
                                                        </div>
                                                    </div>  

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="sub_varieties_crop_value"style="font-size: 1.5em; font-weight: bold;"><b> উপজাতের মূল্য স্থানীয় বাজারদর অনুযায়ী : </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="মোট আবাদি জমির পরিমান (একর)" name="sub_varieties_crop_value" id="sub_varieties_crop_value" required/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center" style="background-color: #134f1f; padding: 10px;">
                                            <h3 style="color:#ffffff;">ফসলের মূল্য (কেজিতে)</h3>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                           
                                                <div class="row">
                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="crop_cost_farmer"style="font-size: 1.5em; font-weight: bold;"><b>কৃষকের বাড়িতে/খামারে: </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="কৃষকের বাড়িতে/খামারে" name="crop_cost_farmer" id="crop_cost_farmer" required/>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="crop_cost_local_market"style="font-size: 1.5em; font-weight: bold;"><b> স্থানীয় বাজার/হাট: </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="স্থানীয় বাজার/হাট" name="crop_cost_local_market" id="crop_cost_local_market" required/>
                                                        
                                                        </div>
                                                    </div>  

                                                    <div class="col-md-6" style="padding-left: 35px; padding-right:35px;">
                                                        <div class="form-group row">
                                                            <label for="crop_cost_millers" style="font-size: 1.5em; font-weight: bold;"><b> সরকারী ক্রয়কেন্দ্র/মিলার্স: </b><span class="text-danger">*</span></label>
                                                            
                                                            <input class="form-control" type="number" placeholder="সরকারী ক্রয়কেন্দ্র/মিলার্স" name="crop_cost_millers" id="crop_cost_millers" required/>
                                                        </div>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-12 text-right">
                                                    @if ($number == true)
                                                        <button type="submit" class="btn btn-success mr-2">Submit</button>
                                                    @else                                                    
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

@push('stackScript')
    <script>
        
        $('#farmer_id').change(function(){
                var farmerId = $('#farmer_id').val();
    
                var url1= window.location.origin+`/bbs/potatoCropCuttingProduction/mouza-place/${farmerId}/get-data`;
                // alert(url1);
                $.getJSON(url1, function(data){
                    console.log(data);
                    if(data.cluster_id != null) {
                        $("#cluster_filed").show();
                        $("#land_segment_signal_field").show();

                        $('#in_cluster').empty();
                        $("#in_cluster").val('দাগগুচ্ছের ভিতরে');

                        // $("#land_segment_signal").val(data.cluster_indentity_no);
                        $("#land_segment_signal").attr("required", 'required');
                    }
                    else{
                        $("#cluster_filed").show();
                        $("#land_segment_signal_field").hide();

                        $('#in_cluster').empty();
                        $("#land_segment_signal").empty();

                        
                        $("#in_cluster").val('দাগগুচ্ছের বাইরে');
                     
                        
                        $('#land_segment_signal').removeAttr('required');

                    }
                });

                
            });
    </script>
@endpush
