@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
        	<form method="POST" action="{{ route("admin.inwards.pick_update") }}" 
        	 enctype="multipart/form-data" id="pick_samples_form">
			@csrf
	        	<div class="panel panel-default">
	                <div class="panel-heading">
	                    Samples to be tested
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
	                        <table class=" table table-bordered table-striped table-hover datatable datatable-PickSamples">
	                            <thead>
	                                <tr>
	                                    <th width="10"></th>
	                                    <th>Sample ID</th>
	                                    <th>Facility</th>
	                                    <th>Specimen Type</th>
	                                    <th>Received Date</th>
	                                    <th>Name</th>
	                                    <th>Patient Reference</th>
	                                    <th>Age</th>
	                                    <th>Gender</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                            	<?php $cnt = 1; ?>
	                                @foreach($inwardData as $key => $inward)
	                                    <tr data-entry-id="{{ $inward->id }}">
	                                        <td></td>
	                                        <td>{{ $inward->sample_id ?? '' }}</td>
	                                        <td>{{ $inward->facility->name ?? '' }}</td>
	                                        <td>{{ $inward->sample_type->name ?? '' }}</td>
	                                        <td>{{ date('d/m/Y H:i a', strtotime($inward->received_at)) ?? '' }}</td>
	                                        <td>{{ $inward->name ?? '' }}</td>
	                                        <td>{{ $inward->patient_id ?? '' }}</td>
	                                        <td>{{ $inward->age ?? '' }}</td>
	                                        <td>{{ $inward->sex ?? '' }}</td>
	                                    </tr>
	                                    <?php $cnt++; ?>
	                                @endforeach
	                            </tbody>
	                        </table>
	                    </div>
	                </div>
	                <div class="panel-footer clearfix">
						<div class="form-group">
	                        <button class="btn btn-success pull-right" id="submit_pick_samples">Send for testing</button>
	                    </div>
					</div>
	            </div>
        	</form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

		$.extend(true, $.fn.dataTable.defaults, {
		    pageLength: 100,
	  	});

	  	var table = $('.datatable-PickSamples:not(.ajaxTable)').DataTable({ buttons: dtButtons });
	    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
	        $($.fn.dataTable.tables(true)).DataTable()
	            .columns.adjust();
	    });

	    // Handle form submission event
	   	$('#submit_pick_samples').on('click', function(e){
		   	e.preventDefault();
      		var form = $('#pick_samples_form');

	      	var ids = $.map(table.rows({ selected: true }).nodes(), function (entry) {
	          	return $(entry).data('entry-id')
	      	});
		      
	      	// Iterate over all ids
	      	$.each(ids, function(index, value){
	            $('#pick_samples_form').append(
	               $('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'id[]')
	                  .val(value)
	            );
	      	});

	      	if (confirm('Do you really want to send '+ids.length+' samples for testing?')) {
	      		form.submit();
	      	}
	   });
	});
</script>
@endsection