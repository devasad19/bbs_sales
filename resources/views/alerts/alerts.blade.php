@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible" id="noprintbtn">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fa fa-ban"></i> {{ __('Alert!') }}</h5>
  {{ __(Session::get('error')) }}
</div>
@endif

@if(Session::has('info'))
<div class="alert alert-info alert-dismissible" id="noprintbtn">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fa fa-info"></i> {{ __('Alert!') }}</h5>
  {{ __(Session::get('info')) }}
</div>
@endif

@if(Session::has('warning'))
<div class="alert alert-warning alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fa fa-exclamation-triangle"></i> {{ __('Alert!') }}</h5>
  {{ __(Session::get('warning')) }}
</div>
@endif

@if(Session::has('success'))
<div class="alert alert-success alert-dismissible" id="noprintbtn">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fa fa-check"></i> {{ __('Success!') }}</h5>
  {{ __(Session::get('success')) }}
</div>
@endif

<div>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
</div>