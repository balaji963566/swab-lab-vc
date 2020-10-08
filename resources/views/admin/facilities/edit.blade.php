@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Edit Facility
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.facilities.update", [$facility->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">Name</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $facility->name) }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group {{ $errors->has('short_name') ? 'has-error' : '' }}">
                            <label class="required" for="short_name">Short Name</label>
                            <input class="form-control" type="text" name="short_name" id="short_name" value="{{ old('short_name', $facility->short_name) }}" required>
                            @if($errors->has('short_name'))
                                <span class="help-block" role="alert">{{ $errors->first('short_name') }}</span>
                            @endif
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                            <label class="required" for="location">Location</label>
                            <input class="form-control" type="text" name="location" id="location" value="{{ old('location', $facility->location) }}" required="">
                            @if($errors->has('location'))
                                <span class="help-block" role="alert">{{ $errors->first('location') }}</span>
                            @endif
                            <span class="help-block"></span>
                        </div>
                        <div class="panel panel-default">
			                <div class="panel-heading" style="font-weight: bold;">
			                    Facility Emails
			                </div>
			                <div class="panel-body" id="add_email_section">
			                	<?php $cnt = 1; ?>
			                	@foreach($facility->facility_emails as $id => $email)
				                	<div class="form-group email_fields">
			                            <label class="required" for="email_{{$id}}">Email</label>
			                            <input class="form-control" type="email" name="email[]" id="email_{{$id}}" value="{{$email->email}}" required>
		                        	
		                        	<?php 
		                        		if($cnt>1){
                        			?>
                        				<a class="btn btn-danger remove_email_field" href="javascript:void(0)">Remove</a>	
                        			<?php
		                        		}
		                        		$cnt++; 
	                        		?>
	                        		</div>
	                        	@endforeach
	                        	<div style="margin-bottom: 10px;float:right;" class="row">
						            <div class="col-lg-12">
						                <a class="btn btn-success" href="javascript:void(0)" id="add_email_link">
						                    Add Email
						                </a>
						            </div>
						        </div>
			                </div>
		                </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		$('#add_email_link').on('click', function(){
			var emailLength = $('#add_email_section').find('.email_fields').length + 1;
			var newEmailFieldHtml = '<div class="form-group email_fields"><label class="required" for="email_'+emailLength+'">Email</label><input class="form-control" type="email" name="email[]" id="email_'+emailLength+'" value="" required><a class="btn btn-danger remove_email_field" href="javascript:void(0)">Remove</a></div>';
			$('#add_email_section').find('.email_fields').last().after(newEmailFieldHtml);
		});

		$('body').on('click', '.remove_email_field', function(){
			var emailLength = $('#add_email_section').find('.email_fields').length;

			if(emailLength == 1){
				$('#add_email_section').find('.email_fields a.remove_email_field').remove();
			}
			else{
				$(this).closest('.email_fields').remove();
			}
			
		});
	});
</script>
@endsection