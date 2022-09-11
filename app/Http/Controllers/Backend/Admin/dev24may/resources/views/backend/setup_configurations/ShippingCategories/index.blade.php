@extends('backend.layouts.app')
@section('content')
@php $total_area_id=0; @endphp


<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="h4" style="text-transform: capitalize;"></h4>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <form  onsubmit="return confirm('Do you really want to submit the form?');"    action="{{ route('shippingcat.update') }}" method="POST" enctype="multipart/form-data">
            <h5 class="text-center"> <button id="delete_button" type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button></h5>
            @csrf
            <table class="shipcat" border="1" width="100%">
                <thead>
                    <tr class=" bg-light">
                        <th class="pl-1" data-breakpoints="lg">#</th>
                        <th class="pl-1 w20">{{translate('Category')}}</th>
                        <th class="pl-1 ">{{translate('SubCategory')}}</th>
                        @foreach ($shipping_cost_area['area'] as $area)
                        <th class="pr-1 text-right">{{$area}}</th>@php $total_area_id++; @endphp
                        @endforeach
                        <input required name="total_area_id" type="text" style="width:100%;border:0px" value="{{$total_area_id}}" class="d-none">
                        <th class="pr-1 text-right" colspan="2"  data-breakpoints="lg" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter=0; $v=1; $ssl=1;   @endphp

                    @foreach ($shipping_categories as $key => $value)

                        <tr title="" id="tr{{$ssl++}}">
                            @if($counter==0)<td  class="pl-1  bg-light" rowspan="{{$total_child_ofparent[$value['parent_id']]}}">{{$v++}}</td>
                            <td  class="pl-1 bg-light" rowspan="{{$total_child_ofparent[$value['parent_id']]}}">

                                <h6>{{$cat_byid[$value['parent_id']]}}

 <span id="save_cost_increment" onclick="save_cost_increment('{{$value['parent_id']}}')"  class="btn btn-xs  "><svg xmlns="http://www.w3.org/2000/svg" onclick="save_cost_increment('{{$value['parent_id']}}')"  xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"   height="15px" viewBox="0 0 407.096 407.096" style="enable-background:new 0 0 407.096 407.096;" xml:space="preserve"><g><g><path d="M402.115,84.008L323.088,4.981C319.899,1.792,315.574,0,311.063,0H17.005C7.613,0,0,7.614,0,17.005v373.086 c0,9.392,7.613,17.005,17.005,17.005h373.086c9.392,0,17.005-7.613,17.005-17.005V96.032 C407.096,91.523,405.305,87.197,402.115,84.008z M300.664,163.567H67.129V38.862h233.535V163.567z"/><path d="M214.051,148.16h43.08c3.131,0,5.668-2.538,5.668-5.669V59.584c0-3.13-2.537-5.668-5.668-5.668h-43.08 c-3.131,0-5.668,2.538-5.668,5.668v82.907C208.383,145.622,210.92,148.16,214.051,148.16z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
 </span>
 <span onclick="expand('{{$value['parent_id']}}')"  class="btn btn-xs  ">
 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" height="15px" viewBox="0 0 256 256" xml:space="preserve"><desc>Created with Fabric.js 1.7.22</desc><defs></defs><g transform="translate(128 128) scale(0.72 0.72)" style=""><g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(-175.05 -175.05000000000004) scale(3.89 3.89)" ><path d="M 58.921 90 H 31.079 c -1.155 0 -2.092 -0.936 -2.092 -2.092 V 2.092 C 28.988 0.936 29.924 0 31.079 0 h 27.841 c 1.155 0 2.092 0.936 2.092 2.092 v 85.817 C 61.012 89.064 60.076 90 58.921 90 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" /><path d="M 90 31.079 v 27.841 c 0 1.155 -0.936 2.092 -2.092 2.092 H 2.092 C 0.936 61.012 0 60.076 0 58.921 V 31.079 c 0 -1.155 0.936 -2.092 2.092 -2.092 h 85.817 C 89.064 28.988 90 29.924 90 31.079 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" /></g></g></svg>
</span>
</h6>

                                <textarea ondblclick="expand('{{$value['parent_id']}}')" id="cost_increment{{$value['parent_id']}}" name="cost_increment" rows="1" cols="35">{{app('App\Http\Controllers\ShippingController')->cost_increment_value($value['parent_id'])}}</textarea>
                                <div id="update{{$value['parent_id']}}"></div>

                            </td>@endif

                            <td rowspan="1"    class="pl-1">
                            <input required name="shipcat[]" type="text" style="width:100%;border:0px" value="{{$value['id']}}" class="d-none">
                            <input required name="shipcat[]" type="text" style="width:100%;border:0px" value="{{$value['parent_id']}}" class="d-none">

                            <i title="Last Update: {{friendlytime($value['updated_at'])}} by  {{user_by_id(9)->name}} " data-toggle="tooltip" data-placement="top">
                            <input id="trspan{{$ssl-1}}" required name="shipcat[]" type="text" style="width:100%;border:0px" value="{{$value['name']}}">
                            <input  id="tr{{$ssl-1}}x" required name="shipcat[]" type="text" style="width:100%;border:0px" value="{{$value['is_active']}}" class="d-none"></i>
                            </td>

                            @php
                                $cost_array = json_decode($value['cost'], true); $sl=0;

                                foreach($cost_array['cost'] as $key=> $single_cost){
                                    $cost_by_area[$cost_array['area_id'][$key]]=$single_cost;
                                }

                            @endphp
                            @foreach ($shipping_cost_area['area'] as $key=>$area)
                            <td style="width:10%" rowspan="1" class="pr-1 text-right">
                            <input required name="shipcat[]" type="text" style="width:100%;border:0px" value="{{$shipping_cost_area['id'][$key]}}" class="d-none">
                            <input name="shipcat[]" required class="pr-1 text-right" type="text" style="width:100%;border:0px" value="@php try {  echo $cost_by_area[$shipping_cost_area['id'][$key]]; } catch (\Exception $e) { } @endphp">

                            </td>
                            @endforeach
                            <td class="text-center" style="width:5%">
                            <span onclick="deleteQueue('tr{{$ssl-1}}')" title="Add to delete queue" class="badge badge-inline badge-danger text-center">-</span>
                            </td>
                            @if($counter==0)<td  rowspan="{{$total_child_ofparent[$value['parent_id']]}}" class="pr-1 text-right bg-light">
                               @php $pi=$value['parent_id']; $tc=($total_child_ofparent[$value['parent_id']]+$ssl)-2;
                               $pn=trim($cat_byid[$value['parent_id']]); @endphp
                               <span  title="Add new subcategory" onclick="addSubcategory({{$pi}},{{$tc}},'{{$pn}}')" class="badge badge-inline badge-success">+</span>
                             </td>@endif  @php if($counter==0){$counter=$total_child_ofparent[$value['parent_id']];} $counter=$counter-1; @endphp
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            <p>Hint for special shipping: 0-*=0%/*flat_seller*/ [3000=100%,4000=100%,5000=100%]</p>
                <h5 class="text-center pt-1"> <button   type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button></h5>
        </form>
    </div>
    </form>
