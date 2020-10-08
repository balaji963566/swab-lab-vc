@extends('layouts.admin')
@section('content')
<div class="content">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
	                <div class="panel-heading">
	                    All Samples
	                </div>
	                <div class="panel-body">
	                	<form method="POST" action="{{ route("admin.inwards") }}" enctype="multipart/form-data">
						@csrf
		                	<div class="row">
		                		<div class="col-md-3">
			                        <div class="form-group">
			                            <label class="required" for="name">Facility</label>
			                            <select class="form-control" name="facility_id">
			                            	<option value="">All</option>
			                            	@foreach($facilities as $id => $facility)
			                            		<option value="{{$facility->id}}" <?php echo (isset($formData['facility_id']) && $formData['facility_id'] == $facility->id)?'selected':'';?>>{{$facility->name}}</option>
			                            	@endforeach
			                            </select>
			                        </div>
		                        </div>
		                        <div class="col-md-2">
			                        <div class="form-group">
			                            <label class="required" for="short_name">From Date</label>
			                            <input type="text" class="form-control pull-right" id="from_datepicker" name="from_date">
			                            <input type="hidden" name="from_date_last_value" value="{{$formData['from_date']??''}}">
			                        </div>
		                        </div>
		                        <div class="col-md-2">
			                        <div class="form-group">
			                            <label class="required" for="short_name">To Date</label>
			                            <input type="text" class="form-control pull-right" id="to_datepicker" name="to_date">
			                            <input type="hidden" name="to_date_last_value" value="{{$formData['to_date']??''}}">
			                        </div>
		                        </div>
		                        <div class="col-md-3">
			                        <div class="form-group">
			                            <label class="required" for="name">Status</label>
			                            <select class="form-control" name="status">
			                            	<option value="All">All</option>
			                            	@foreach($sampleStatus as $id => $status)
			                            		<option value="{{$status->status}}" <?php echo (isset($formData['status']) && $formData['status'] == $status->status)?'selected':'';?>>{{$status->status}}</option>
			                            	@endforeach
			                            	<option value="sent_for_testing" <?php echo (isset($formData['status']) && $formData['status'] == 'sent_for_testing')?'selected':'';?>>Result Awaited</option>
			                            	<option value="pending" <?php echo (isset($formData['status']) && $formData['status'] == 'pending')?'selected':'';?>>Test Pending</option>
			                            	<option value="rejected" <?php echo (isset($formData['status']) && $formData['status'] == 'rejected')?'selected':'';?>>Rejected</option>
			                            </select>
			                        </div>
		                        </div>
		                        <div class="col-md-2">
		                        	<div class="form-group" style="padding-top: 22px !important">
			                            <button class="btn btn-success" style="padding: 5px 50px;font-size: 16px;" type="submit">Submit</button>
			                        </div>
	                        	</div>
	                        </div>
                        </form>                
	                </div>
	            </div>
		</div>
	</div>
    <div class="row">
        <div class="col-lg-12">
        	<div class="panel panel-default">
                <div class="panel-heading">
                    Sample Details
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-sampleStatus" id="all_samples_table">
							<thead>
	                            <tr>
									<th>Sample ID</th>
									<th>Facility</th>
									<th>Specimen Type</th>
									<th>Name</th>
									<th>Contact No</th>
									<th>Age</th>
									<th>Gender</th>
									<th>Address</th>
									<th>Received Date</th>
									<th>Testing Date</th>
									<th>SARS-CoV 2</th>
									<th>Action</th>
								</tr>
	                        </thead>
							<tbody>
		                    	<?php $cnt = 1; ?>
		                        @foreach($inwardData as $key => $inward)
		                            <tr class="{{$inward->sample_id}}">
		                                <td>{{ $inward->sample_id ?? '' }}</td>
		                                <td><span class="pfacility">{{ $inward->facility_name ?? '' }}</span></td>
		                                <td><span class="psample_type">{{ $inward->sample_type_name ?? '' }}</span></td>
		                                <td><span class="pname">{{ $inward->patient_name ?? '' }}</span></td>
		                                <td><span class="pcontact_no">{{ $inward->contact_no ?? '' }}</span></td>
		                                <td><span class="page">{{ $inward->age ?? '' }}</span></td>
		                                <td><span class="psex">{{ $inward->sex ?? '' }}</span></td>
		                                <td><span class="paddress">{{ $inward->address ?? '' }}</span></td>
		                                <td>{{date('d/m/Y H:i a', strtotime($inward->received_at))}}</td>
		                                <td>
		                                	{{($inward->tested_at)?date('d/m/Y H:i a', strtotime($inward->tested_at)):''}}
		                                </td>
		                                <td>{{ $inward->status ?? '' }}</td>
		                                <td style="text-align: center;">
	                                		<a href="" alt="Edit" class="edit_details" pid="{{$inward->id}}">
												<span class="glyphicon glyphicon-pencil" style="color: #3c8dbc;cursor: pointer;"></span>
											</a>
											@can('report_individual_download_pdf')
												@if($inward->tested_at != null)
													<a href="javascript:void(0);" alt="Download" class="individual_report" style="margin-left: 3px;" formid="report_{{$inward->id}}">
														<img src="{{ asset('images/doc_pdf.png') }}">
													</a>
													<form action="{{ route('admin.reports.individualPdf', $inward->id) }}" method="POST" style="display: inline-block;" id="report_{{$inward->id}}">
	                                                    <input type="hidden" name="_method" value="POST">
	                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
	                                                </form>
                                                @endif
											@endcan
		                                </td>
		                            </tr>
		                            <?php $cnt++; ?>
		                        @endforeach
		                    </tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- /.box -->
        </div>
    </div>
