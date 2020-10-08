@extends('layouts.admin')
@section('content')
<style type="text/css">
#state_report_table>thead>tr>th {
    text-align: center;
    font-size: 12px;
}
#state_report_table>tbody>tr>td {
    text-align: center;
    font-size: 13px;
}
</style>
<div class="content">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
	                <div class="panel-heading">
	                    Generate State Report
	                </div>
	                <div class="panel-body">
	                	<form method="POST" action="{{ route("admin.reports.stateReports") }}" enctype="multipart/form-data" id="state_report_form">
						@csrf
		                	<div class="row">
		                		<div class="col-md-3">
			                        <div class="form-group">
			                            <label class="required" for="name">Facility</label>
			                            <select class="form-control" name="facility_id" data-validation="required">
			                            	<option value="">Select Facility</option>
			                            	<option value="All" <?php echo (isset($formData['facility_id']) && $formData['facility_id'] == 'All')?'selected':'';?>>All</option>
			                            	@foreach($facilities as $id => $facility)
			                            		<option value="{{$facility->id}}" <?php echo (isset($formData['facility_id']) && $formData['facility_id'] == $facility->id)?'selected':'';?>>{{$facility->name}}</option>
			                            	@endforeach
			                            </select>
			                        </div>
		                        </div>
		                        <div class="col-md-2">
			                        <div class="form-group">
			                            <label class="required" for="short_name">From Date</label>
			                            <input type="text" class="form-control pull-right" id="from_datepicker" name="from_date" data-validation="required">
			                            <input type="hidden" name="from_date_last_value" value="{{$formData['from_date']??''}}">
			                        </div>
		                        </div>
		                        <div class="col-md-2">
			                        <div class="form-group">
			                            <label class="required" for="short_name">To Date</label>
			                            <input type="text" class="form-control pull-right" id="to_datepicker" name="to_date" data-validation="required">
			                            <input type="hidden" name="to_date_last_value" value="{{$formData['to_date']??''}}">
			                        </div>
		                        </div>
		                        <div class="col-md-3">
			                        <div class="form-group">
			                            <label class="required" for="name">Status</label>
			                            <select class="form-control" name="status" data-validation="required">
			                            	<option value="">Select Status</option>
			                            	<option value="All" <?php echo (isset($formData['status']) && $formData['status'] == 'All')?'selected':'';?>>All</option>
			                            	@foreach($sampleStatus as $id => $status)
			                            		<option value="{{$status->status}}" <?php echo (isset($formData['status']) && $formData['status'] == $status->status)?'selected':'';?>>{{$status->status}}</option>
			                            	@endforeach
			                            </select>
			                        </div>
		                        </div>
		                        <div class="col-md-2">
		                        	<div class="form-group" style="padding-top: 22px !important">
			                            <button class="btn btn-success" style="padding: 5px 50px;font-size: 16px;" id="get_state_report">Submit</button>
			                        </div>
	                        	</div>
	                        </div>
                        </form>                
	                </div>
	            </div>
		</div>
	</div>
	@if(!empty($inwardData))
	    <div class="row">
	        <div class="col-lg-12">
	        	<div class="panel panel-default">
	                <div class="panel-heading">
	                    <span>Sample Details</span>
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
	                        <table class=" table table-bordered table-striped table-hover datatable datatable-StateSamples" id="state_report_table">
								<thead>
		                            <tr>
		                            	<th width="2%"></th>
										<th style="text-align: center;">SR. NO</th>
										<th>SAMPLE ID</th>
										<th>PATIENT’S NAME</th>
										<th>AGE</th>
										<th>GENDER</th>
										<th>ADDRESS OF PATIENT</th>
										<th>PHONE NUMBER OF PATIENT</th>
										<th>NAME OF REFERRING FACILITY/ HOSPITAL</th>
										<th>SPECIMEN TYPE</th>
										<th>DATE OF SAMPLE TESTING</th>
										<th>SARS-CoV 2</th>
									</tr>
		                        </thead>
								<tbody>
			                    	<?php $cnt = 1; ?>
			                        @foreach($inwardData as $key => $inward)
			                            <tr data-entry-id="{{$inward->id}}">
			                            	<td width="2%"></td>
			                                <td style="text-align: center;">{{ $cnt ?? '' }}</td>
			                                <td>{{ $inward->sample_id ?? '' }}</td>
			                                <td>
			                                	{{ $inward->patient_name ?? '' }}
			                                	<?php echo ($inward->patient_id)?' / '.$inward->patient_id:''; ?>
			                                </td>
			                                <td>{{ $inward->age ?? '' }}</td>
			                                <td>{{ $inward->sex ?? '' }}</td>
			                                <td>{{ $inward->address ?? '' }}</td>
			                                <td>{{ $inward->contact_no ?? '' }}</td>
			                                <td>{{ $inward->facility_name ?? '' }}</td>
			                                <td>{{ $inward->sample_type_name ?? '' }}</td>
			                                <td>{{$inward->reported_at?date('d-m-Y', strtotime($inward->reported_at)):''}}</td>
			                                <td>{!! ($inward->status == 'Positive') ? '<b>'.$inward->status.'</b>' : $inward->status !!}</td>
			                            </tr>
			                            <?php $cnt++; ?>
			                        @endforeach
			                    </tbody>
							</table>
						</div>
					</div>
					<div class="panel-footer clearfix">
						<div class="form-group">
	                        <button class="btn btn-success pull-right" id="generate_state_report">Generate State Report</button>
	                    </div>
					</div>
				</div>
				<!-- /.box -->
	        </div>
	    </div>
    @else
    	<div class="row">
	        <div class="col-lg-12">
	        	<div class="box">
					<div class="box-header">
						<h3 class="box-title">All Generated Reports</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body table-responsive">
						<table class="table table-bordered table-striped table-hover datatable datatable-GenerateReport">
							<thead>
								<tr>
									<th style="width: 7%;text-align: center;">Sr. No</th>
									<th>Facility Name</th>
									<th>From Date</th>
									<th>To Date</th>
									<th>Status</th>
									<th style="text-align: center;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $cnt = 1; ?>
								@foreach($reports as $report)
								<tr>
									<td style="width: 7%;text-align: center;">{{$cnt}}</td>
									<td>{{$report->facility_name}}</td>
									<td>{{date('d/m/Y', strtotime($report->from_date))}}</td>
									<td>{{date('d/m/Y', strtotime($report->to_date))}}</td>
									<td>{{$report->status}}</td>
									<td style="text-align: center;">
										@can('state_report_download_pdf')
											<a href="{{route('admin.reports.downloadStateReportPdf',['id'=> $report->id])}}" alt="Download">
												<img src="{{ asset('images/doc_pdf.png') }}">
											</a>
										@endcan
										@can('state_report_send_email')
											<a href="{{route('admin.reports.stateMailReview',['id'=> $report->id])}}" alt="Send mail" data-report-id="{{$report->id}}" class="state_email_review" style="margin-left: 5px;">
												<span class="glyphicon glyphicon-envelope" style="color: #3c8dbc;cursor: pointer;"></span>
											</a>
										@endcan
									</td>
								</tr>
								<?php $cnt++; ?>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<!-- /.box -->
	        </div>
	    </div>
    @endif
