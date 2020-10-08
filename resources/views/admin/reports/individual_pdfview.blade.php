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

	#brief_table{
		width: 90%;
		margin: 0 auto;
		border-collapse: collapse;
    	border-spacing: 0;
    	position: relative;
		font-weight: 700;
		font-size: 12px;
	}

	#brief_table td,#brief_table th {
		border:1px solid #000;
		padding: 0;
		margin: 0;
		text-align: center;
	}

	#brief_table th {
		font-size: 12px;
	}
	#brief_table td {
		font-size: 13px;
	}

	#remark_table{
		border-collapse: collapse;
    	border-spacing: 0;
    	position: relative;
		font-weight: 400;
		margin-left: 30px;
		line-height: 30px;
		border-spacing: 10px;
		border-collapse: separate;
	}

	#facility_name{
		font-size: 16px;
		background-color: #DADADA;
	}

	table{
		width: 90%;
		margin: 0 auto;
		border-collapse: collapse;
    	border-spacing: 0;
	}

	#detail_table td,table th {
		border:1px solid #000;
		padding: 0;
		margin: 0;
		text-align: center;
	}

	#detail_table th {
		font-size: 12px;
	}
	#detail_table td {
		font-size: 13px;
	}
    section { page-break-after: always; }
    section:last-child { page-break-after: never; }
  </style>
</head>
<body>
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
	    <section>
	    	<table id="brief_table" style="margin-top:120px;">
				<tbody>
					<tr>
						<td>Date & Time of receipt of specimen (dd/mm/yyyy)</td>
						<td>{{date('d/m/Y H:i a', strtotime($data->received_at))}}</td>
					</tr>
					<tr>
						<td>Date and time of testing (dd/mm/yyyy)</td>
						<td>{{date('d/m/Y H:i a', strtotime($data->tested_at))}}</td>
					</tr>
					<tr>
						<td>SPECIMEN DETAILS</td>
						<td>{{$data->sample_type->name}}</td>
					</tr>
					<tr id="facility_name">
						<td>NAME OF QUARANTINE CENTER / HOSPITAL</td>
						<td>{{$data->facility->name}}</td>
					</tr>
				</tbody>
			</table>
			<table id="detail_table" style="margin-top:40px;">
			  	<thead>
				    <tr>
				      	<th>Sample ID</th>
				      	<th>Patient Name</th>
				      	<th>Age (Yrs)</th>
				      	<th>Gender</th>
				      	<th>SARS-CoV 2</th>
				    </tr>
			  	</thead>
			  	<tbody>
			      	<tr>
			        	<td>{{$data->sample_id}}</td>
				        <td>{{$data->name}}</td>
				        <td>{{$data->age}}</td>
				        <td>{{$data->sex}}</td>
				        <td>{{$data->status}}</td>
			      	</tr>
			  	</tbody>
			</table>
			@if($remarks)
			<table id="remark_table" style="margin-top:40px;">
				<tbody>				    
			    	<tr>
				      	<td style="font-weight: bold;width:50px;">Remarks</td>
				      	<td>:  {{$remarks}}</td>
				    </tr>				    
			    </tbody>
			</table>
			@endif
	    </section>
  	</main>
</body>
</html>