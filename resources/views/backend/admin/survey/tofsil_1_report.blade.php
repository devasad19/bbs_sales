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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Cluster Survey Report</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.index')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="text-muted">Cluster Survey Report</a>
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
                                    <p class="font-weight-bold mt-4">দাগগুচ্ছ জরিপ প্রতিবেদন</p>
                                    <p class="mt-4">(Cluster Survey Report)</p>
                                </div>
                                <div class="col-3">
                                    <p class="font-weight-bold text-right">তফসিল - ১</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive ajax-data-container pt-3">
                                    <p style="font-size: 1.3em; font-weight: 500">১। দাগগুচ্ছের পরিচিতি :</p>
                                    <table class="table">
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="3">বিভাগ:</td>
                                            <td style="border: 1px solid #000" align="left" colspan="3">জেলা:</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">উপজেলা/থানা:</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="3">কোড:</td>
                                            <td style="border: 1px solid #000" align="left" colspan="3">কোড:</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">কোড:</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">ইউনিয়ন:</td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left">কোড:</td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left">মৌজা:</td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left">কোড:</td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">দাগগুচ্ছ নম্বর:</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2"></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">জে,এল,নং:</td>
                                            <td style="border: 1px solid #000" align="left" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">দাগগুচ্ছের আয়তন (একরে):</td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">পর্ব নং:(1/2/3/4 প্রযোজ্যটি লিখুন)</td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left" colspan="2">তথ্য সংগ্রহের তারিখ:......................</td>
                                        </tr>
                                    </table>
                                    <hr>

                                    <p style="font-size: 1.3em; font-weight: 500">২। ভূমির ব্যবহার:</p>
                                    <table class="table">
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" rowspan="3">ভূমি খন্ডের পরিচিতি নম্বর</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="3">বর্তমানে ভূমি খন্ড ব্যবহারকারী/চাষীর নাম ও মোবাইল নম্বর</td>
                                            <td style="border: 1px solid #000" align="left" colspan="4">ভূমি খন্ড/জমির ব্যবহার লিখুন</td>
                                            <td style="border: 1px solid #000" align="left">এ ভূমি খন্ডটিতে বছরে কতবার বিভিন্ন ফসলের চাষ করা হয়</td>
                                            <td style="border: 1px solid #000" align="left">ভূমি খন্ডের চাষ পদ্ধতি</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="3">ভূমি খন্ডে সেচ পদ্ধতির কোড</td>
                                            <td style="border: 1px solid #000" align="left">এ ভূমিখন্ডে বিভিন্ন ফসলের জন্য গত বছরে কতবার সেচ দেয়া হয়েছে</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left" colspan="2">ফসলি জমি</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">অফসলি জমি (কোড লিখুন)</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">পরিমান (একর)</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">এক ফসলী - 1 <br>দুই ফসলী - 2 <br>তিন ফসলী - 3</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">যান্ত্রিক - 1 <br>অযান্ত্রিক - 2</td>
                                            <td style="border: 1px solid #000" align="left" rowspan="2">একবার - 1 <br>দুইবার - 2 <br>তিনবার - 3</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">ফসলের নাম</td>
                                            <td style="border: 1px solid #000" align="left">ফসলের কোড</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #000" align="left">১</td>
                                            <td style="border: 1px solid #000" align="left">২</td>
                                            <td style="border: 1px solid #000" align="left">৩</td>
                                            <td style="border: 1px solid #000" align="left">৪</td>
                                            <td style="border: 1px solid #000" align="left">৫</td>
                                            <td style="border: 1px solid #000" align="left">৬</td>
                                            <td style="border: 1px solid #000" align="left">৭</td>
                                            <td style="border: 1px solid #000" align="left">৮</td>
                                            <td style="border: 1px solid #000" align="left">৯</td>
                                            <td style="border: 1px solid #000" align="left">১০</td>
                                        </tr>

                                        {{-- this row will be inside loop --}}
                                        <tr>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                            <td style="border: 1px solid #000" align="left"></td>
                                        </tr>
                                        
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mt-20">
                                        <div class="row">
                                            <div class="col-lg-4 ml-8">
                                                <p>তথ্য সংগ্রহকারীর স্বাক্ষর, নাম ও পদবী</p>
                                                <p>তারিখ :...................</p>
                                            </div>
                                            <div class="col-lg-3"></div>
                                            <div class="col-lg-4 text-right mr-8">
                                                <p>কর্মকর্তার স্বাক্ষর ও সীল</p>
                                                <p>তারিখ :....................</p>
                                            </div>
                                        </div>
                                        
                                        <p class="text-center">তফসিল পুরণের নির্দেশাবলী</p>

                                        <p>১. নমুনা দাগ্গুচ্ছের ভূমিখন্ডগুলোতে একই সময়ে বিভিন্ন ফসলের চাষ হতে পারে। চাষকৃত ফসলের জাতভিত্তিক (যেমন - দেশি, উফসী বোরো, দেশি ও উচ্চ ফলনশীল আলু প্রভৃতি) জমির পরিমাণ অন্যান্য তথ্যাদি ভিন্ন ভিন্ন লাইনে লিখতে হবে। ফসলের জাতের স্বীকৃত বা প্রচলিত নামই ব্যবহার করতে হবে। স্থানীয় বা স্বল্প পরিচিত নামসমূহ যতদূর সম্ভব পরিহার করা উচিত। সকল সংখ্যা ইংরেজিতে লিখতে হবে। </p>

                                        <p>২. দাগগুচ্ছ জরিপ প্রতিবেদন (তফসিল-১) পূরণের সময় ১৫ জুন (১ আষাঢ়) হতে পরবর্তী ১৪ জুন (৩১ জৈষ্ঠ্য) পর্যন্ত।</p>

                                        <p>৩. ফসলের কোড, অফসলি জমির কোড, সেচ পদ্ধতির কোড তফসিল-১ অনুযায়ী পূরণ করতে হবে।</p>

                                        <p>৪. প্রতিটি দাগগুচ্ছে নির্ধারিত সময়ে বছরে ৪ (চার) বার তফসিল-১ পূরণ করতে হবে।</p>

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