</div>
<div class="modal fade overlay-wrapper" id="modal_edit_details">
	<div class="modal-dialog">
    	<div class="modal-content" style="width: 500px;margin: 0 auto;">
      		<div class="modal-header" style="text-align: center;background-color: #4386bc;color: #fff;">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
          		</button>
        		<h4 class="modal-title">Edit Sample - <span id="edit_sample_id"></span></h4>
      		</div>
      		<div class="modal-body" style="padding:10px 15px 0 15px;">
      			
      		</div>
      		<div class="modal-footer">
		        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
		        <button type="button" class="btn btn-primary" id="save_edited_sample">Save</button>
      		</div>
    	</div>
    <!-- /.modal-content -->
  	</div>
  	<div class="overlay" style="display: none;">
      <i class="fa fa-refresh fa-spin"></i>
    </div>
  	<!-- /.modal-dialog -->
</div>
<div class="modal fade overlay-wrapper" id="modal-reverify">
	<div class="modal-dialog">
    	<div class="modal-content" style="width: 400px;margin: 0 auto;">
    		<form method="POST" action="{{ route("admin.reports.reverify") }}"
        	 enctype="multipart/form-data">
			@csrf
	      		<div class="modal-header" style="text-align: center;">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	          		</button>
	        		<h4 class="modal-title">Signatory Password</h4>
	      		</div>
	      		<div class="modal-body" style="padding-left: 30px;padding-right: 30px;">
			        <p>
			        	<div class="form-group">
			        		<label>Please enter signatory password</label>
			        		<input type="password" name="password" class="form-control" required="" >
			        		<p class="help-block"></p>
			        	</div>
			        	<div class="form-group">
			        		<label>Remarks</label>
			        		<textarea class="form-control" name="remarks" id="remarks" ></textarea>
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
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		var APP_URL = {!! json_encode(url('/')) !!}
		var fromDate = $("input[name=from_date_last_value]").val();
		
		if(fromDate){
			$('#from_datepicker').datetimepicker({
		    	defaultDate: new Date(fromDate),
		    	format: 'DD/MM/YYYY'
		    });
		}
		else{
			$('#from_datepicker').datetimepicker({
				defaultDate: new Date(),
		    	format: 'DD/MM/YYYY'
		    });
		}

		var toDate = $("input[name=to_date_last_value]").val();
		
		if(toDate){
			$('#to_datepicker').datetimepicker({
		    	defaultDate: new Date(toDate),
		    	format: 'DD/MM/YYYY'
		    });
		}
		else{
			$('#to_datepicker').datetimepicker({
				defaultDate: new Date(),
		    	format: 'DD/MM/YYYY'
		    });
		}

	    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

		$.extend(true, $.fn.dataTable.defaults, {
		    pageLength: 100,
	  	});

	  	var table = $('.datatable-sampleStatus:not(.ajaxTable)').DataTable({ buttons: dtButtons });

	  	$.map(table.rows().nodes(), function (entry) {
          	$(entry).find('td.select-checkbox').removeClass('select-checkbox');
      	});
	    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
	        $($.fn.dataTable.tables(true)).DataTable()
	            .columns.adjust();
	    });

	    $('.edit_details,#save_edited_sample').on('click', function(e){
	    	e.preventDefault();
	    	var pid = $(this).attr('pid');

	    	if(pid){
	    		data = '';

	    		if($('#modal_edit_details').find('#edit_samples_form').length){
	    			data = $('#edit_samples_form').serialize();
	    		}

	    		$('.overlay').show();

				$.ajax({
		          	headers: {'x-csrf-token': _token},
		          	method: 'POST',
		          	url: APP_URL+'/admin/inwards/edit-sample',
		          	data: { pid: pid, data:data },
		          	success: function (response) { 
		          		if(response.status == 'success' && response.for!='show'){
		          			inward = response.data;
		          			$('.overlay').hide();
		          			curTr = $('#all_samples_table').find('tr.'+inward.sample_id);
                            curTr.find('.pfacility').text(inward.facility.name);
                            curTr.find('.psample_type').text(inward.sample_type.name);
                            curTr.find('.pname').text(inward.name);
                            curTr.find('.pcontact_no').text(inward.contact_no);
                            curTr.find('.page').text(inward.age);
                            curTr.find('.psex').text(inward.sex);
                            curTr.find('.paddress').text(inward.address);

                            $('#modal_edit_details').find('.modal-body').html('');
                            $('#modal_edit_details').modal('hide');
		          		}
		          		else{
		          			$('.overlay').hide();
		          			$('#modal_edit_details').find('#save_edited_sample').attr('pid',pid);
		          			$('#modal_edit_details').find('#edit_sample_id').text(response.sample_id);
		          			$('#modal_edit_details').find('.modal-body').html(response.html);
		          			$('#modal_edit_details').modal('show');
		          			$.validate();
		          		}
		          		return false;
		          	}
	          	})
			}
	    });

	    $('#modal-reverify').on('hidden.bs.modal', function (e) {
	  		//clean Modal
	  		$('#modal-reverify').find('input[name=password]').val('');
	  		$('#modal-reverify').find('textarea[name=remarks]').val('');
	  		$('#modal-reverify').find('.form-group').removeClass('has-error');
  			$('#modal-reverify').find('.help-block').text('');
		});
		
		$('#modal_edit_details').on('hidden.bs.modal', function (e) {
	  		//clean Modal
	  		$('#modal_edit_details').find('.modal-body').html('');
		});

		$('.individual_report').on('click', function(e){
			e.preventDefault();
			formId = $(this).attr('formid');
			$('#recheck_password').attr('formid',formId);
			$('#modal-reverify').modal('show');
		});

		$('#recheck_password').on('click', function(e){
		   	e.preventDefault();
		   	thisObj = $(this);
		   	formId = thisObj.attr('formid');

		   	var remarks = $('#remarks').val();

		   	$('.overlay').show();
		   	var form = thisObj.closest('form');
      		
      		var url = form.attr('action');
      		var _token = form.find('input[name=_token]').val();
      		var password = form.find('input[name=password]').val();
      		
      		$.ajax({
	          	headers: {'x-csrf-token': _token},
	          	method: 'POST',
	          	url: url,
	          	data: { password: password },
	          	success: function (response) {
	          		$('.overlay').hide();
	          		$('#modal-reverify').modal('hide');
	          		if(response.status == 'success'){
	          			form.find('.form-group').removeClass('has-error');
	          			form.find('.help-block').text('');
	          			$('#'+formId).append(
			               $('<input>')
			                  .attr('type', 'hidden')
			                  .attr('name', 'remarks')
			                  .val(remarks)
			            );
	          			$('#'+formId).submit();
	          		}
	          		else{
	          			form.find('.form-group').addClass('has-error');
	          			form.find('.help-block').text(response.message);
	          		}
	          		return false;
	          	}
          	})
	   	});
	});
</script>
@endsection