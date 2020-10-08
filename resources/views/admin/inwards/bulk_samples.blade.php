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
            <form method="POST" action="{{ route("admin.inwards.bulkSample") }}" enctype="multipart/form-data" id="add_sample_form">
                @csrf
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Bulk Samples Upload
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="name">Facility</label>
                                    <select class="form-control" name="facility_id" id="facility_id" data-validation="required">
                                        <option value="">Please select facility</option>
                                        @foreach($facilities as $id => $facility)
                                        <option value="{{$facility->id}}" <?php echo (isset($formData['facility_id']) && $formData['facility_id'] == $facility->id) ? 'selected' : ''; ?>>{{$facility->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="short_name">Sample collection date</label>
                                    <input type="text" class="form-control pull-right" id="collect_datepicker" name="collected_at" data-validation="required">
                                    <input type="hidden" name="collected_at_last_value" value="{{$formData['collected_at']??''}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="short_name">Sample receiving date & time</label>
                                    <input type="text" class="form-control pull-right" id="receive_datepicker" name="received_at" data-validation="required">
                                    <input type="hidden" name="received_at_last_value" value="{{$formData['received_at']??''}}">
                                </div>
                            </div>
                        </div>           
                    </div>
                </div>
                @if(!empty($exlData))
                <div id="rowtemplate">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                @if(!empty($fromSampleId) && !empty($toSampleId))
                                <div class="box-header" style="border-bottom: 1px solid #d4cdcd;color: #4d9ea0;">							
                                    <h4 class="box-title" style="font-size: 13px;font-weight: 700;">Last inserted sample ids from {{$fromSampleId}} to {{$toSampleId}}</h4>
                                </div>
                                @endif
                                <div class="box-header">
                                    <h3 class="box-title">Review Bulk Samples</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                <th style="text-align: center;">Sample ID</th>
                                                <th>Name</th>
                                                <th>Patient Reference</th>
                                                <th>Contact No</th>
                                                <th>Specimen Type</th>
                                                <th>Age</th>
                                                <th>Gender</th>
                                                <th>Address</th>
                                                <th>Reject</th>
                                            </tr>
                                            <?php
                                            $cnt = 1;
                                            $genderArr = array('Male' => 'M', 'Female' => 'F', 'Other' => 'O');
                                            ?>
                                            @foreach($exlData as $inward)
                                            <tr>
                                                <td style="text-align: center;width:8%;">
                                                    <div class="form-group input-group prefix">
<!--																<span class="sample_prefix">C</span> -->

                                                        <select name="detail[{{$cnt}}][sample_id_prefix]" class="form-control samplePrefix" style="float:left; width:45px;">
                                                            <option value="C">C</option>
                                                            <option value="T">T</option>
                                                            <option value="G">G</option>
                                                            <option value="A">A</option>
                                                        </select>

                                                        <input type="text" name="detail[{{$cnt}}][sample_id]" class="form-control sampleidcheck" value="{{$inward['sample_id']}}" data-validation="required" style="margin-left:50px; margin-top:-35px;">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" style="margin-left:48px;"> 
                                                        <input type="text" name="detail[{{$cnt}}][name]" class="form-control" value="{{$inward['name']}}" data-validation="required">
                                                    </div>
                                                </td>
                                                <td width="10%">
                                                    <div class="form-group"> 
                                                        <input type="text" name="detail[{{$cnt}}][patient_id]" class="form-control" value="{{$inward['patient_id']}}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group"> 
                                                        <input type="text" name="detail[{{$cnt}}][contact_no]" class="form-control" value="{{$inward['contact_no']}}" data-validation="number length" data-validation-length="11" data-validation-optional="true" data-validation-error-msg="The input value has to be numeric (11 digits)">
                                                    </div>
                                                </td>
                                                <td width="19%">
                                                    <div class="form-group"> 
                                                        <select class="form-control" name="detail[{{$cnt}}][sample_type_id]">
                                                            <option>Select</option>
                                                            @foreach($sampleTypes as $id => $type)
                                                            <option value="{{$type->id}}" <?php echo ($type->id == $inward['sample_type_id']) ? 'selected' : ''; ?>>{{$type->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td width="5%">
                                                    <div class="form-group"> 
                                                        <input type="text" name="detail[{{$cnt}}][age]" class="form-control" value="{{$inward['age']}}">
                                                    </div>
                                                </td>
                                                <td width="7%">
                                                    <div class="form-group">
                                                        <select class="form-control" name="detail[{{$cnt}}][sex]">
                                                            <option value="">Select</option>
                                                            <option value="M" <?php echo ($inward['sex'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                            <option value="F" <?php echo ($inward['sex'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                            <option value="O" <?php echo ($inward['sex'] == 'O') ? 'selected' : ''; ?>>Other</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group"> 
                                                        <input type="text" name="detail[{{$cnt}}][address]" class="form-control" value="{{$inward['address']}}">
                                                    </div>
                                                </td>
                                                <td style="width: 16%; text-align: center;">
                                                    <button class="btn btn-danger reject-button">Reject</button>
                                                    <select class="form-control" name="detail[{{$cnt}}][rejected_reason_id]" style="display: none;">
                                                        <option value="0">Select Reason</option>
                                                        @foreach($rejectedReasons as $reason)
                                                        <option value="{{$reason->id}}">{{$reason->reason}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php $cnt++; ?>
                                            @endforeach	
                                        </tbody>
                                    </table>
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
                @else
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Bulk Upload</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <p class="sub-header">
                                    <a href="{{asset('public/sample templates/bulk_samples.xlsx')}}">Download Sample Template</a>
                                </p>

                                <input type="file" data-plugins="dropify" data-height="300" name="xlsfile"/>
                            </div> <!-- end card-body-->
                            <div class="box-footer clearfix">
                                <div class="form-group">
                                    <button class="btn btn-success pull-right">Submit</button>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div>
                </div>
                @endif
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

	    var collected_at = $("input[name=collected_at_last_value]").val();
		
		if(collected_at){
			$('#collect_datepicker').datetimepicker({
		    	defaultDate: new Date(collected_at),
		    	format: 'DD/MM/YYYY'
		    });
		}
		else{
			$('#collect_datepicker').datetimepicker({
				defaultDate: new Date(),
		    	format: 'DD/MM/YYYY'
		    });
		}

		var received_at = $("input[name=received_at_last_value]").val();
		
		if(received_at){
			$('#receive_datepicker').datetimepicker({
		    	defaultDate: new Date(received_at),
		    	format: 'DD/MM/YYYY hh:mm a'
		    });
		}
		else{
			$('#receive_datepicker').datetimepicker({
				defaultDate: new Date(),
		    	format: 'DD/MM/YYYY hh:mm a'
		    });
		}

		$('#add_one_more_sample').on('click', function(e){
			var prevSampleCount = parseInt($('input#samples_count').val());
			$('input#samples_count').val(prevSampleCount+1);
			create_rows(prevSampleCount, (prevSampleCount+1));
		});

		$('.reject-button').on('click', function(e){
			e.preventDefault();
			if(confirm("Do you really want to reject this sample?")){
				$(this).hide();
				$(this).next().show();
			}
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
	          	headers: {'x-csrf-token': _token},
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
		          	headers: {'x-csrf-token': _token},
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
	});
</script>
@endsection