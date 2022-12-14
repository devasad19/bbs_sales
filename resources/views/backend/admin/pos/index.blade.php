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
                        <h5 class="text-dark font-weight-bold my-1 mr-5">BBS Point Of Sales (POS)</h5>
                        <!--end::Page Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.index') }}" class="text-muted">Dashboard</a>
                            </li>
                            @can('manage_storage')
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.pos.index') }}" class="text-muted">BBS Point Of Sales
                                        (POS)</a>
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
                    <div class="col-lg-4">
                        <!--begin::Card-->
                        <form action="{{ route('admin.pos.submit') }}" class="form-group " method="post">
                            @csrf
                            <div class="card card-custom example example-compact" style="min-height: 512px;">
                                <div class="card-body">
                                    
                                        <div class="row">
                                            
                                            <div class="col-lg-10 col-md-10 col-sm-10 mb-2" >
                                                <select class="form-control step2-select"
                                                    name="customer" id="customer" 
                                                    
                                                    data-placeholder="Customer"
                                                    data-select2-id="customer"
                                                    tabindex="-1" 
                                                    aria-hidden="true"
                                                    data-ajax-url="{{route('admin.searchAjax',['type'=>'searchUser'])}}"
                                                    data-ajax-cache="true"
                                                    data-ajax-dataType="json"
                                                    data-ajax-delay="200">
                                                                                                       
                                                </select>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2">
                                                <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    <div class="table-responsive ajax-data-container-product">
                                        @include('backend.admin.pos.ajax.cartTable')
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">

                                <div class="col-md-6">
                                    <button class="btn btn-block btn-danger" type="submit" name="cancel"
                                        value="cancel">Cancel</button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-block btn-success" type="submit" name="submit"
                                        value="submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header p-3">


                                <input type="text" data-url="{{ route('admin.searchAjax', ['type' => 'posItemSearch']) }}"
                                    class="form-control form-control-lg form-control-solid ajax-data-search cartItemOnkeySave"
                                    name="q" placeholder="Search Items By Barcode or Title" style="background-color:white;">


                            </div>
                            <div class="card-body">
                                <div class="table-responsive ajax-data-container">
                                    @include('backend.admin.pos.ajax.productTable')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- modal --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add New Customer</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{route('admin.pos.createCustomer')}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label text-right col-lg-3 col-sm-12">First Name  <span style="color: red;">*</span></label>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <input type="text" required="" class="form-control " value="" name="first_name" placeholder="Enter First Name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label text-right col-lg-3 col-sm-12">Last Name  <span style="color: red;">*</span></label>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <input type="text" required="" class="form-control " value="" name="last_name" placeholder="Enter Last Name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label text-right col-lg-3 col-sm-12">Phone  <span style="color: red;">*</span></label>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <input type="text" required="" class="form-control " value="" name="mobile" placeholder="Enter Phone Number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label text-right col-lg-3 col-sm-12">Email  <span style="color: red;">*</span></label>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <input type="text" required="" class="form-control " value="" name="email" placeholder="Enter Email ">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label text-right col-lg-3 col-sm-12">Address  <span style="color: red;">*</span></label>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <textarea type="text" required="" class="form-control " value="" name="address" placeholder="Enter Address "></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
          </div>
        </div>
    </div>

@endsection

@push('stackScript')

    <script>
        $('.cartItemOnkeySave').on('keyup', function(e) {

            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.pos.saveInventoryItem') }}",
                data: {
                    _token: $('input[name=_token]').val(),
                    item_id: $(this).val()
                },

                success: function(response) {
                    $(".ajax-data-container-product").empty().append(response.page);
                },

            });
        });
    </script>

    <script>

        function addToCartFunction(id){
            // var that = $(this);

            var item = id;

            $.ajax({
                url: window.location.origin + `/bbs/pos/add-to-cart/${item}`,
                type: 'GET',
                cache: false,
                dataType: 'json',
                success: function(response) {
                    $(".ajax-data-container-product").empty().append(response.page);
                    if(response.message)
                    {
                        Swal.fire(response.message, '', 'warning');
                    }
                },
                error: function() {}
            });
        }
        // $(".add-to-cart").on("click", function(e) {

        //     e.preventDefault();

        //     var that = $(this);

        //     var item = $(that).attr("data-id");

        //     $.ajax({
        //         url: window.location.origin + `/bbs/pos/add-to-cart/${item}`,
        //         type: 'GET',
        //         cache: false,
        //         dataType: 'json',
        //         success: function(response) {
        //             $(".ajax-data-container-product").empty().append(response.page);
        //             if(response.message)
        //             {
        //                 Swal.fire(response.message, '', 'warning');
        //             }
        //         },
        //         error: function() {}
        //     });
        // });
    </script>
    <script>
        function updateCartQuantity(itemId) {
            var id = itemId;
            var quantity = $(".update-cart-quantity" + id).val();
            
            $.ajax({
                url: window.location.origin + `/bbs/pos/update-cart/${id}/${quantity}`,
                type: 'GET',
                cache: false,
                dataType: 'json',
                success: function(response) {
                    
                    $(".ajax-data-container-product").empty().append(response.page);                    
                    if(response.message)
                    {
                        Swal.fire(response.message, '', 'warning');
                    }
                },
                error: function(response){
                }
            });
        }
    </script>
    <script>
        function deleteCart(id) {
            var item_id = id;
            $.ajax({

                url: window.location.origin + `/bbs/pos/delete-from-cart/${item_id}`,
                type: 'GET',
                cache: false,
                dataType: 'json',
                success: function(response) {
                    $(".ajax-data-container-product").empty().append(response.page);
                },
                error: function() {}
            });

        }
    </script>

    <script> 
        $('form')
            .each(function(){
                $(this).data('serialized', $(this).serialize())
            })
            .on('change input', function(){
                $(this)             
                    .find('input:submit, button:submit')
                        .prop('disabled', $(this).serialize() == $(this).data('serialized'))
                ;
            })
            .find('input:submit, button:submit')
                .prop('disabled', false)
        ;
    </script>
@endpush
