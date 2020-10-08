@extends('layouts.admin')
@section('content')
<style type="text/css">
	#generate_report_form .form-group.has-error .help-block{
		display:none;
	}
	#generate_report_form .form-control.error{
		border: 2px solid #dd4b39 !important;
	}
</style>
<div class="content">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<form method="POST" action="" enctype="multipart/form-data" id="fetch_generate_records">
				@csrf
	                <div class="panel-heading">
	                    Generate Report
	                </div>
	                <div class="panel-body">
	                	<div class="row">
	                		<div class="col-md-2">
		                        <div class="form-group">
		                            <label for="facility_id">Facility</label>
		                            <select class="form-control" name="facility_id" id="facility_id" style="width: 90%;" data-validation="required">
		                            	<option value="">Please select facility</option>
		                            	@foreach($data['facilities'] as $id => $facility)
		                            		<option value="{{$facility->id}}" <?php echo (isset($data['facility_id']) && $data['facility_id'] == $facility->id)?'selected':'';?>>{{$facility->name}}</option>
		                            	@endforeach
		                            </select>
		                        </div>
	                        </div>
	                        <div class="col-md-8">
		                        <div class="form-group">
		                            <label for="name">Specimen Types</label>
		                            <div class="checkbox">		                            	
			                            @foreach($data['sampleTypes'] as $id => $type)
			                            	<label style="margin-right: 20px;">
		                            			<input type="checkbox" name="specimen_types[]"value="{{$type->id}}" <?php echo ((isset($data['specimen_types']) && in_array($type->id, $data['specimen_types'])))?'checked':(($type->default_selected == 1)?'checked':''); ?>> {{$type->name}}
		                            		</label>
	                            		@endforeach	                            	
                            		</div>
		                        </div>
	                        </div>
	                        <div class="col-md-2">
	                        	<div class="form-group" style="padding-top: 22px !important">
		                            <button class="btn btn-success" style="padding: 5px 50px;font-size: 16px;" type="submit">Submit</button>
		                        </div>
                        	</div>
                        </div>                 
	                </div>
                </form>
            </div>
		</div>
	</div>

	@if (isset($data['inwardData']) && !empty($data['inwardData']))
    <div class="row">
        <div class="col-lg-12">
        	<form method="POST" action="{{ route("admin.reports.generate") }}" 
        	 enctype="multipart/form-data" id="generate_report_form">
			@csrf
	        	<div class="panel panel-default">
	                <div class="panel-heading">
	                    Samples to be reported
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
	                        <table class=" table table-bordered table-striped table-hover datatable datatable-GenerateReport" id="generate_table">
	                            <thead>
	                                <tr>
	                                    <th></th>
										<th>Sample ID</th>
										<th>Facility Name</th>
										<th>Specimen Type</th>
										<th>Name</th>
										<th style="width:5%">Age</th>
										<th style="width:8%">Gender</th>
										<th style="width:20%">Status</th>
										<th style="width:20%">Remarks</th>
										<th style="text-align: center;width:5%">Action</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                            	<?php 
	                            		$genderArr = array('M'=>'Male','F'=>'Female','O'=>'Other');
                            		?>
	                                @foreach($data['inwardData'] as $key => $inward)
	                                    <tr data-entry-id="{{ $inward->id }}">
	                                        <td></td>
	                                        <td style="width:8%">{{ $inward->sample_prefix.$inward->sample_id ?? '' }}</td>
	                                        <td>{{ $inward->facility->name ?? '' }}</td>
	                                        <td>{{ $inward->sample_type->name ?? '' }}</td>
	                                        <td>
	                                        	<span class="last_value pname">{{ $inward->name ?? '' }}</span>
	                                        	<span class="last_value" style="font-size: 10px;display: block;">{{ $inward->patient_id ?? '' }}</span>
	                                        	<div class="form-group input_value"> 
													<input type="text" name="name" class="form-control pname" value="{{$inward->name}}" data-validation="required" style="width:100%">
												</div>
	                                        </td>
	                                        <td style="width:5%">
	                                        	<span class="last_value page">{{ $inward->age ?? '' }}</span>
	                                        	<div class="form-group input_value"> 
													<input type="text" name="age" class="form-control page" value="{{$inward->age}}" style="width:100%" data-validation="number" maxlength="3" data-validation-optional="true">
												</div>
	                                        </td>
	                                        <td style="width:8%">
	                                        	<span class="last_value psex">
													<?php 
													echo isset($inward->sex)?$genderArr[$inward->sex]:'';
													?>
												</span>
												<div class="form-group input_value">
													<select class="form-control psex" name="sex" style="width:100%">
														<option value="">Select</option>
														<option value="M" <?php echo ($inward->sex == 'M')?'selected':'';?>>Male</option>
														<option value="F" <?php echo ($inward->sex == 'F')?'selected':'';?>>Female</option>
														<option value="O" <?php echo ($inward->sex == 'O')?'selected':'';?>>Other</option>
													</select>
												</div>
	                                        </td>
	                                        <td style="width:20%" class="status_td">
	                                        	<span>{{$inward->status}}</span>
	                                        	@can('reports_status_change')
	                                        		<a href="javascript:void(0)" class="change_status pull-right" style="font-size: 12px;">change status</a>
                                        		@endcan
	                                        </td>
	                                        <td style="width:20%">
	                                        	<span class="last_value premarks">{{ $inward->remarks ?? '' }}</span>
	                                        	<div class="form-group input_value"> 
													<input type="text" name="remarks" class="form-control premarks" value="{{$inward->remarks}}" style="width:100%">
												</div>
	                                        </td>
	                                        <td style="text-align: center;width:5%">
												<a href="" alt="Edit" class="edit_details">
													<span class="glyphicon glyphicon-pencil" style="font-size: 143%;color: #3c8dbc;cursor: pointer;"></span>
												</a>
												<a href="" alt="Close" class="close_details" style="display: none;">
													<span class="glyphicon glyphicon-remove-circle" style="font-size: 143%;color: #dd4b39;cursor: pointer;"></span>
												</a>
											</td>
	                                    </tr>
	                                @endforeach
	                            </tbody>
	                        </table>
	                    </div>
	                </div>
	            </div>
	            @if(count($data['inwardData']))
	            <!-- /.box -->
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">Review Email IDs</h3>
						</div>
						<div class="box-body">
							<dl class="dl-horizontal">
								<dt>Facility Name</dt>
								<dd>{{$data['facility']->name}}</dd>
								@foreach($data['facility']->facility_emails as $email)
									<dt>Email</dt>
									<dd>{{$email->email}}</dd>
								@endforeach
							</dl>
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							<div class="form-group">
								@can('report_generate_and_mail')
									<button class="btn btn-success pull-right" id="submit_report_samples" >Generate Report & Email</button>
								@endcan
								@can('reports_preview')
									<button class="btn btn-success pull-right preview" style="margin-right: 10px;" id="submit_for_preview">Preview Report</button>
								@endcan							
								<button class="btn btn-success pull-right update" id="update_report_data" style="display:none;">Update</button>
							</div>
						</div>
		        		<!-- /.box-footer-->
		      		</div>
	      		@endif
        	</form>			
        </div>
    </div>
    @endif
