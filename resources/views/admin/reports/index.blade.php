@extends('layouts.admin')
@section('content')
<div class="content">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
	                <div class="panel-heading">
	                    All Reports
	                </div>
	                <form method="POST" action="" enctype="multipart/form-data">
					@csrf
		                <div class="panel-body">
		                	<div class="row">
		                		<div class="col-md-3">
			                        <div class="form-group">
			                            <label class="required" for="name">Facility</label>
			                            <?php $selectedFacility = 'All'; ?>
			                            <select class="form-control" name="facility_id">
			                            	<option>All</option>
			                            	@foreach($facilities as $id => $facility)
			                            		<?php
			                            			if(($facility_id == $facility->id))
			                            				$selectedFacility = $facility->name;
			                            		?>
			                            		<option value="{{$facility->id}}" <?php echo ($facility_id == $facility->id)?'selected':'';?>>{{$facility->name}}</option>
			                            	@endforeach
			                            </select>
			                        </div>
		                        </div>
		                        <div class="col-md-3">
			                        <div class="form-group">
			                            <label class="required" for="short_name">Samples Reporting Date</label>
			                            <input type="text" class="form-control pull-right" id="datepicker" name="reported_at">
			                            <input type="hidden" name="reported_at_last_value" value="{{$reported_at}}">
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
    <div class="row">
        <div class="col-lg-12">
        	<div class="box">
				<div class="box-header">
					<h3 class="box-title">Report - {{$selectedFacility}} <?php echo ($reported_at)?'('.date('d-m-Y',strtotime($reported_at)).')':''?></h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body table-responsive no-padding">
					<table class="table table-hover">
						<tbody>
							<tr>
								<th>Sr. No</th>
								<th>Facility Name</th>
								<th>Reporting Date</th>
								<th style="text-align: center;">Total Samples</th>
								<th style="text-align: center;display: none;">Received Samples</th>
								<th style="text-align: center;display: none;">Rejected Samples</th>
								<th style="text-align: center;display: none;">Tested Samples</th>
								<th style="text-align: center;">Download</th>
							</tr>
							<?php $cnt = 1; ?>
							@foreach($reports as $report)
							<tr>
								<td>{{$cnt}}</td>
								<td>{{$report->facility_name}}</td>
								<td>{{date('d/m/Y H:i a', strtotime($report->reported_at))}}</td>
								<td style="text-align: center;">{{$report->total_count}}</td>
								<td style="text-align: center;display: none;">50</td>
								<td style="text-align: center;display: none;">4</td>
								<td style="text-align: center;display: none;">46</td>
								<td style="text-align: center;">
									@can('report_all_download_pdf')
										<a href="{{route('admin.reports.pdf',['id'=>$report->report_id])}}" alt="Download">
											<img src="{{ asset('images/doc_pdf.png') }}">
										</a>
									@endcan
									@can('report_all_download_excel')
										<a href="{{route('admin.reports.excel',['id'=>$report->report_id])}}" alt="Download" style="margin-left: 5px;">
											<img style="width: 20px;height: 18px;" src="{{ asset('images/Excel-icon.jpg') }}">
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
</div>
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		//Date picker
		var reportedAt = $("input[name=reported_at_last_value]").val();
		
		if(reportedAt){
			$('#datepicker').datetimepicker({
		    	defaultDate: new Date(reportedAt),
		    	format: 'DD-MM-YYYY'
		    });
		}
		else{
			$('#datepicker').datetimepicker({
				defaultDate: new Date(),
		    	format: 'DD-MM-YYYY'
		    });
		}
	});
</script>
@endsection