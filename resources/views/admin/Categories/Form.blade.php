<div class="alert alert-danger print-error-msg" style="display:none">
<ul></ul>
</div>
<form method="POST" action="{{ url($action) }}" class="needs-validation" id="form" novalidate="">
@csrf
<div class="form-group">
  <label>Category Name</label>
  <input type="text" name="category_name" placeholder="Enter Category Name" class="form-control {{ $errors->has('category_name') ? 'is-invalid' : '' }}" value="{{ $model->category_name }}">
  @error('category_name')
  <div class="invalid-feedback">{{$message}}</div>
  @enderror
</div>
<div class="form-group">
  <label>Category Code</label>
  <input type="text" name="category_code" placeholder="Enter Category Code" class="form-control {{ $errors->has('category_code') ? 'is-invalid' : '' }}" value="{{ $model->category_code }}">
  @error('category_code')
  <div class="invalid-feedback">{{$message}}</div>
  @enderror
</div>
<div class="form-group text-center">
  <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
  {{ $name }}
  </button>
</div>
</form>