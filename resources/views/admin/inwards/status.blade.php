@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
        	<form method="POST" action="{{ route("admin.inwards.status_update") }}" 
        	 enctype="multipart/form-data" id="status_samples_form">
			@csrf
	        	<div class="panel panel-default">
	                <div class="panel-heading">
	                    Update Sample Status
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
	                        <table class=" table table-bordered table-striped table-hover datatable datatable-sampleStatus" id="sample_status_table">
	                            <thead>
	                                <tr>
	                                    <th width="10"></th>
	                                    <th>Sample ID</th>
	                                    <th>Facility</th>
	                                    <th>Specimen Type</th>
	                                    <th>Name</th>
	                                    <th>Age</th>
	                                    <th>Gender</th>
	                                    <th>Status</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                                @foreach($inwardData as $key => $inward)
	                                    <tr data-entry-id="{{ $inward->id }}">
	                                        <td></td>
	                                        <td>{{ $inward->sample_id ?? '' }}</td>
	                                        <td>{{ $inward->facility->name ?? '' }}</td>
	                                        <td>{{ $inward->sample_type->name ?? '' }}</td>
	                                        <td>
	                                        	{{ $inward->name ?? '' }}
	                                        	<br> <span style="font-size: 10px;">{{ $inward->patient_id ?? '' }}</span>
	                                        </td>
	                                        <td>{{ $inward->age ?? '' }}</td>
	                                        <td>{{ $inward->sex ?? '' }}</td>
	                                        <td style="width: 18%">
	                                        	<select class="form-control sample_status">
					                            	<option value="">Select</option>
					                            	@foreach($sampleStatus as $id => $status)
					                            		<option value="{{$status->status}}" <?php echo ($inward->status == $status->status)?'selected':'';?>>{{$status->status}}</option>
					                            	@endforeach
					                            </select>
	                                        </td>
	                                    </tr>
	                                @endforeach
	                            </tbody>
	                        </table>
	                    </div>
	                </div>
	                <div class="panel-footer clearfix">
						<div class="form-group">
	                        <button class="btn btn-success pull-right" id="submit_status_samples">Update</button>
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

	  	var table = $('.datatable-sampleStatus:not(.ajaxTable)').DataTable({ buttons: dtButtons });
	    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
	        $($.fn.dataTable.tables(true)).DataTable()
	            .columns.adjust();
	    });

	    // Handle form submission event
	   	$('#submit_status_samples').on('click', function(e){
		   	e.preventDefault();
      		var form = $('#status_samples_form');
			
			if($('#sample_status_table').find('tr.selected').length == 0){
				alert('Please select patients to update status!');	    		
	    		return false;
	    	}

	      	var data = $.map(table.rows({ selected: true }).nodes(), function (entry) {
	          	return {id:$(entry).data('entry-id'),status:$(entry).find('.sample_status').val()};
	      	});

	      	emptyStatus = false;
	      	// Iterate over all ids
	      	$.each(data, function(index, value){
	      		if(!value['status']){
	      			alert('Please select status of selected patients!');
	      			emptyStatus = true;
	      			return false;
	      		}
	            $('#status_samples_form').append(
	               $('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][id]')
	                  .val(value['id'])
	            );

	            $('#status_samples_form').append(
	            	$('<input>')
	                  .attr('type', 'hidden')
	                  .attr('name', 'data['+index+'][status]')
	                  .val(value['status'])
              	);
	      	});

	      	if(!emptyStatus){
		      	if (confirm('Are you sure you want to update the result for '+data.length+' patients?')) {
		      		form.submit();
		      	}
	      	}
	   });
	});
</script>
@endsection