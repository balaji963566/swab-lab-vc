@extends('layouts.admin')
@section('content')
<style type="text/css">
	#rowtemplate .form-group.has-error .help-block{
		display:none;
	}
	#rowtemplate .form-control.error{
		border: 2px solid #dd4b39 !important;
	}
</style>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
        	<form method="POST" action="{{ route("admin.inwards.store") }}" enctype="multipart/form-data" id="add_sample_form">
			@csrf
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    Create Samples
	                </div>
	                <div class="panel-body">
	                	<div class="row">
	                		<div class="col-md-3">
		                        <div class="form-group">
		                            <label class="required" for="name">Facility</label>
		                            <select class="form-control" name="facility_id" id="facility_id" data-validation="required">
		                            	<option value="">Please select facility</option>
		                            	@foreach($facilities as $id => $facility)
		                            		<option value="{{$facility->id}}">{{$facility->name}}</option>
		                            	@endforeach
		                            </select>
		                        </div>
	                        </div>
	                        <div class="col-md-2">
		                        <div class="form-group">
		                            <label class="required" for="short_name">Sample collection date</label>
		                            <input type="text" class="form-control pull-right" id="collect_datepicker" name="collected_at" data-validation="required">
		                        </div>
	                        </div>
	                        <div class="col-md-3">
		                        <div class="form-group">
		                            <label class="required" for="short_name">Sample receiving date & time</label>
		                            <input type="text" class="form-control pull-right" id="receive_datepicker" name="received_at" data-validation="required">
		                        </div>
	                        </div>
	                        <div class="col-md-2">
		                        <div class="form-group {{ $errors->has('no_of_samples') ? 'has-error' : '' }}">
		                            <label class="required" for="no_of_samples">No. of Samples</label>
		                            <input class="form-control" type="text" name="samples_count" id="samples_count" data-validation="required">
		                        </div>
	                        </div>
	                        <div class="col-md-2">
	                        	<div class="form-group" style="padding-top: 22px !important;">
		                            <button class="btn btn-success" id="generateFields" style="padding: 5px 50px;font-size: 16px;">Inward</button>
		                        </div>
	                        </div>
	                    </div>

	                </div>
	            </div>
	            <div id="rowtemplate" style="display: none;">
	            	<div class="row">
						<div class="col-xs-12">
							<div id="sampleTypesSelect" style="display: none;">
								<div>
									<select class="form-control">
		                            	@foreach($sampleTypes as $id => $type)
		                            		<option value="{{$type->id}}" <?php echo ($type->default_selected == 1)?'selected':'';?>>{{$type->name}}</option>
		                            	@endforeach
		                            </select>
	                            </div>
                            </div>
                            <div id="testTypesSelect" style="display:none">
								<div>
                                    <select class="form-control">
                                        <option value="C">C</option>
                                        <option value="T">T</option>
                                        <option value="G">G</option>
                                        <option value="A">A</option>
                                    </select>
	                            </div>
							</div>
							<div class="box">
								@if(!empty($fromSampleId) && !empty($toSampleId))
									<div class="box-header" style="border-bottom: 1px solid #d4cdcd;color: #4d9ea0;">
										<h4 class="box-title" style="font-size: 13px;font-weight: 700;">Last inserted sample ids from {{$fromSampleId}} to {{$toSampleId}}</h4>
									</div>
								@endif
								<div class="box-header">
									<h3 class="box-title">Add Patient Details</h3>
								</div>
								<!-- /.box-header -->
								<div class="box-body table-responsive no-padding">
									<table class="table table-hover">
										<tbody>
											<tr>
                                                <th style="text-align: center;">Sample ID</th>
                                                <!-- <th>Test Type</th> -->
												<th>Name</th>
												<th>Patient Reference</th>
												<th>Contact No</th>
												<th>Specimen Type</th>
												<th>Age</th>
												<th>Gender</th>
												<th>Address</th>
												<th>Delete</th>
											</tr>
										</tbody>
									</table>
									<div style="margin-left: 10px;">
										<a href="javascript:void(0)" id="add_one_more_sample"><span class="glyphicon glyphicon-plus-sign" style="font-size: 143%;color: #398439;cursor: pointer;"></span></a>
									</div>

								</div>
								<!-- /.box-body -->
								<div class="box-footer clearfix">
									<div class="form-group">
			                            <button class="btn btn-success pull-right" id="submit_inwards">Save</button>
			                        </div>
								</div>
							</div>
							<!-- /.box -->
						</div>
					</div>
	            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade overlay-wrapper" id="modal_sample_id_error">
	<div class="modal-dialog">
    	<div class="modal-content" style="width: 600px;margin: 0 auto;">
      		<div class="modal-header" style="text-align: center;">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
          		</button>
        		<h4 class="modal-title" style="color:#dd4b39;">Sample ID Error</h4>
      		</div>
      		<div class="modal-body">
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
		var APP_URL = {!! json_encode(url('/')) !!}
		//Date picker
	    $('#collect_datepicker').datetimepicker({
			defaultDate: new Date(),
	    	format: 'DD/MM/YYYY'
	    });

	    $('#receive_datepicker').datetimepicker({
			defaultDate: new Date(),
	    	format: 'DD/MM/YYYY hh:mm a'
	    });

		$('#generateFields').on('click', function(e){
			e.preventDefault();

			if($("#rowtemplate").find('tr').length > 2){
				alert('Input grid is already formed. Kindly refresh to generate new grid!');
				return false;
			}

			if($(this).hasClass('editFields')){
				//$('input#samples_count').prop('disabled',false);
				$('#facility_id').prop('disabled',false);
				$('#receive_datepicker').prop('disabled',false);
				$('#collect_datepicker').prop('disabled',false);

				$(this).removeClass('editFields').text('Inward');
				return false;
			}

			var sampleCount = parseInt($('input#samples_count').val());
			var facility = $('#facility_id').val();
			var received_at = $('#receive_datepicker').val();
			var collected_at = $('#collect_datepicker').val();

			if(!facility){
				alert('Please select facility.');
				return false;
			}

			if(!collected_at){
				alert('Please select sample collection date.');
				return false;
			}

			if(!received_at){
				alert('Please select sample receiving date & time.');
				return false;
			}

			//var samplePrefix = 'C';


			if(Number.isInteger(sampleCount) && sampleCount >= 1){
				create_rows(0,sampleCount);

				/*$('input#samples_count').prop('disabled',true);
				$('#facility_id').prop('disabled',true);
				$('#receive_datepicker').prop('disabled',true);
				$('#collect_datepicker').prop('disabled',true);

				$(this).removeAttr('id').addClass('editFields').text('Edit');*/
			}
			else{
				alert('No. of samples must be numberic and greater than 1');
			}
		});

		$('#add_one_more_sample').on('click', function(e){
			var prevSampleCount = parseInt($('input#samples_count').val());
			$('input#samples_count').val(prevSampleCount+1);
			create_rows(prevSampleCount, (prevSampleCount+1));
		});

		$('#submit_inwards').on('click',function(e){
			e.preventDefault();

			var arr = [];
			var dupMsg = '';
			$("input.sampleidcheck").each(function(){
			    var value = $(this).val();
			    if (arr.indexOf(value) == -1)
			        arr.push(value);
			    else{
			        dupMsg = 'You have entered duplicate sample id - '+value;
			        return false;
			    }
			});

			if(dupMsg != ''){
				alert(dupMsg);
				return false;
			}

			$.ajax({
	          	//headers: {'x-csrf-token': _token},
				  headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
					  },
	          	method: 'POST',
	          	url: APP_URL+'/admin/inwards/check-sample-ids',
	          	data: { data: $("#add_sample_form").serialize() },
	          	success: function (response) {
	          		if(response.status == 'success'){
	          			$("#add_sample_form").submit();
	          		}
	          		else{
	          			$('.overlay').hide();
	          			$('#modal_sample_id_error').find('.modal-body').html(response.html);
	          			$('#modal_sample_id_error').modal('show');
	          		}
	          		return false;
	          	}
          	})
		});

		$('body').on('focusout','.sampleidcheck', function() {
			sampldId = $(this).val();

			if(sampldId){
				$.ajax({
		          	//headers: {'x-csrf-token': _token},'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
					  headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
					  },
		          	method: 'POST',
		          	url: APP_URL+'/admin/inwards/check-sample-ids',
		          	data: { sample_id: sampldId, check:'onlyDuplicate' },
		          	success: function (response) {
		          		if(response.status == 'success'){
		          			return false;
		          		}
		          		else{
		          			$('.overlay').hide();
		          			$('#modal_sample_id_error').find('.modal-body').html(response.html);
		          			$('#modal_sample_id_error').modal('show');
		          		}
		          		return false;
		          	}
	          	})
			}
		});

		$('body').on('click', '#editFields', function(){
			$('input#samples_count').prop('disabled',false);
			$('#facility_id').prop('disabled',false);
			$('#receive_datepicker').prop('disabled',false);
			$('#collect_datepicker').prop('disabled',false);

			$(this).attr('id','generateFields').text('Inward');
		});

		$('body').on('click', '.remove_row', function(){
			if(confirm('Are you sure you want to delete this record ?')){
				$(this).closest('tr').remove();
				var sampleCount = parseInt($('input#samples_count').val());
				$('input#samples_count').val(sampleCount-1);
			}
		});

		function create_rows(start, numberOfRows){
			var samplesFieldHtml = '';
            var sampleTypesHtml = $($("#sampleTypesSelect").html());
            var testTypesHtml = $($("#testTypesSelect").html());

            for (i = start; i < numberOfRows; i++) {
                sampleTypesHtml.find('select').attr('name', 'detail[' + i + '][sample_type_id]');
                testTypesHtml.find('select').attr('name', 'detail[' + i + '][sample_id_prefix]').attr('class', 'samplePrefix');
                samplesFieldHtml += '<tr class="newlyadded"><td style="text-align: center;width:12%;"><div class="form-group input-group prefix" style="float:left; margin-right:2px;">' + testTypesHtml.html() + '</div><div class="form-group input-group"><input type="text" name="detail[' + i + '][sample_id]" class="form-control sampleidcheck" data-validation="required"></div></td><td><div class="form-group"><input type="text" name="detail[' + i + '][name]" class="form-control" data-validation="required"></div></td><td width="10%"><div class="form-group"><input type="text" name="detail[' + i + '][patient_id]" class="form-control"></div></td><td width="10%"><div class="form-group"><input type="text" name="detail[' + i + '][contact_no]" class="form-control" data-validation="number" maxlength="11" data-validation-optional="true" data-validation-error-msg="The input value has to be numeric (max:11 digits)"></div></td><td width="19%"><div class="form-group">' + sampleTypesHtml.html() + '</div></td><td width="5%">\<div class="form-group">\<input type="text" name="detail[' + i + '][age]" class="form-control"></div></td><td width="7%"><div class="form-group"><select class="form-control" name="detail[' + i + '][sex]"><option value="">Select</option><option value="M">Male</option><option value="F">Female</option><option value="O">Other</option></select></div></td><td><div class="form-group"><input type="text" name="detail[' + i + '][address]" class="form-control"></div></td><td><a class="remove_row" href="javascript:void(0)"><span class="glyphicon glyphicon-trash" style="font-size: 143%;color: red;cursor: pointer;"></span></a></td></tr>';
            }

			$('#rowtemplate').find('table tbody').append(samplesFieldHtml);

			//$('#rowtemplate').show();
			$("#rowtemplate").animate( { "opacity": "show", top:"100"} , 1000 );
			$.validate();
		}
	});
</script>
@endsection
