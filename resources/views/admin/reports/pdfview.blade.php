<html>
<head>
  <style>
    @page { 
    	margin-top: 50px; margin-bottom: 280px;
    }
    body{
		text-align: center;
		margin: 0 auto;
		font-family: 'Open Sans', sans-serif;
	}
    header { 
    	position: fixed; 
    	top: 0px; 
    	left: 0px; 
    	right: 0px;
    	line-height: 10px;
		font-size: 13px;
    }
    footer { 
    	position: fixed; 
    	bottom: 0px; 
    	left: 0px; 
    	right: 0px;
    	height: 50px; 
	}
	#guidance{
		margin: 0 auto;
	    width: 90%;
	    text-align: left;
	    margin-top: 50px;
        font-size: 10px;
	    font-weight: bold;
	    font-style: italic;
	}

	#signatures{
		width: 90%;
    	margin: 0 auto;
    	font-weight: bold;
    	font-size: 13px;
	}

	#signatures #verified_by{
		float: left;
		text-align: left;
	}

	#signatures #approved_by{
		float: right;
		text-align: right;
	}

	#signatures img{
		width: 80px;
		height: 25px;
	}

	#logo{
		position: absolute;
	}

	#logo img{
		width: 60px;
	    height: 60px;
	    margin-left: 40px;
	}

	table{
		width: 90%;
		margin: 0 auto;
		border-collapse: collapse;
    	border-spacing: 0;
	}

	table td,table th {
		border:1px solid #000;
		padding: 0;
		margin: 0;
		text-align: center;
	}

	table th {
		font-size: 12px;
	}
	table td {
		font-size: 13px;
	}
	#brief_table{
		position: relative;
		font-weight: 700;
		font-size: 12px;
	}
	#facility_name{
		font-size: 16px;
		background-color: #DADADA;
	}
	#watermark {
		position: fixed;
		top: 35%;
		left: 55px;
		transform: rotate(310deg);
		transform-origin: 50% 50%;
		opacity: .3;
		font-size: 100px;
		color: gray;
		width: 600px;
		text-align: center;
	}
    section { page-break-after: always; }
    section:last-child { page-break-after: never; }
  </style>
