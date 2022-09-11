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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Perennial Crop Production Survey</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.index')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="text-muted">Perennial Crop Production Survey</a>
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
                                    <p class="font-weight-bold">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</p>
                                    <p class="font-weight-bold">পরিকল্পনা মন্ত্রণালয়</p>
                                    <p class="font-weight-bold">বাংলাদেশ পরিসংখ্যান ব্যুরো</p>
                                    <p class="font-weight-bold">এগ্রিকালচার উইং</p>
                                    <p class="font-weight-bold">পরিসংখ্যান ভবন</p>
                                    <p class="font-weight-bold">ই-২৭/এ আগারগাঁও, ঢাকা-১২০৭</p>
                                    <p class="font-weight-bold mt-4">স্থায়ী/বহুবর্ষজীবী ফসল উৎপাদন জরিপ</p>
                                    <p class="mt-4">(Perennial Crop Production Survey)</p>
                                </div>
                                <div class="col-3">
                                    <p class="font-weight-bold text-right">তফসিল - ৪</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive ajax-data-container pt-3">
                                    <p style="font-size: 1.3em; font-weight: 500">১। এলাকা পরিচিতি :</p>
                                    <table class="table">
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">বিভাগ:<b>{{ $list->division ? $list->division->name_bn : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">জেলা:<b>{{ $list->district ? $list->district->name_bn : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">উপজেলা:<b>{{ $list->upazila ? $list->upazila->name_bd : '' }}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">কোড:<b>{{ $list->division ? $list->division->division_bbs_code : '' }}</b></td></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">কোড:<b>{{ $list->district ? $list->district->district_bbs_code : '' }}</b></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">কোড:<b>{{ $list->upazila ? $list->upazila->upazila_bbs_code : '' }}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">ফসলের নাম: {{ $cropData->name_en }}</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">ইউনিয়ন/ওয়ার্ড: {{ $list->union ? $list->union->name_bn : '' }}</td>
                                            <td style="border: 1px solid #000" align="left">মৌজা: {{ $list->mouza ? $list->mouza->name_bn : '' }}</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2" rowspan="2">তথ্য সংগ্রহের তারিখ: <br>{{ $list->created_at->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">কোড:{{ $cropData->code }}</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">কোড:{{ $list->union ? $list->union->nunion_bbs_code : '' }}</td>
                                            <td style="border: 1px solid #000" align="left">কোড: {{ $list->mouza ? $list->mouza->nmouza_bbs_code : '' }}</td>
                                        </tr>
                                    </table>

                                    <p style="font-size: 1.3em; font-weight: 500">১| স্থায়ী ফসলের অধীন গাছের সংখ্যা, ফলন হার, উৎপাদন ও আয়তন</p>
                                    <table class="table">
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">ক্রমিক</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">চাষীর নাম ও মোবাইল নম্বর</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">মোট গাছের সংখ্যা</td>
                                            <td style="border: 1px solid #000" align="center" colspan="6">ফলবান গাছ</td>
                                            <td style="border: 1px solid #000" align="center" colspan="2">ফলবিহীন গাছ</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">মোট গাছের অধীন জমির আয়তন (একর)</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">বাগানে গাছের সংখ্যা</td>
                                            <td style="border: 1px solid #000" align="left">বিক্ষিপ্ত গাছের সংখ্যা</td>
                                            <td style="border: 1px solid #000" align="left">মোট গাছের সংখ্যা</td>
                                            <td style="border: 1px solid #000" align="left">গাছের অধীন জমির আয়তন (একর)</td>
                                            <td style="border: 1px solid #000" align="left">গাছ প্রতি গড় ফলন (কেজি)</td>
                                            <td style="border: 1px solid #000" align="left">মোট উৎপাদন (মে.টন)</td>
                                            <td style="border: 1px solid #000" align="left">মোট গাছের সংখ্যা</td>
                                            <td style="border: 1px solid #000" align="left">গাছের অধীন জমির আয়তন (একর)</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">১</td>
                                            <td style="border: 1px solid #000" align="left">২</td>
                                            <td style="border: 1px solid #000" align="left">৩(=৬+১০)</td>
                                            <td style="border: 1px solid #000" align="left">৪</td>
                                            <td style="border: 1px solid #000" align="left">৫</td>
                                            <td style="border: 1px solid #000" align="left">৬(=৪+৫)</td>
                                            <td style="border: 1px solid #000" align="left">৭</td>
                                            <td style="border: 1px solid #000" align="left">৮(=৯/৬)</td>
                                            <td style="border: 1px solid #000" align="left">৯</td>
                                            <td style="border: 1px solid #000" align="left">১০</td>
                                            <td style="border: 1px solid #000" align="left">১১</td>
                                            <td style="border: 1px solid #000" align="left">১২(=৭+১১)</td>
                                        </tr>
                                        @foreach ($peremmialCrops as $crop)
                                            @php
                                                $data = 0;
                                                foreach ($crop->mouzaData($crop->mouza_id) as $key) {

                                                    $data = $data + 1;
                                                }
                                            @endphp
                                            <tr>
                                                <td style="border: 1px solid #000" align="left" rowspan="{{ $data + 5 }}">{{ $loop->index + 1 }}</td>
                                            </tr>
                                            {{-- data loop statrt --}}
                                            @php
                                                $total_trees_form = 0;
                                                $total_fruity_trees_in_garden = 0;
                                                $total_fruity_scattered_trees = 0;
                                                $sum_of_6 = 0;
                                                $sum_of_7 = 0;
                                                $sum_of_8 = 0;
                                                $sum_of_9 = 0;
                                                $sum_of_10 = 0;
                                                $sum_of_11 = 0;
                                                $sum_of_12 = 0;
                                            @endphp
                                            @foreach ($crop->mouzaData($crop->mouza_id) as $item)

                                                <tr>
                                                    <td style="border: 1px solid #000" align="left">{{ $item->farmer ? $item->farmer->farmers_name :'' }} <br> {{ $item->farmer ? $item->farmer->farmers_mobile :'' }}</td>
                                                    {{-- total_trees_form --}}
                                                    <td style="border: 1px solid #000" align="left"> {{  ($item->total_fruity_trees_in_garden + $item->total_fruity_scattered_trees) + $item->total_fruitless_trees }}</td>
                                                    {{-- total_fruity_trees_in_garden --}}
                                                    <td style="border: 1px solid #000" align="left">{{ $item->total_fruity_trees_in_garden }}</td>
                                                    {{-- total_fruity_scattered_trees --}}
                                                    <td style="border: 1px solid #000" align="left">{{ $item->total_fruity_scattered_trees }}</td>
                                                    {{-- $sum_of_6 --}}
                                                    <td style="border: 1px solid #000" align="left">{{ $item->total_fruity_trees_in_garden + $item->total_fruity_scattered_trees }}</td>
                                                        {{-- $sum_of_7 --}}
                                                    <td style="border: 1px solid #000" align="left">{{ $item->land_amount_under_the_fruitly_trees }}</td>
                                                        {{-- $sum_of_8 --}}
                                                    
                                                    <td style="border: 1px solid #000" align="left">{{ $item->total_production / ($item->total_fruity_trees_in_garden + $item->total_fruity_scattered_trees) }}</td>
                                                        {{-- $sum_of_9 --}}
                                                    
                                                    <td style="border: 1px solid #000" align="left">{{ $item->total_production }}</td>
                                                    {{-- $sum_of_10 --}}
                                                    <td style="border: 1px solid #000" align="left">{{ $item->total_fruitless_trees }}</td>
                                                    <td style="border: 1px solid #000" align="left">{{ $item->land_amount_under_the_fruitless_trees }}</td>
                                                    <td style="border: 1px solid #000" align="left">{{ $item->land_amount_under_the_fruitly_trees + $item->land_amount_under_the_fruitless_trees}}</td>
                                                </tr>
                                                @php
                                                    $total_trees_form += ($item->total_fruity_trees_in_garden + $item->total_fruity_scattered_trees) + $item->total_fruitless_trees ;
                                                    $total_fruity_trees_in_garden += $item->total_fruity_trees_in_garden;
                                                    $total_fruity_scattered_trees += $item->total_fruity_scattered_trees;
                                                    $sum_of_6 += $item->total_fruity_trees_in_garden + $item->total_fruity_scattered_trees;
                                                    $sum_of_7 += $item->land_amount_under_the_fruitly_trees; 
                                                    $sum_of_8 += $item->total_production / ($item->total_fruity_trees_in_garden + $item->total_fruity_scattered_trees);
                                                    $sum_of_9 += $item->total_production;
                                                    $sum_of_10 += $item->total_fruitless_trees;
                                                    $sum_of_11 += $item->land_amount_under_the_fruitless_trees;
                                                    $sum_of_12 += $item->land_amount_under_the_fruitly_trees + $item->land_amount_under_the_fruitless_trees;
                                                @endphp
                                            @endforeach
                                            {{-- data loop end --}}
                                        
                                            <tr>
                                                <td style="border: 1px solid #000" align="left">মোট</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_trees_form }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_fruity_trees_in_garden }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_fruity_scattered_trees }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_6 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_7 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_8 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_9 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_10 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_11 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_12 }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #000" align="left">মৌজার মোট</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_trees_form }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_fruity_trees_in_garden }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_fruity_scattered_trees }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_6 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_7 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_8 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_9 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_10 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_11 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_12 }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #000" align="left">ইউনিয়নের মোট</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_trees_form }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_fruity_trees_in_garden }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $total_fruity_scattered_trees }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_6 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_7 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_8 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_9 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_10 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_11 }}</td>
                                                <td style="border: 1px solid #000" align="left">{{ $sum_of_12 }}</td>
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
                                                
                                                <p>তথ্য সংগ্রহকারীর স্বাক্ষর, পূর্ণ নাম ও পদবী</p>
                                                <p>তারিখ : {{ $list->updated_at->format('d-m-Y') }}</p>
                                            </div>
                                            <div class="col-lg-3"></div>
                                            <div class="col-lg-4 text-right mr-8">
                                                @if ($list->createdBy)
                                                    
                                                <img src="{{ asset('storage/signatures/'.$list->createdBy->signature) }}" width="100" height="70">
                                                @endif
                                                
                                                <p>কর্মকর্তার স্বাক্ষর ও সীল</p>
                                                <p>তারিখ : {{ $list->created_at->format('d-m-Y') }}</p>
                                            </div>
                                        </div>
                                        
                                        <p class="text-center">তফসিল পূরণের নির্দেশাবলী</p>

                                        <p>১. উপজেলার প্রতিটি ইয়নিয়নের দৈব চয়ন পদ্ধতিতে নির্বাচিত একটি মৌজায় ৫ জন চাষীর নিকট হতে তথ্য সংগ্রহ করতে হবে। ৫ জন চাষী নির্বাচন কৃষক তালিকা ফরম (সংকলন-১) হতে দৈব চয়ন পদ্ধতিতে করতে হবে।</p>

                                        <p>২. কৃষক তালিকা ফরম (সংকলন-১) হতে ৩ জন ক্ষুদ্র কৃষক, ১ জন মাঝারী কৃষক এবং ১ জন বড় কৃষক দৈব চয়ন পদ্ধতিতে নির্বাচিত করে তথ্য সংগ্রহ করতে হবে।</p>

                                        <p>৩. বাগানে গাছের সংখ্যা: স্থায়ী ফসলের বাগানের জমিতে জরিপকৃত/সংশ্লিষ্ট স্থায়ী ফসলের কতটি গাছ হতে পারে তা কৃষক/খানা প্রধানের নিকট থেকে জেনে নিন, এরপর বাগানে কতটি ফলবান গাছ আছে জিজ্ঞাসা করুন অত:পর ফলবান গাছের সংখ্যা কলাম ৪-এ লিখুন এবং ফল বিহীন গাছের সংখ্যা কলাম-১০ এ লিখুন।</p>

                                        <p>৪. বিক্ষিপ্ত গাছের সংখ্যা: নির্বাচিত খানার মালিকানাধীন বসতভিটায়, মিশ্র বাগানে, মাঠে, রাস্তায়, পুকুর পাড়ে ইত্যাদিতে সংশ্লিষ্ঠ ফসলের যেসব গাছ রয়েছে, তার মোট ফলবান গাছের সংখ্যার হিসাব বের করে কলাম ৫-এ লিখুন এবং বিক্ষিপ্ত ফলবিহীন গাছের সংখ্যা, বাগানের ফলবিহীন গাছের সংখ্যার সাথে যোগ করে মোট ফলবিহীন গাছ কলাম - ১০ এ লিখুন।</p>

                                        <p>৫. মোট ফলবান গাছের সংখ্যা: বাগানে গাছের সংখ্যা ও বিক্ষিপ্ত ভাবে গাছের সংখ্যা যোগ করে মোট ফলবান গাছ সংখ্যা কলাম-৬ এ লিখুন।</p>

                                        <p>৬. মোট গাছের সংখ্যা: মোট ফলবান গাছের সংখ্যা এবং ফলবিহীন গাছের সংখ্যা যোগ করে মোট গাছ সংখ্যা কলাম-৩ (=৬+১০) এ লিখুন।</p>

                                        <p>৭. জমির পরিমাণ: খানাপ্রধান/বাগান মালিকদের নিকট থেকে জেনে ফলবান গাছের অধীন জমির পরিমাণ কলাম ৭-এ এবং ফলবিহীন গাছের অধীন জমির পরিমাণ কলাম ১১-এ লিখুন। বাগানে জমির পরিমাণ ও বিক্ষিপ্ত ভাবে গাছের অধীন জমির পরিমাণ যোগ করে মোট গাছের অধীন জমির পরিমাণ কলাম -১২ (=৭+১১) এ লিখুন।</p>

                                        <p>৮. গাছ প্রতি ফলন: একটি গাছে গড়ে কী পরিমাণ ফল হয় তা কেজিতে কলাম ৮ (=৯/৬)-এ লিখুন। উদাহরণস্বরূপ - কাঁঠাল সংখ্যাতে হলেও কেজিতে লিখতে হবে।</p>

                                        <p>৯. মোট উৎপাদন: একটি গাছে/বাগানে কী পরিমাণ ফল হয় খানাপ্রধান/বাগান মালিকদের নিকট থেকে জেনে মোট উৎপাদন কলম ৯-এ লিখুন।</p>

                                        <p>১০. প্রশ্ন ৬ এ শতকরা হ্রাস/বৃদ্ধি গত বছরের ভিত্তিতে করতে হবে। হ্রাস হলে শতকরা হিসাবের সামনে (-) চিহ্ন দিয়ে লিখতে হবে। যেমন (-) ৫%।</p>

                                        <p>১১. প্রশ্ন ৭ এ হ্রাস/বৃদ্ধির কারণ হিসাবে ভাল আবহাওয়া, প্রাকৃতিক দুর্যোগ, অর্থনৈতিকভাবে লাভজনক ইত্যাদি কারণ হতে পারে। বিস্তারিত কারণ উল্লেখ করুন।</p>

                                        <p>১২. প্রাক্কলিত মৌজার তথ্যের ভিত্তিতে উপজেলার প্রতিটি ইউনিয়নের তথ্য প্রাক্কলন করতে হবে।</p>

                                        <p>১৩. তফসিল ৪ পূরণ করার সময় মৌজা এবং ইউনিয়নের তথ্য স্থানীয় জনপ্রতিনিধি, গন্যমান্য ব্যক্তি, গ্রাম পুলিশ, উপসহকারী কৃষি কর্মকর্তাসহ সংশ্লিষ্ঠদের সাথে আলাপ করে তথ্যের যথার্থতা যাচাই করুন।</p>

                                        <p>১৪. স্থায়ী ফসল/বহুবর্ষজীবি ফসল বলতে দুই বা ততধিক বছর ধরে যে সব গাছ ফসল দেয় তা বুঝবে। উদাহরণস্বরূপ: আম, কাঁঠাল, জাম, লিচু ইত্যাদি ফলজ গাছসহ চা, সুপারি ইত্যাদি।</p>

                                        <p>১৫. তফসিল ৪ এর তথ্যের ভিত্তিতে সংকলন-৪ পূরণ করতে হবে।</p>

                                        <p>১৬. উপজেলা অফিস এ জরিপ তথ্যের ভিত্তিতে প্রয়োজনীয় উপাত্ত "সংকলন ফরম-৪" পূরণ করে জেলা অফিসে নির্ধারিত সময়ের মধ্যে প্রেরণ করবে। জেলা অফিস অধীনস্থ সকল উপজেলার সংকলন ফরম-৪ এর ভিত্তিতে তথ্য সংকলন করে বিভাগীয় অফিসে প্রেরণ করবে এবং বিভাগীয় অফিস অধীনস্থ জেলা অফিসসমূহ হতে প্রাপ্ত "সংকলন ফরম-৪" সংকলন করে এগ্রিকালচার উইংয়ে নির্ধারিত সময়ে প্রেরণ করবে। "সংকলন ফরম-৪" এর মত "তফসিল-৪" উপজেলা হতে জেলা অফিস, বিভাগ অফিস এবং এগ্রিকালচার উইং-এ প্রেরণ করতে হবে।</p>

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