</div>
<div id="sampleStatusSelect" style="display: none;">
	<div>
		<select class="form-control pstatus">
        	@foreach($data['sampleStatus'] as $id => $status)
				<?php if($status->inactive_in_generate_report !=1 ) { ?>
        		<option value="{{$status->status}}">{{$status->status}}</option>
				<?php } ?>
        	@endforeach
        </select>
    </div>
</div>
<div class="modal fade overlay-wrapper" id="modal-default">
	<div class="modal-dialog">
    	<div class="modal-content" style="width: 400px;margin: 0 auto;">
    		<form method="POST" action="{{ route("admin.reports.reverify") }}" 
        	 enctype="multipart/form-data" id="pick_samples_form">
			@csrf
	      		<div class="modal-header" style="text-align: center;">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	          		</button>
	        		<h4 class="modal-title">Signatory Password</h4>
	      		</div>
	      		<div class="modal-body" style="padding-left: 65px;">
			        <p>
			        	<div class="form-group">
			        		<label>Please enter signatory password</label>
			        		<input type="password" name="password" class="form-control" required="" style="width: 250px;">
			        		<p class="help-block"></p>
			        	</div>
			        </p>
	      		</div>
	      		<div class="modal-footer">
			        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			        <button type="button" class="btn btn-primary" id="recheck_password">Submit</button>
	      		</div>
	      	</form>
    	</div>
    <!-- /.modal-content -->
  	</div>
  	<div class="overlay" style="display: none;">
      <i class="fa fa-refresh fa-spin"></i>
    </div>
  	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade overlay-wrapper" id="modal-report-preview">
	<div class="modal-dialog" style="width: 800px;margin: 0 auto;">
    	<div class="modal-content" style="margin-top:20px;">    		
      		<div class="modal-header" style="text-align: center;">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
          		</button>
        		<h4 class="modal-title">Report Preview</h4>
      		</div>
      		<div class="modal-body">
      		</div>
      		<div class="modal-footer">
		        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" id="send_mail_on_preview">Send Mail</button>
      		</div>
    	</div>
    <!-- /.modal-content -->
  	</div>
  	<div class="overlay" style="display: none;">
      <i class="fa fa-refresh fa-spin"></i>
    </div>
  	<!-- /.modal-dialog -->
