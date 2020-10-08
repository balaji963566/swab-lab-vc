<form method="POST" action="{{ route("admin.inwards.editSample") }}" enctype="multipart/form-data" id="edit_samples_form">
@csrf
	<div class="form-group row">
        <label class="required col-md-4" for="name">Facility</label>
        <div class="col-md-8">
	        <select class="form-control" name="data[facility_id]" id="facility_id" data-validation="required">
	        	<option value="">Please select facility</option>
	        	@foreach($facilities as $id => $facility)
	        		<option value="{{$facility->id}}" <?php echo ($inwardData->facility_id == $facility->id)?'selected':'';?>>{{$facility->name}}</option>
	        	@endforeach
	        </select>
	    </div>
    </div>
    <div class="form-group row">
        <label class="required col-md-4" for="name">Name</label>
        <div class="col-md-8">
        	<input class="form-control" type="text" name="data[name]" id="name" value="{{$inwardData->name}}" data-validation="required">
    	</div>
    </div>
    <div class="form-group row">
        <label class="col-md-4" for="preference">Patient Reference</label>
        <div class="col-md-8">
        	<input class="form-control" type="text" name="data[patient_id]" id="patient_id" value="{{$inwardData->patient_id}}">
    	</div>
    </div>
	<div class="form-group  row">
        <label class="col-md-4" for="contact_no">Contact No</label>
        <div class="col-md-8">
        	<input class="form-control" type="text" name="data[contact_no]" id="contact_no" value="{{$inwardData->contact_no}}">
    	</div>
    </div>
    <div class="form-group row">
        <label class="required col-md-4" for="contact_no">Specimen Type</label>
        <div class="col-md-8">
	        <select class="form-control" name="data[sample_type_id]">
	        	@foreach($sampleTypes as $id => $type)
	        		<option value="{{$type->id}}" <?php echo ($inwardData->sample_type_id == $type->id)?'selected':(($type->default_selected == 1)?'selected':'');?>>{{$type->name}}</option>
	        	@endforeach
	        </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-4" for="age">Age</label>
        <div class="col-md-8">
        	<input class="form-control" type="text" name="data[age]" id="age" value="{{$inwardData->age}}">
    	</div>
    </div>
    <div class="form-group row">
        <label class="col-md-4" for="age">Gender</label>
        <div class="col-md-8">
	        <select class="form-control" name="data[sex]">
				<option value="">Select</option>
				<option value="M" <?php echo ($inwardData->sex == 'M')?'selected':'';?>>Male</option>
				<option value="F" <?php echo ($inwardData->sex == 'F')?'selected':'';?>>Female</option>
				<option value="O" <?php echo ($inwardData->sex == 'O')?'selected':'';?>>Other</option>
			</select>
		</div>
    </div>
    <div class="form-group row">
        <label class="col-md-4" for="age">Rejected Reason</label>
        <div class="col-md-8">
	        <select class="form-control" name="data[rejected_reason_id]">
		    	<option value="0">Select Reason</option>
		    	@foreach($rejectedReasons as $reason)
		    		<option value="{{$reason->id}}" <?php echo ($inwardData->rejected_reason_id == $reason->id)?'selected':'';?>>{{$reason->reason}}</option>
				@endforeach
		    </select>
	    </div>
    </div>
    <div class="form-group row">
        <label class="col-md-4" for="age">Address</label>
        <div class="col-md-8">
        	<input class="form-control" type="text" name="data[address]" id="address" value="{{$inwardData->address}}">
        </div>
    </div>
</form>