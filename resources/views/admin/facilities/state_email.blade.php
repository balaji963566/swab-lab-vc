@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    State Government Emails
                </div>
                <div class="panel-body">
                	<div class="row">
			            <div class="col-lg-12">
			                <a class="btn btn-success pull-right" href="javascript:void(0)" id="edit_state_email">
			                    Edit
			                </a>
			            </div>
			        </div>
                    <form method="POST" action="{{ route("admin.facilities.update", [$facility->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group show_value">
                            <label>Name</label>
                            <span style="display: block;">{{ $facility->name }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }} edit_value" style="display: none;">
                            <label class="required" for="name">Name</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ $facility->name }}" required data-validation="required">
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block"></span>
                        </div>
                        <div class="panel panel-default">
			                <div class="panel-heading" style="font-weight: bold;">
			                    State Emails
			                </div>
			                <div class="panel-body" id="add_email_section">
			                	@foreach($facility->facility_emails as $id => $email)
				                	<div class="form-group email_fields show_value">
			                            <label>Email</label>
			                            <span style="display: block;">{{$email->email}}</span>
	                        		</div>
	                        	@endforeach
			                	<?php $cnt = 1; ?>
			                	@foreach($facility->facility_emails as $id => $email)
				                	<div class="form-group email_fields edit_value" style="display: none;">
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
	                        	<div style="margin-bottom: 10px;float:right;display: none;" class="row edit_value">
						            <div class="col-lg-12">
						                <a class="btn btn-success" href="javascript:void(0)" id="add_email_link">
						                    Add Email
						                </a>
						            </div>
						        </div>
			                </div>
		                </div>
                        <div class="form-group edit_value" style="display: none;">
                            <button class="btn btn-success pull-right" type="submit">
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
		$.validate();
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

		$('#edit_state_email').on('click', function(){
			$('.edit_value,.show_value').toggle();
		});
	});
</script>
@endsection