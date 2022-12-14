<table class="table table-separate table-head-custom table-checkable table-striped" >
    <thead>
        <tr>
            <th>#</th>
            <th class="text-left">Questions</th>
            <th class="text-left">Answer</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @if ($faqs->count() > 0)
        @php
            $i = (($faqs->currentPage() - 1) * $faqs->perPage() + 1);
        @endphp
            @foreach ($faqs as $faq)
                <tr>
                    <td>
                        {{$i}}
                    </td>
                    <td align="left">
                        {{custom_name($faq->question,50)}}
                    </td>
                    <td align="left">
                        {{custom_name($faq->answer,50)}} 
                        
                    </td>
                    
                    <td>
                        @if ($faq->status == 1)
                            <span class="label label-lg font-weight-bold label-light-success label-inline">Active</span>
                        @else
                            <span class="label label-lg font-weight-bold label-light-danger label-inline">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @can('edit_faq')
                            
                            <a href="{{route('admin.faq.edit',$faq)}}" class="btn btn-sm btn-clean btn-icon" title="Edit">
                                <i class="la la-edit text-warning"></i>
                            </a>
                        @endcan

                        @can('status_faq') 
                            @if ($faq->status == true)
                                
                            <button  class="btn btn-sm btn-clean btn-icon" data-toggle="modal" data-target="#deleteApplicationPurpost{{$faq->id}}">                                                         
                                <i class="la la-trash text-danger"></i>
                            </button>
                            @else
                            <button  class="btn btn-sm btn-clean btn-icon" data-toggle="modal" data-target="#deleteApplicationPurpost{{$faq->id}}">                                                         
                                
                                <i class="la la-check-circle text-success la-2x"></i>
                            </button>
                            @endif
                        @endcan
                        
                        {{-- delete modal --}}
                        <div id="deleteApplicationPurpost{{$faq->id}}" class="modal fade" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header py-5">
                                        <h5 class="modal-title">{{$faq->status == true ? 'Disable' : 'Enable'}} Designation
                                        <span class="d-block text-muted font-size-sm"></span></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <i aria-hidden="true" class="ki ki-close"></i>
                                        </button>
                                    </div>
                                    <form class="form" action="{{route('admin.faq.destroy',$faq)}}" method="post">
                                        <div class="modal-body">
                                                @csrf
                                                <div class="container">
                                                    Do you want to {{$faq->status == true ? 'disable' : 'enable'}} applicattion purpose ?
                                                </div>                    
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-sm {{$faq->status == true ? 'btn-danger' : 'btn-primary'}} " type="submit">{{$faq->status == true ? 'Disable' : 'Enable'}}</button>
                                            
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        @else
            <tr class="odd"><td valign="top" colspan="11" class="dataTables_empty">No matching records found</td></tr>
        @endif
    </tbody>
</table>