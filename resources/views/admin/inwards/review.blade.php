@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
        	<form method="POST" action="{{ route("admin.inwards.review_update") }}" enctype="multipart/form-data">
			@csrf
	        	<div class="panel panel-default">
	                <div class="panel-heading">
	                    Review recently added patient details
	                </div>
	                <div class="panel-body">
	                	<div class="row">
	                		<div class="col-md-4">
		                        <div class="form-group">
		                            <label for="name">Facility</label>
		                            <span style="display: block;padding-top: 10px;">{{$data['facility_name']}}</span>
		                            <select class="form-control" style="display: none;" name="facility_id" data-validation="required">
		                            	<option value="">Please select Facility</option>
		                            	@foreach($data['facilities'] as $id => $facility)
		                            		<option value="{{$facility->id}}" <?php echo ($data['facilityId'] == $facility->id)?'selected':'';?>>{{$facility->name}}</option>
		                            	@endforeach
		                            </select>
		                        </div>
	                        </div>
	                        <div class="col-md-4">
		                        <div class="form-group">
		                            <label>Sample Date</label>
		                            <span style="display: block;padding-top: 10px;">{{$data['receivedAt']}}</span>
		                            <input style="display: none;" type="text" class="form-control pull-right" id="datepicker" name="received_at" data-validation="required">
		                            <input type="hidden" name="received_at_last_value" value="{{$data['receivedAt']}}">
		                        </div>
	                        </div>
	                        <div class="col-md-4">
		                        <div class="form-group">
		                            <label class="required">No. of Samples</label>
		                            <span style="display: block;padding-top: 10px;padding-left: 40px;">{{count($data['newInwards'])}}</span>
		                        </div>
	                        </div>
	                    </div>	                                     
	                </div>
	            </div>
	        	<div class="box">
					<div class="box-header">
						<h3 class="box-title">Patient Details</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body table-responsive no-padding">
						<table class="table table-hover" id="review_table">
							<tbody>
								<tr>
									<th>Sample ID</th>
									<th>Name</th>
									<th>Patient Reference</th>
									<th>Contact No</th>
									<th>Specimen Type</th>
									<th style="width: 5%;">Age</th>
									<th>Gender</th>
									<th>Address</th>
									<th style="text-align: center;">Reject</th>
									<th>Actions</th>
								</tr>
								<?php 
									$cnt = 1; 
									$genderArr = array('M'=>'Male','F'=>'Female','O'=>'Other');
								?>
								@foreach($data['newInwards'] as $inward)
									<tr>
										<td>
											{{$inward->sample_id}}
											<input type="hidden" name="detail[{{$cnt}}][id]" class="form-control" value="{{$inward->id}}">
										</td>
										<td>
											<span class="last_value">{{$inward->name}}</span>
											<div class="form-group input_value"> 
												<input type="text" name="detail[{{$cnt}}][name]" class="form-control" value="{{$inward->name}}" data-validation="required">
											</div>
										</td>
										<td>
											<span class="last_value">{{$inward->patient_id}}</span>
											<div class="form-group input_value"> 
												<input type="text" name="detail[{{$cnt}}][patient_id]" class="form-control" value="{{$inward->patient_id}}">
											</div>
										</td>
										<td>
											<span class="last_value">{{$inward->contact_no}}</span>
											<div class="form-group input_value"> 
												<input type="text" name="detail[{{$cnt}}][contact_no]" class="form-control" value="{{$inward->contact_no}}" data-validation="number length" data-validation-length="10" data-validation-optional="true" data-validation-error-msg="The input value has to be numeric (10 digits)">
											</div>
										</td>
										<td>
											<span class="last_value">{{$inward->sample_type->name}}</span>
											<div class="form-group input_value"> 
												<select class="form-control" name="detail[{{$cnt}}][sample_type_id]">
					                            	<option>Select</option>
					                            	@foreach($data['sampleTypes'] as $id => $type)
					                            		<option value="{{$type->id}}" <?php echo ($type->id==$inward->sample_type->id)?'selected':'';?>>{{$type->name}}</option>
					                            	@endforeach
					                            </select>
											</div>
										</td>
										<td>
											<span class="last_value">{{$inward->age}}</span>
											<div class="form-group input_value"> 
												<input type="text" name="detail[{{$cnt}}][age]" class="form-control" value="{{$inward->age}}">
											</div>
										</td>
										<td>
											<span class="last_value">
												<?php 
												echo isset($inward->sex)?$genderArr[$inward->sex]:'';
												?>
											</span>
											<div class="form-group input_value">
												<select class="form-control" name="detail[{{$cnt}}][sex]">
													<option value="">Select</option>
													<option value="M" <?php echo ($inward->sex == 'M')?'selected':'';?>>Male</option>
													<option value="F" <?php echo ($inward->sex == 'F')?'selected':'';?>>Female</option>
													<option value="O" <?php echo ($inward->sex == 'O')?'selected':'';?>>Other</option>
												</select>
											</div>
										</td>
										<td>
											<span class="last_value">{{$inward->address}}</span>
											<div class="form-group input_value"> 
												<input type="text" name="detail[{{$cnt}}][address]" class="form-control" value="{{$inward->address}}">
											</div>
										</td>
										<td style="width: 16%; text-align: center;">
											<button class="btn btn-danger reject-button">Reject</button>
											<select class="form-control" name="detail[{{$cnt}}][rejected_reason_id]" style="display: none;">
				                            	<option value="0">Select Reason</option>
				                            	@foreach($data['rejectedReasons'] as $reason)
				                            		<option value="{{$reason->id}}">{{$reason->reason}}</option>
			                            		@endforeach
				                            </select>
										</td>
										<td>
											<a href="" alt="Edit" class="edit_details"><span class="glyphicon glyphicon-pencil" style="font-size: 143%;color: #3c8dbc;cursor: pointer;"></span></a>
										</td>
									</tr>
									<?php $cnt++; ?>
								@endforeach
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
					<div class="box-footer clearfix">
						<div class="form-group">
	                        <button class="btn btn-success pull-right" type="submit">Save</button>
	                    </div>
					</div>
				</div>
				<!-- /.box -->
			</form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		$.validate();
		//Date picker
		var receivedAt = $("input[name=received_at_last_value]").val();

	    $('#datepicker').datetimepicker({
	    	defaultDate: new Date(receivedAt),
	    	format: 'DD/MM/YYYY hh:mm a'
	    });

		$('.reject-button').on('click', function(e){
			e.preventDefault();
			if(confirm("Do you really want to reject this sample?")){
				$(this).hide();
				$(this).next().show();
			}
		});

		$('.edit_details').on('click', function(e){
			e.preventDefault();

			$(this).closest('tr').find('.last_value').hide();
			$(this).closest('tr').find('.input_value').show();
		});
	});
</script>
@endsection