</head>
<body>
	<?php if(isset($preview) && $preview == true) { ?>
		<div id="watermark">Unauthorized</div>
	<?php } ?>
	<header>
	  	<div id="logo">
			<img src="images/tmc_logo.png">
		</div>
		<div id="header">
			<h3>NATIONAL COVID-19 LABORATORY</h3>
			<h3>DEPARTMENT OF MICROBIOLOGY, R.G.M.C. C.S.M.H.</h3>                    
			<h3>COVID 19 TESTING FACILITY: C. R. Wadia Dispensary, Thane 400601</h3>
			<h3>RT-PCR Test Report</h3>
		</div>
  	</header>
  	<footer>
  		<div id="guidance">
			Note: The results relate only to the specimens tested and should be correlated with clinical findings.<br/>
			Interpretation guidance:-
			<ul style="list-style-type:disc">
			  <li>Testing of referred clinical specimens was considered on the basis of request / referral received from / through State Surveillance Officer (SSO) of concerned State   Integrated Disease Surveillance Programme (IDSP)/any other health care facility affirming requirements of the case definition/s.</li>
			  <li>A positive test result is only tentative, and will be reconfirmed by retesting.</li>
			  <li>Repeat sampling and testing of lower respiratory specimen is strongly recommended in severe or progressive disease.</li>
			  <li>The repeat specimens may be considered after a a gap of 2 – 4 days after the collection of the first specimen foradditional testing if required.*</li>
			  <li>A positive alternate pathogen does not necessarily ruleout either, as little is yet known about the role ofcoinfections.</li>
			  <li>Please note that these results are not to be used for any thesis or presentations or for Publication in any Journal without the prior permission of the Director General,ICMR</li>
			</ul>     
		</div>
		<div id="signatures">
			<div id="verified_by">
				<img src="images/sign2.png"><br/>
				Verified by <br/>
				Dr. Shalmali Dharma Ph. D <br/>
			</div>
			<div id="approved_by">
				<img src="images/sign1.png"><br/>
				Checked and Approved by <br/>
				Dr. Milind Ubale M.D. (Microbiology) <br/>			
			</div>
		</div>
  	</footer>
  	<main>
  		<?php 
  			if(!empty($data)){
	  			foreach($data as $sampleKey=>$patientSType){
	  				$curSampleType = $sampleTypes[$sampleKey]['name'];
	  				$received_at = reset($patientSType)['received_at'];
	  				$patientChunks = array_chunk($patientSType, 15);

	  				foreach($patientChunks as $patients){
		?>
					    <section>
					    	<table id="brief_table" style="margin-top:120px;">
								<tbody>
									<tr>
										<td>Date & Time of receipt of specimen (dd/mm/yyyy)</td>
										<td>{{date('d/m/Y H:i a', strtotime($received_at))}}</td>
									</tr>
									<tr>
										<td>Date and time of reporting (dd/mm/yyyy)</td>
										<td>{{date('d/m/Y H:i a', strtotime($reported_at))}}</td>
									</tr>
									<tr>
										<td>SPECIMEN DETAILS</td>
										<td>{{$curSampleType}}</td>
									</tr>
									<tr id="facility_name">
										<td>NAME OF QUARANTINE CENTER / HOSPITAL</td>
										<td>{{$facility_name}}</td>
									</tr>
								</tbody>
							</table>
							<table style="margin-top:40px;">
							  	<thead>
								    <tr>
								      	<th>Sample ID</th>
								      	<th>Patient Name</th>
								      	<th>Age (Yrs)</th>
								      	<th>Gender</th>
								      	<th>Date of sample testing</th>
								      	<th>SARS-CoV 2</th>
								    </tr>
							  	</thead>
							  	<tbody>
							    	@foreach($patients as $inward)
								      	<tr>
								        	<td>{{ $inward['sample_id'] }}</td>
									        <td>{{ $inward['name'].' '.$inward['patient_id'] }}</td>
									        <td>{{ $inward['age'] }}</td>
									        <td>{{ $inward['sex'] }}</td>
									        <td>{{ date('d.m.Y', strtotime($inward['tested_at'])) ?? '' }}</td>
											<td>{!! ($inward['status'] == 'Positive') ? '<b>'.$inward['status'].'</b>' : $inward['status'] !!}</td>
								      	</tr>
							    	@endforeach
							  	</tbody>
							</table>
					    </section>
    	<?php
    				}
    			}
			}
	    ?>

	    <?php 
	    	if(!empty($remarkedPatients)){
	  			foreach($remarkedPatients as $sampleKey=>$patientSType){
	  				$curSampleType = $sampleTypes[$sampleKey]['name'];
	  				$received_at = reset($patientSType)['received_at'];
	  				$patientChunks = array_chunk($patientSType, 15);

	  				foreach($patientChunks as $patients){
		?>
					    <section>
					    	<table id="brief_table" style="margin-top:120px;">
								<tbody>
									<tr>
										<td>Date & Time of receipt of specimen (dd/mm/yyyy)</td>
										<td>{{date('d/m/Y H:i a', strtotime($received_at))}}</td>
									</tr>
									<tr>
										<td>Date and time of reporting (dd/mm/yyyy)</td>
										<td>{{date('d/m/Y H:i a', strtotime($reported_at))}}</td>
									</tr>
									<tr>
										<td>SPECIMEN DETAILS</td>
										<td>{{$curSampleType}}</td>
									</tr>
									<tr id="facility_name">
										<td>NAME OF QUARANTINE CENTER / HOSPITAL</td>
										<td>{{$facility_name}}</td>
									</tr>
								</tbody>
							</table>
							<table style="margin-top:40px;">
							  	<thead>
								    <tr>
								      	<th>Sample ID</th>
								      	<th>Patient Name</th>
								      	<th>Age (Yrs)</th>
								      	<th>Gender</th>
								      	<th>Date of sample testing</th>
								      	<th>SARS-CoV 2</th>
								      	<th>Remarks</th>
								    </tr>
							  	</thead>
							  	<tbody>
							    	@foreach($patients as $inward)
								      	<tr>
								        	<td>{{ $inward['sample_id'] }}</td>
									        <td>{{ $inward['name'].' '.$inward['patient_id'] }}</td>
									        <td>{{ $inward['age'] }}</td>
									        <td>{{ $inward['sex'] }}</td>
									        <td>{{ date('d.m.Y', strtotime($inward['tested_at'])) ?? '' }}</td>
									        <td>{!! ($inward['status'] == 'Positive') ? '<b>'.$inward['status'].'</b>' : $inward['status'] !!}</td>
									        <td>{{ $inward['remarks'] }}</td>
								      	</tr>
							    	@endforeach
							  	</tbody>
							</table>
					    </section>
    	<?php
    				}
    			}
			}
	    ?>
  	</main>
</body>
</html>