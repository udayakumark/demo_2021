<div class="alert alert-danger print-error-msg" style="display:none">
<ul></ul>
</div>
<form method="POST" action="{{ url($action) }}" class="needs-validation" id="form" novalidate="">
@csrf
<div class="form-group">
  <label>City Name</label>
  <select class="form-control select2" name="city">
      <option value="">Select City</option>
      @foreach ($cityList as $city)
      <option value="{{ $city->id }}" {{ $model->city_id==$city->id ? 'selected' : '' }}>{{ $city->name }}</option>
      @endforeach
  </select> 
  @error('city')
  <div class="invalid-feedback">{{$message}}</div>
  @enderror
</div>
<div class="form-group">
  @if($model->city_id=="")
  <label>City Pincode (Multiple pincode seperate by ,)</label>
  @else
  <label>City Pincode</label>
  @endif
  <input type="text" name="pincode" placeholder="Enter Pincode" class="form-control {{ $errors->has('pincodes') ? 'is-invalid' : '' }}" value="{{ $model->pincode }}">
  @error('pincodes')
  <div class="invalid-feedback">{{$message}}</div>
  @enderror
</div>
<div class="form-group text-center">
  <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
  {{ $name }}
  </button>
</div>
</form>