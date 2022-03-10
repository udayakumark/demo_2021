@extends('layouts.admin-layout')
@section('title', $name.' Vendor')
@section('script-src', asset('public/admin-assets/pages/manage-dealers.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Vendor</h4></div>
                                <div class="col-4">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger print-error-msg" style="display:none">
                                    <ul></ul>
                                </div>
								@if($type==3)     
								<div class="row">
								 <div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Name</strong></td>
										<td>{{ $usermodel->first_name.' '.$usermodel->last_name }}</td>
									</tr>
									<tr>
										<td><strong>Mobile Number</strong></td>
										<td>{{ $model->mobile_number }}</td>
									</tr>
									<tr>
										<td><strong>Vendor Type</strong></td>
										<td>{{ $VendorTypeList }}</td>
									</tr>
									<tr>
										<td><strong>Payment Type</strong></td>
										<td>{{ ($usermodel->type== 'C') ? 'Sundry creditor' : 'Sundry debitor' }}</td>
									</tr>
									<tr>
										<td><strong>Email ID</strong></td>
										<td>{{ $model->email_id }}</td>
									</tr>
									<tr>
										<td><strong>Pincode</strong></td>
										<td>{{ $usermodel->pincode }}</td>
									</tr>
									<tr>
										<td><strong>Area</strong></td>
										<td>{{ $usermodel->area }}</td>
									</tr>
									<tr>
										<td><strong>City</strong></td>
										<td>{{ $usermodel->city }}</td>
									</tr>
									<tr>
										<td><strong>State</strong></td>
										<td>{{ $usermodel->state }}</td>
									</tr>
									<tr>
										<td><strong>Address</strong></td>
										<td>{{ $usermodel->address }}</td>
									</tr>
								</tbody>
								</table>
								</div>
								<div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Profile Image</strong></td>
										<td>  @if(isset($usermodel->profile_image) && $usermodel->profile_image!='')
											<img src="{{URL::to('/public/')}}{{$usermodel->profile_image}}" width="100" height="100" />  
											@else  
											@endif</td>
									</tr>
									<tr>
										<td><strong>Aadhar card No.</strong></td>
										<td>{{ $usermodel->aadhar }}</td>
									</tr>
									<tr>
										<td><strong>Aadhar Image</strong></td>
										<td>@if(isset($usermodel->aadhar_image) && $usermodel->aadhar_image!='')
											<img src="{{URL::to('/public/')}}{{$usermodel->aadhar_image}}" width="100" height="100" />  
											@else
											@endif</td>
									</tr>
									<tr>
										<td><strong>Bank Name</strong></td>
										<td>{{ $usermodel->bank_name }}</td>
									</tr>
									<tr>
										<td><strong>Bank Branch</strong></td>
										<td>{{ $usermodel->bank_branch }}</td>
									</tr>
									<tr>
										<td><strong>Account Number</strong></td>
										<td>{{ $usermodel->account_number }}</td>
									</tr>
									<tr>
										<td><strong>Current Balance</strong></td>
										<td>{{ $usermodel->current_balance }}</td>
									</tr>
									<tr>
										<td><strong>IFSC Code</strong></td>
										<td>{{ $usermodel->ifsc_code }}</td>
									</tr>
									<tr>
										<td><strong>GST</strong></td>
										<td>{{ $usermodel->gst }}</td>
									</tr>
								</tbody>
								</table>
								</div>
								<div class="col-md-12 form-group text-center">
									<a href="{{ url('/admin/vendors') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                 </div>	
								</div>
								@else
								<div class="col-lg-12 col-sm-12">
                                <form method="POST" action="{{ url($action) }}" redirect-url="{{ url($redirectUrl) }}" id="form" novalidate="">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-3 form-group">
                                            <label>Name*</label>
                                            <input type="text" name="first_name" autocomplete="OFF" placeholder="Enter First Name" class="form-control" value="{{ $usermodel->first_name }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Mobile Number*</label>
                                            <input type="text" maxlength="10" name="mobile_number" placeholder="Enter Mobile Number"  autocomplete="OFF" class="form-control" value="{{ $model->mobile_number }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Select Vendor Type*</label>
                                            <select class="form-control select2" name="account">
                                                <option value="">Select Vendor Type</option>
                                                @foreach ($VendorTypeList as $vendor)
                                                    <option value="{{ $vendor['id'] }}" {{ $usermodel->account== $vendor['id'] ? 'selected' : '' }} >{{ $vendor['name'] }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Select Payment Type*</label>
                                            <select class="form-control select2" name="type">
                                                <option value="">Select Payment Type</option>
                                                <option value="C" {{ $usermodel->type== 'C' ? 'selected' : '' }}>Sundry creditor</option>
                                                <option value="D" {{ $usermodel->type== 'D' ? 'selected' : '' }}>Sundry debitor</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Email ID</label>
                                            <input type="text" name="email_id" placeholder="Enter Email ID"  class="form-control" value="{{ $model->email_id }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Pincode</label>
                                            <input type="text" maxlength="6" name="pincode" id="pincode" autocomplete="OFF" placeholder="Enter Pincode" class="form-control" value="{{ $usermodel->pincode }}"  required>
                                        </div>
										  <div class="col-md-3 form-group">
                                            <label>Area</label>
                                            <select class="form-control select2" name="area" id="area" aria-hidden="true">
                                                <option value="">Select Area</option>
												@if($areaList) 
													@foreach ($areaList as $area)
														<option value="{{ $area}}" {{ $usermodel->area == $area ? 'selected' : '' }} >{{ $area }}</option>
													@endforeach
												 @endif
												
                                            </select>
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>State</label>
                                            <input type="text" name="state" id="state" autocomplete="OFF" placeholder="" class="form-control" value="{{ $usermodel->state }}" readonly="true">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>City</label>
                                            <input type="text" name="city" id="city" autocomplete="OFF" placeholder="" class="form-control" value="{{ $usermodel->city }}" readonly="true">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Address</label>
                                            <textarea rows="4" name="address" placeholder="Enter Address" class="form-control">{{ $usermodel->address }}</textarea>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Profile Image</label>
                                            <input type="file" name="profile_image" placeholder="Select Profile Image" class="form-control" >
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Aadhar Number</label>
                                            <input type="number" max="12" name="aadhar" placeholder="Enter Aadhar Number" class="form-control" value="{{ $usermodel->aadhar }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Aadhar Image</label>
                                            <input type="file" name="aadhar_image" placeholder="Select Aadhar Image" class="form-control" >
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Bank Name</label>
                                            <input type="text" name="bank_name" placeholder="Enter Bank Name" class="form-control" value="{{ $usermodel->bank_name }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Bank Branch</label>
                                            <input type="text" name="bank_branch" placeholder="Enter Bank Branch" class="form-control" value="{{ $usermodel->bank_branch }}">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Account Number</label>
                                            <input type="text" name="account_number" placeholder="Enter Account Number" class="form-control" value="{{ $usermodel->account_number }}">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Current Balance</label>
                                            <input type="text" name="current_balance" placeholder="Enter Current Balance" class="form-control" value="{{ $usermodel->current_balance }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>IFSC Code</label>
                                            <input type="text" name="ifsc_code" placeholder="Enter IFSC No" class="form-control" value="{{ $usermodel->ifsc_code }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>GST Number</label>
                                            <input type="number" max="18" name="gst" placeholder="Enter GST Number" class="form-control" value="{{ $usermodel->gst }}">
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/vendors') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                            <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                                                {{ $name }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
								@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
	<script>
		var baseUrl = '<?php echo url('/'); ?>';
		$('#pincode').change(function() {
            var pin_code = this.value;
			$.ajax(baseUrl+'/admin/vendor/getPincodedetails?pin_code='+pin_code,   // request url
			{
				success: function (data, status, xhr) {// success callback function
					var response = data.data;
					$('#area').html('');
					if(data.status) {
						$.each(response.area, function (i, value) {
							$('#area').append('<option value=' + value + '>' + value + '</option>');
						});
					} else {
						$('#pincode').val ('');
						$('#area').append('<option value=' + data.message + '>' + data.message + '</option>');
					}
					$("#city").val(response.city);
					$("#state").val(response.state);
				}
			});
        });
	</script>
@endsection