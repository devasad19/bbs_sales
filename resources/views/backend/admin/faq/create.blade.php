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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">FAQ</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.index')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="text-muted">Add FAQ</a>
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
            <!--begin::Card-->
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom example example-compact">
                        <div class="card-header">
                            <h3 class="card-title">Add FAQ</h3>
                        </div>

                        <form class="form" action="{{ route('admin.faq.store') }}" method="post" id="kt_form_1" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-form-label text-right col-lg-4 col-sm-12">Question: <span class="text-danger"> *</span></label>
                                    <div class="col-lg-5 col-sm-12">
                                        <textarea type="text" class="form-control {{ $errors->has('question') ? ' is-invalid' : '' }}" name="question" placeholder="Enter Question" value="{{old('question')}}" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label text-right col-lg-4 col-sm-12">Answer: <span class="text-danger"> *</span></label>
                                    <div class="col-lg-5 col-sm-12">
                                        <textarea type="text" class="form-control {{ $errors->has('answer') ? ' is-invalid' : '' }}" name="answer" placeholder="Enter Answaer" value="{{old('answer')}}" required></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-form-label text-right col-lg-4 col-sm-12">Attach File: </label>
                                    <div class="col-lg-5 col-sm-12">
                                        <input type="file" class="form-control {{ $errors->has('answer') ? ' is-invalid' : '' }}" name="file" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label text-right col-lg-4 col-sm-12">Created For: </label>
                                    <div class="col-lg-5 col-sm-12">
                                        <input type="text" class="form-control {{ $errors->has('created_for') ? ' is-invalid' : '' }}" name="created_for" placeholder="Enter Created Reason" value="{{old('created_for')}}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer"> 
                                <div class="form-group row">
                                    <div class="col-lg-9 text-right">
                                        <button type="submit" class="btn btn-primary font-weight-bold" name="submitButton">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection