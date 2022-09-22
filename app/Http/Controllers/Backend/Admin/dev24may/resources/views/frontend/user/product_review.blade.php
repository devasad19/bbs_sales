@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Review') }}</h5>
        </div>
      
            <div class="card-body">
                <table class="table aiz-table mb-0">

				<thead>
					<tr>
						<th data-breakpoints="lg">#</th>
						<th>{{translate('Product')}}</th>
						<th>{{translate('Photos')}}</th>
				
			
						<th>{{translate('Rating')}}</th>
						<th data-breakpoints="lg">{{translate('Comment')}}</th>
						<th data-breakpoints="lg">{{translate('OrderCode')}}</th>
						<th data-breakpoints="lg">{{translate('Created')}}</th>
						<th data-breakpoints="lg">{{translate('Status')}}</th>
					</tr>
				</thead>
				<tbody>
					@foreach($reviews as $key => $review)
						@if ($review->product != null && $review->user != null)
							<tr>
								<td>{{ ($key+1) + ($reviews->currentPage() - 1)*$reviews->perPage() }}</td>
								<td>
									<a href="{{ route('product', $review->product->slug) }}" target="_blank" class="text-reset text-truncate-2">{{ $review->product->getTranslation('name') }} <span class="badge badge-inline badge-info">Edit Review</span></a>
								</td>
								<td> 
															{{--Start review image--}}
															@foreach (explode(',',$review->photos)  as $photos_each ) @if($photos_each!='')
															
																<span id="{{$photos_each}}"   >
																<img id="main{{$photos_each}}" style="height:100px;width:auto" onclick="image_view({{$photos_each}},{{$review->id}})"
																class="img-fluid lazyload pointer responsive"
																src="{{ static_asset('assets/img/placeholder.jpg') }}"
																data-src="{{ uploaded_asset($photos_each) }}"
																onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
																></span>
															  @endif
															@endforeach
															<div class="col-12 p-3" style="padding-left:5px !important"> <div id="place{{$review->id}}"></div> </div>          
															</div>   
															{{--End review image--}}

								</td>
								

								<td>{{ $review->rating }}</td>
								<td>{{ $review->comment }}</td>
								

								@php
								 $review_data= DB::table('orders')->where('id', '=', $review->order_id)->first(); 
								@endphp

								<td>
								@if(empty($review_data))  
								@else 
									{{$review_data->code}}
								@endif
								</td>
								 
								<td>{{ friendlytime($review->created_at) }}</td>
								<td> 
								<?php if($review->status == 1) {echo "<span class=\"badge badge-inline badge-success\">Approved</span>";}else {echo "<span class=\"badge badge-inline badge-danger\">Pending</span>";} ?>
								
							 
								</td>
							</tr>
						@endif
					@endforeach
				</tbody>


                </table>
				<div class="aiz-pagination">
					{{ $reviews->appends(request()->input())->links() }}
				</div>
            </div>
     
    </div>
@endsection