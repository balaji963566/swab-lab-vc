@if(!empty($report))
	<div class="duplicate-error" style="margin-bottom: 15px;">
		<label>Generated report criteria:</label>
	</div>
	<div class="table-responsive">
	    <table class=" table table-bordered table-striped table-hover">
	        <thead>
	            <tr>
	                <th>From Date</th>
	                <th>To Date</th>
	            </tr>
	        </thead>
	        <tbody>
                <tr>
                    <td>{{date('d/m/Y', strtotime($report->from_date))}}</td>
                    <td>{{date('d/m/Y', strtotime($report->to_date))}}</td>
                </tr>
	        </tbody>
	    </table>
	</div>
@endif

@if(!empty($stateFacility))
	<div class="duplicate-error" style="margin-bottom: 15px;">
		<label>Select mail ids to send report:</label>
	</div>

	<div class="form-group">
        <div class="checkbox">
			@foreach($stateFacility->facility_emails as $email)
		    	<label style="margin-right: 20px;">
					<input type="checkbox" name="emails[]" value="{{$email->id}}" data-validation="required"> 
					{{$email->email}}
				</label>
    		@endforeach	
		</div>
    </div>
@endif