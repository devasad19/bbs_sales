@extends('backend.layout.master')

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
                        <h5 class="text-dark font-weight-bold my-1 mr-5">Edit Course Curriculam Details</h5>
                        <!--end::Page Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{route('admin.index')}}" class="text-muted">Dashboard</a>
                            </li>
                        
                            <li class="breadcrumb-item active">
                                <a class="text-muted">Edit Course Curriculam Details</a>
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
                            <div class="card-header">
                                <h3 class="card-title">Edit Course Curriculam</h3>
                            </div>
                            <div class="card-body">
                                <form  action="{{ route('admin.course.updateCourseCurriculam',$courseCurriculam) }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <!--Left-->
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-form-label text-right col-lg-4 col-sm-12">Module Number<span
                                                        class="text-danger">
                                                        *</span></label>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="number" placeholder="Module Number" class="form-control" name="module_no"
                                                        value="{{ $courseCurriculam->module_no }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-form-label text-right col-lg-4 col-sm-12">Subject Title<span
                                                        class="text-danger">
                                                        *</span></label>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" placeholder="Subject Title" class="form-control" name="subject_title"
                                                        value="{{ $courseCurriculam->subject_title }}" required>
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-form-label text-right col-lg-4 col-sm-12">Subject Code<span
                                                        class="text-danger">
                                                        *</span></label>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" placeholder="Subject Code" class="form-control"
                                                        name="subject_code" value="{{ $courseCurriculam->subject_code }}" required>
                                                </div>
                                            </div>
                        
                                        </div>
                        
                                        
                                        <div class="col-md-3 offset-md-4">
                                            <button class="btn btn-block btn-info" id="kt_btn_1" type="submit">
                                                 Update
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection