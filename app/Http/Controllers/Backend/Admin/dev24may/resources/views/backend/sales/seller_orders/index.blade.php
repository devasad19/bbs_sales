@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Seller Orders') }}</h5>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="seller_id" name="seller_id">
                        <option value="">{{ translate('All Sellers') }}</option>
                        @foreach (App\Models\Seller::all() as $key => $seller)
                            @if ($seller->user != null && $seller->user->shop != null)
                                <option value="{{ $seller->user->id }}" @if ($seller->user->id == $seller_id) selected @endif>
                                    {{ $seller->user->shop->name }} ({{ $seller->user->name }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">
        <!-----table modification------>
        <table class="table aiz-table mb-0">
            <thead>
            <tr>
                <th>#</th>
                <th data-breakpoints="md">{{ translate('Date') }}</th>
                <th>{{ translate('Order') }}</th>
                <th>{{ translate('Seller') }}</th>
                <th data-breakpoints="md">{{ translate('Customer') }}</th>
                <th data-breakpoints="md">{{ translate('Mobile No') }}</th>
                <th data-breakpoints="md">{{ translate('Amount') }}</th>
                <th data-breakpoints="md">{{ translate('Method') }}</th>
                <th data-breakpoints="md">{{ translate('Payment') }}</th>
                <th data-breakpoints="md">{{ translate('Delivery') }}</th>

                <th class="text-right" width="15%">{{translate('options')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($orders as $key => $order)
            <tr>
                <td>
                    {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                </td>
                <td><?php $datee = explode(' ', $order->created_at) ?>{{ $datee[0] }}</td>
                <td>
                    {{ $order->code }}@if($order->viewed == 0) <span class="badge badge-inline badge-info">{{translate('New')}}</span>@endif
                </td>
                <td>
                    @if($order->seller)
                    {{ $order->seller->name }}
                    @endif
                </td>
                <td>
                    @if ($order->user != null)
                    {{ $order->user->name }}
                    @else
                    {{$order->guest_id}}
                    @endif
                </td>
                <td>
                    @if ($order->user != null)
                    {{ $order->user->phone }}
                    @endif
                </td>
                <td>
                    {{ single_price($order->grand_total) }}
                </td>
                <td>
                    {{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}
                </td>
                <td>
                    @if ($order->payment_status == 'paid')
                    <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                    @else
                    <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                    @endif
                </td>
                <td>
                    @php
                    $status = $order->delivery_status;
                    @endphp
                    {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                </td>
                <td class="text-right">
                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('seller_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                        <i class="las la-eye"></i>
                    </a>
                    <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                        <i class="las la-download"></i>
                    </a>
                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Delete') }}">
                        <i class="las la-trash"></i>
                    </a>
                </td>
                @endforeach
            </tbody>
        </table>
        <!----end table modification----->

        <div class="aiz-pagination">
            {{ $orders->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function sort_orders(el){
            $('#sort_orders').submit();
        }
    </script>
@endsection
