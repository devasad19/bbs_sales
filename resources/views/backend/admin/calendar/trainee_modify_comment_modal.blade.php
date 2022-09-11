

<form action="{{ route('admin.course.storeTraineeModifyComment') }}" method="POST">
    @csrf
    <div class="modal fade" id="claimModifyForTrainee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title 44</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>

                <div class="modal-body">
                    <div class="container">
                        @csrf
                        <input type="hidden" name="user_id_val" id="user_id_val" value="">
                        <input type="hidden" name="ctld_id_val" id="ctld_id_val" value="">
                        <div class="form-group row">
                            <label class="col-form-label text-right col-lg-3 col-sm-12">Comment<span style="color: red;">*</span></label>
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <input type="text" required class="form-control" name="comment" placeholder="Comment For Changes">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send For Modify</button>
                </div>
        </div>
        </div>
    </div>
</form>


