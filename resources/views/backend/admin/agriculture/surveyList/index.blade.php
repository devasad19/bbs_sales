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
							<h5 class="text-dark font-weight-bold my-1 mr-5">Agriculture Survey Assigned Field Officer and Areas </h5>
							<!--end::Page Title-->
							<!--begin::Breadcrumb-->
							<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
								<li class="breadcrumb-item">
									<a href="{{route('admin.index')}}" class="text-muted">Dashboard</a>
								</li>
								{{-- @can('manage_agriculture') --}}
									<li class="breadcrumb-item">
										<a href="{{route('admin.surveyList.index')}}" class="text-muted">Agriculture Survey Assigned Field Officer and Areas </a>
									</li>
								{{-- @endcan --}}
								<li class="breadcrumb-item active">
									<a class="text-muted">Agriculture Survey Assigned Field Officer and Areas</a>
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
					<!--session msg-->
					@include('alerts.alerts')

					<!--begin::Card-->
					<div class="row">
						<div class="col-lg-12">
							<!--begin::Card-->
							<div class="card card-custom example example-compact">
								<div class="card-header">
									<h3 class="card-title">Agriculture Survey Assigned Field Officer and Areas</h3>
									<input type="text" data-url="{{ route('admin.searchAjax',['type'=> 'agriSurveyNoti']) }}" class="form-control form-control-lg form-control-solid ajax-data-search align-left" name="q" placeholder="Search Survey Notification Form by name" style="width: 25%; float:right; margin-top:10px">
								</div>

								<div class="card-body">
									<div class="table-responsive ajax-data-container pt-3">
										@include('backend.admin.agriculture.surveyList.ajax.tableBody')                                    
									</div>
								</div>
								<!--end::table-->
							</div>
							<!--end::Card-->
						</div>
					</div>
				</div>
				<!--end::Container-->
			</div>
			<!--end::Entry-->
		</div>
		<!--end::Content-->
	@endsection
					