</div>
<div class="modal fade overlay-wrapper" id="modal_mail_review">
	<div class="modal-dialog">
		<form method="POST" action="{{ route("admin.reports.sendReportEmail") }}" 
        	 enctype="multipart/form-data" id="send_report_mail_form">
			@csrf
	    	<div class="modal-content" style="width: 600px;margin: 0 auto;">    		
	      		<div class="modal-header" style="text-align: center;">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          			<span aria-hidden="true">&times;</span>
	          		</button>
	        		<h4 class="modal-title">State report mail review</h4>
	      		</div>
	      		<div class="modal-body">
	      		</div>
	      		<div class="modal-footer">
	      			<div class="form-group">
	                    <button class="btn btn-success pull-right" id="send_state_report_mail">Send Mail</button>
	                </div>
	      		</div>
	    	</div>
	    	<!-- /.modal-content -->
    	</form>
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

	  	var table = $('.datatable-StateSamples:not(.ajaxTable)').DataTable({ buttons: dtButtons });
	    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
	        $($.fn.dataTable.tables(true)).DataTable()
	            .columns.adjust();
	    });

	  	$('.datatable-GenerateReport:not(.ajaxTable)').DataTable({ dom: 'lfrtip', columnDefs:false, buttons: false });

	    $('#get_state_report,#generate_state_report').on('click',function(){
	    	if($(this).attr('id') == 'generate_state_report'){
	    		var form = $('#state_report_form');

	    		if($('#state_report_table').find('tr.selected').length == 0){
	    			alert('Please select patients to generate report!');
		    		return false;
		    	}

		      	var ids = $.map(table.rows({ selected: true }).nodes(), function (entry) {
		          	return $(entry).data('entry-id')
		      	});
			      
		      	// Iterate over all ids
		      	$.each(ids, function(index, value){
		            form.append(
		               $('<input>')
		                  .attr('type', 'hidden')
		                  .attr('name', 'id[]')
		                  .val(value)
		            );
		      	});

	    		form.append(
	               $('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'report_pdf')
	                  .val('yes')
	            );
	    	}

	    	form.submit();
	    });

	    $('.state_email_review').on('click', function(e){
	    	e.preventDefault();

	    	url = $(this).attr('href');
	    	report_id = $(this).data('report-id');

			$.ajax({
	          	headers: {'x-csrf-token': _token},
	          	method: 'POST',
	          	url: url,
	          	success: function (response) { 
	          		if(response.status == 'success'){
	          			$('.overlay').hide();
	          			modalBody = $('#modal_mail_review').find('.modal-body');

	          			$('#send_report_mail_form').append(
			               $('<input>')
			                  .attr('type', 'hidden')
			                  .attr('name', 'report_id')
			                  .val(report_id)
			            );

	          			modalBody.html(response.html);

	          			$('#modal_mail_review').modal('show');
	          			$.validate();
	          		}
	          		return false;
	          	}
          	});
	    });
	});
</script>
@endsection