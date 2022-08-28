<table class="table table-separate table-head-custom table-checkable table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th class="text-left">District</th>
            <th class="text-left">Total Response</th>
            <th >Year</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @if ($lists->count() > 0)
            @php
                $i = ($lists->currentPage() - 1) * $lists->perPage() + 1;
            @endphp


            @foreach ($lists as $list)
                <tr>
                    <td>{{ $i }}</td>
                <td class="text-left">{{ $list->district ? $list->district->name_en : '' }}</td>
                <td class="text-left">{{ $list->surveyCountPenirialDistrict( $list->district_id)}}</td>
                <td>{{ $list->year ? $list->year : ''}}</td>
                <td>
                    
                    <button class="btn btn-success btn-sm w3-circle forward_btn" title="Forward" data-id="{{ $list->id }}">Forward</button>
                    <a href="{{ route('admin.processingList.upazilaCropCropCuttingData', $list) }}" class="btn btn-info btn-sm">Details</a>
                    <a href="{{ route('admin.processingList.backwardToDistrict',$list) }}" class="btn btn-danger btn-sm">Backward</a>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModalCenter{{$list->id}}">
                        Comment
                    </button>
                </td>
                @php
                    $i++;
                @endphp
                </tr>

                {{-- Modal --}}
                <div class="modal fade" id="exampleModalCenter{{$list->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-bottom" role="document">
                       <div class="modal-content">
                          <div class="modal-header bg-info">
                             <h5 class="modal-title text-white" id="exampleModalLongTitle">Provide your comment</h5>
                             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">×</span>
                             </button>
                          </div>
                          <form action="{{ route('admin.processingList.commentDivisionTofsil4', $list) }}" method="post">
                            @csrf
                             
                             <div class="modal-body">
                                <div class="form-group row">
                                   <label class="col-form-label col-12">বিভাগ/জেলা/উপজেলা/থানায় গত বছর হতে চলতি বছরে উৎপাদন ও ফলন হার হ্রাস/বৃদ্ধির কারণ:</label>
                                   <div class="col-lg-12">
                                      <textarea name="comment_division_tofsil4" class="form-control" required></textarea>
                                   </div>
                                </div>
                             </div>
                             <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                             </div>
                          </form>
                       </div>
                    </div>
                 </div>
                {{-- end Modal --}}
            @endforeach

            
        @else
            <tr class="odd">
                <td valign="top" colspan="11" class="dataTables_empty">No matching records found</td>
            </tr>
        @endif
    </tbody>
</table>

@push('stackScript')
    <script> 
        $(".forward_btn").click(function(e) {
            var data_id = $(this).attr("data-id");
            var url = '<a href="{{route("admin.processingList.forwardToDg", ":id")}}" class="swal2-confirm swal2-styled" title="Forward">Confirm</a>';
            url = url.replace(':id', data_id );
            
            Swal.fire({
                title: 'Are you sure want to forward ?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: url,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Data Forwarded Successfully!', '', 'success')
                } else if (result.dismiss === "cancel") {
                    Swal.fire('Canceled', '', 'error')
                }
            })
        });

    </script>
@endpush