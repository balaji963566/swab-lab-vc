@if(!empty($skippedSample))
	<div class="skip-error" style="margin-bottom: 20px;">
		<label>Skip Error:</label>
		<span style="display: block;">You have skipped Sample ID after <b>{{$skippedSample}}</b></span>
	</div>
@endif

@if(!empty($inwardData))
	<div class="duplicate-error" style="margin-bottom: 15px;">
		<label>Duplicate Sample ID Error:</label>
		<span style="display: block;">Submitted sample ids are already in use:</span>
	</div>
	<div class="table-responsive">
	    <table class=" table table-bordered table-striped table-hover">
	        <thead>
	            <tr>
	                <th>Facility Name</th>
	                <th>Sample ID</th>
	                <th>Name</th>
	                <th>Age</th>
	                <th>Gender</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php $cnt = 1; ?>
	            @foreach($inwardData as $key => $inward)
	                <tr>
	                    <td>{{ $inward['facility']['name'] }}</td>
	                    <td>{{ $inward['sample_id'] }}</td>
	                    <td>
	                    	{{ $inward['name'] ?? '' }}
	                    	<br> <span style="font-size: 10px;">{{ $inward['patient_id'] ?? '' }}</span>
	                    </td>
	                    <td>{{ $inward['age'] ?? '' }}</td>
	                    <td>{{ $inward['sex'] ?? '' }}</td>
	                </tr>
	                <?php $cnt++; ?>
	            @endforeach
	        </tbody>
	    </table>
	</div>
@endif