</div>

<!-- delete Modal -->
<input id="deleteQueues" value="" class="d-none"/>
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">Delete Queue</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">Make sure you add to the delete queue?</p>
                <p class="mt-1"><i class="cp">*Click on "Confirm Delete Queue?" button After Adding to deleteQueue</i></p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">Cancel</button>
                <p  onClick="confirmQueue()"  class="btn btn-primary mt-2">Done</p>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
@endsection


<script>
  /*  var styles = `
    .shipcat tr:hover {
        background-color: blue;
    }

    .shipcat tr:hover td {
        background-color: transparent;
    }
    span.badge.badge-inline {
    cursor: pointer;
}
.deleteable {
    background-color: red;
    opcity: .5;
    pointer-events: none;
    padding:2px;
}

.w20{
     width: 20% !important;
}
.cp{
    color:pink;
}
`;

var styleSheet = document.createElement("style")
styleSheet.type = "text/css"
styleSheet.innerText = styles
document.head.appendChild(styleSheet)*/



function addSubcategory(parent,position,parent_name) {
     var tr= `
    <tr id="new`+position+`">
   <td rowspan="1"  ><span onclick="removeCat(\'new`+position+`\')" class="badge badge-inline badge-danger">-</span></td>
   <td rowspan="1"  ></td>
   <td rowspan="1" class="pl-1">
      <input required="" name="shipcat[]" type="text" style="width:100%;border:0px" value="x" class="d-none">
	  <input required="" name="shipcat[]" type="text" style="width:100%;border:0px" value="`+parent+`" class="d-none">
      <input required="" placeholder="Enter SubCategory of `+parent_name+`" name="shipcat[]" type="text" onClick="this.select();"  style="width:100%;border:0px" value="">
      <input  id="" required name="shipcat[]" type="text" style="width:100%;border:0px" value="1" class="d-none">
   </td>
   <td style="width:10%" rowspan="1" class="pr-1 text-right">
      <input required="" name="shipcat[]" type="text" style="width:100%;border:0px" value="1" class="d-none">
      <input name="shipcat[]" required="" class="pr-1 text-right" type="text" style="width:100%;border:0px" value="">
   </td>
   <td style="width:10%" rowspan="1" class="pr-1 text-right">
      <input required="" name="shipcat[]" type="text" style="width:100%;border:0px" value="2" class="d-none">
      <input name="shipcat[]" required="" class="pr-1 text-right" type="text" style="width:100%;border:0px" value="">
   </td>
   <td style="width:10%" rowspan="1" class="pr-1 text-right">
      <input required="" name="shipcat[]" type="text" style="width:100%;border:0px" value="3" class="d-none">
      <input name="shipcat[]" required="" class="pr-1 text-right" type="text" style="width:100%;border:0px" value="">
   </td>
   <td style="width:5%" rowspan="1" class="pr-1 text-right">


   </td>
</tr>`;

    $('#tr'+position).after(tr);
}


function removeCat(remove_id) {
    $( "#"+remove_id ).remove();
}

function ConfirmSubmit() {
     $("#delete-modal").modal("show");
}

function deleteQueue(remove_id) {
    $("#"+remove_id).addClass("deleteable");
    $("#"+remove_id+"x").val("0");
    $( "#delete_button" ).html("<i class='las la-trash'>Confirm Delete Queue?</i>");
    $("#delete-modal").modal("hide");
}

function save_cost_increment(id) {
    var ci= $("#cost_increment"+id).val();
    var res='';
    $.post('{{ route('ShippingController.update.CostIncrement') }}', {_token:'{{ csrf_token() }}', ci:ci, id:id}, function(data){
       if(data=='Update Successful'){ $("#update"+id).html('<p class="text-success">'+data+'</p>');}else{
          $("#update"+id).html('<p class="text-danger">Error! '+data+'</p>');
      }
      res=data;
    });
    setTimeout( function() {
        if(res==''){
            $("#update"+id).html('<p class="text-danger">Error! Something went wrong </p>');
        }
     }, 300);


}

var last_id='';
function expand(id) {
    $("#cost_increment"+id).animate({height:"150px"});
   // if(last_id!=''){$("#cost_increment"+last_id).animate({height:"20px"});}
    $("#update"+last_id).html('');
    last_id=id;
}

</script>