</div>
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		$.validate();		

		$('body').on('click','.edit_details', function(e){
			e.preventDefault();

			$(this).hide();
			$(this).next().show();
			$(this).closest('tr').find('.last_value').hide();
			$(this).closest('tr').find('.input_value').show();
		});

		$('body').on('click','.close_details', function(e){
			e.preventDefault();
			trObj = $(this).closest('tr');

			$(this).hide();
			$(this).prev().show();

			name = trObj.find('.last_value.pname').text();
			age = trObj.find('.last_value.page').text();
			sex = $.trim(trObj.find('.last_value.psex').text());	

			trObj.find('input.pname').val(name);
			trObj.find('input.page').val(age);
			trObj.find('select.psex option[value="'+sex+'"]').prop('selected', true);

			trObj.find('.last_value').show();
			trObj.find('.input_value').hide();
		});

		let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

		$.extend(true, $.fn.dataTable.defaults, {
		    pageLength: 100,
	  	});

	  	table = $('.datatable-GenerateReport:not(.ajaxTable)').DataTable({ buttons: dtButtons });
	    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
	        $($.fn.dataTable.tables(true)).DataTable()
	            .columns.adjust();
	    });

	    $('#modal-default').on('hidden.bs.modal', function (e) {
	  		//clean Modal
	  		$('#recheck_password').removeClass('status_change');
	  		$('#recheck_password').removeAttr('curid');
	  		$('#modal-default').find('input[name=password]').val('');
		})

	    $('.change_status').on('click',function(e){
	    	e.preventDefault();
	    	var url = $(this).attr('href');
	    	$('#modal-default').modal('show');
	    	$('#recheck_password').addClass('status_change');
	    	var curId = $(this).closest('tr').data('entry-id');
	    	$('#recheck_password').attr('curid',curId);
	    });

	    $('#submit_report_samples,#submit_for_preview,#update_report_data').on('click', function(e){
	    	e.preventDefault();

	    	var preview = false;
	    	var update = false;

	    	if($('#generate_table').find('tr.selected').length == 0){
	    		if($(this).hasClass('preview'))
	    			alert('Please select patients to preview report!');
    			else if($(this).hasClass('update'))
    				alert('Please select patients to update details!');
				else
	    			alert('Please select patients to generate report!');
	    		
	    		return false;
	    	}

	    	if($(this).hasClass('preview')){
	    		preview = true;
	    	}

	    	if($(this).hasClass('update')){
	    		update = true;
	    	}
	    	
	    	emptyPositiveFields = false;
	    	$.map(table.rows({ selected: true }).nodes(), function (entry) {
	          	name = $(entry).find('input[name=name]').val();
          		age = $(entry).find('input[name=age]').val();
          		sex = $(entry).find('select[name=sex]').val();
          		status = $(entry).find('td.status_td').find('span').text();

          		if(status=='Positive' && (!name || !age || !sex)){
          			emptyPositiveFields = true;      				
          			$(entry).find('.edit_details').trigger('click');
          			$("#update_report_data").show();
  					$('#submit_report_samples,#submit_for_preview').hide();
          		}
	      	});

	      	if(emptyPositiveFields){
	      		alert('Details for positive patients are mandatory!');
	      		return false;
	      	}

	    	if(!update){
	    		$('#modal-default').modal('show');

	    		if(preview)
	    			$('#recheck_password').addClass('preview');
	    	}
	    	else{
	    		generate_report(preview,update);
	    	}
	    });

	    $("#pick_samples_form").on('submit',function(e){
	    	e.preventDefault();
	    });

	    // Handle form submission event
	   	$('#recheck_password').on('click', function(e){
		   	e.preventDefault();
		   	thisObj = $(this);
		   	var preview = false;
		   	if(thisObj.hasClass('preview')){
		   		preview = true;
		   	}

		   	curId = '';
		   	if(thisObj.hasClass('status_change')){
		   		curId = thisObj.attr('curid');
		   	}

		   	$('.overlay').show();
		   	var form = thisObj.closest('form');
      		
      		var url = form.attr('action');
      		var _token = form.find('input[name=_token]').val();
      		var password = form.find('input[name=password]').val();
      		
      		$.ajax({
	          	headers: {'x-csrf-token': _token},
	          	method: 'POST',
	          	url: url,
	          	data: { password: password, curId : curId },
	          	success: function (response) {
	          		if(response.status == 'success'){
	          			if(response.for == 'status'){
	          				$('.overlay').hide();
	          				$('#modal-default').modal('hide');
	          				var sampleStatusHtml = $($("#sampleStatusSelect").html());
	          				sampleStatusHtml.find('select.pstatus option[value="'+response.sample_status+'"]').attr('selected', 'selected');
	          				curTr = $('#generate_report_form').find("tr[data-entry-id="+curId+"]");
	          				
	          				curTr.find('td.status_td').prepend(sampleStatusHtml.html());
	          				curTr.find('td.status_td span').hide();
	          				curTr.find('td.status_td a').hide();
	          			}
	          			else{
		          			form.find('.form-group').removeClass('has-error');
		          			form.find('.help-block').text('');
		          			thisObj.removeClass('preview')
		          			generate_report(preview,false);
		          		}
	          		}
	          		else{
	          			$('.overlay').hide();
	          			form.find('.form-group').addClass('has-error');
	          			form.find('.help-block').text(response.message);
	          		}
	          		return false;
	          	}
          	})
	   	});

	   	$('body').on('change','.pstatus',function(){
	   		thisObj = $(this);
	   		curTr = thisObj.closest('tr');
	   		curId = curTr.data('entry-id');
	   		curStatus = thisObj.val();
	   		$.ajax({
	          	headers: {'x-csrf-token': _token},
	          	method: 'POST',
	          	url: '{{route('admin.reports.changeStatus')}}',
	          	data: { curId: curId, curStatus: curStatus },
	          	success: function (response) { 
	          		if(response.status == 'success'){
	          			if(curStatus == 'Positive'){
	          				thisObj.closest('tr').find('td:last').html('<a href="" alt="Edit" class="edit_details"><span class="glyphicon glyphicon-pencil" style="font-size: 143%;color: #3c8dbc;cursor: pointer;"></span></a><a href="" alt="Close" class="close_details" style="display: none;"><span class="glyphicon glyphicon-remove-circle" style="font-size: 143%;color: #dd4b39;cursor: pointer;"></span></a>');
	          			}
	          			else{
	          				thisObj.closest('tr').find('td:last').text('NA');
	          			}
	          			thisObj.closest('td').find('span').text(curStatus).show();
	          			thisObj.closest('td').find('a').show();
	          			thisObj.remove();

	          			curTr.css('background-color','#B0BED9');
						setTimeout(function() {curTr.css('background-color','');}, 3000);
	          			//toggleButtons();
	          		}
	          		else{
	          			alert('Some error has occured!');
	          		}
	          		return false;
	          	}
          	})
	   	});

	   	$('body').on('click','#send_mail_on_preview',function(){
	   		//$('#submit_report_samples').trigger('click');
	   		generate_report(false,false);
	   	});

	   	function toggleButtons(){
	   		var emptyPositiveFields = false;

	   		$.map(table.rows().nodes(), function (entry) {
	          	name = $(entry).find('input[name=name]').val();
          		age = $(entry).find('input[name=age]').val();
          		sex = $(entry).find('select[name=sex]').val();
          		status = $(entry).find('td.status_td').find('span').text();

          		if(status=='Positive' && (!name || !age || !sex)){
          			emptyPositiveFields = true;
          		}
	      	});

      		if(emptyPositiveFields){
  				$("#update_report_data").show();
  				$('#submit_report_samples,#submit_for_preview').hide();
      		}
      		else{
  				$("#update_report_data").hide();
  				$('#submit_report_samples,#submit_for_preview').show();
      		}
	   	}

	   	function generate_report(preview,update){
	   		var form = $('#generate_report_form');
	   		form.find('input[name=preview]').remove();
	   		form.find('input[name=update]').remove();
	   		form.find('.new_element').remove();

	      	var data = $.map(table.rows({ selected: true }).nodes(), function (entry) {
	          	return {
	          		id:$(entry).data('entry-id'),
	          		name:$(entry).find('input[name=name]').val(),
	          		age:$(entry).find('input[name=age]').val(),
	          		sex:$(entry).find('select[name=sex]').val(),
	          		remarks:$(entry).find('input[name=remarks]').val()
	          	};
	      	});
	      	
	      	$('#generate_report_form').append(
               $('<input>')
                  .attr('type', 'hidden')
                  .attr('name', 'facility_id')
                  .attr('class', 'new_element')
                  .val($('#fetch_generate_records').find('select[name=facility_id]').val())
            );

            if(preview){
            	$('#generate_report_form').append(
	               $('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'preview')
	                  .val('yes')
	            );
            }

            if(update){
            	$('#generate_report_form').append(
	               $('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'update')
	                  .val('yes')
	            );
            }

	      	var opts = $('#fetch_generate_records').find('input[type=checkbox]:checked').map(function() {
			    return $(this).val();
			}).get();
			
            $('#generate_report_form').append(
               $('<input>')
                  .attr('type', 'hidden')
                  .attr('name', 'sample_types')
                  .attr('class', 'new_element')
                  .val(opts.join())
            );

	      	// Iterate over all ids
	      	$.each(data, function(index, value){
	            $('#generate_report_form').append(
	               $('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][id]')
	                  .attr('class', 'new_element')
	                  .val(value['id'])
	            );

	            $('#generate_report_form').append(
	            	$('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][name]')
	                  .attr('class', 'new_element')
	                  .val(value['name'])
              	);

              	$('#generate_report_form').append(
	            	$('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][age]')
	                  .attr('class', 'new_element')
	                  .val(value['age'])
              	);

              	$('#generate_report_form').append(
	            	$('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][sex]')
	                  .attr('class', 'new_element')
	                  .val(value['sex'])
              	);

              	$('#generate_report_form').append(
	            	$('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][remarks]')
	                  .attr('class', 'new_element')
	                  .val(value['remarks'])
              	);
	      	});

	      	if(preview){
	      		url = form.attr('action');
	      		$.ajax({
		            type: 'POST',
		            url: url,
		            data: form.serialize(),
		            success: function (response) {
	              		if(response.status == 'success'){
	              			$('.overlay').hide();
	              			$("#modal-default").modal('hide');
	              			html = '<embed src="'+response.file+'" type="application/pdf" style="width: 100%; height: 400px; margin:0; padding:0;">';
	              			$('#modal-report-preview').find('.modal-body').html(html);
	              			$('#modal-report-preview').modal('show');
	              		}
		            }
	          	});

	          	return false;
	      	}
	      	else{
      			form.submit();
      		}
	   	}
	});
</script>
@endsection