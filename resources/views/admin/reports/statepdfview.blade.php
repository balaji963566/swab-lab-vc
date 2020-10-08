<html>
<head>
  	<style>
	  	@page { 
	  		margin:50px 0px -30px 0px;
	    }
	    body{
			text-align: center;
			margin: 0 auto;
			font-family: 'Open Sans', sans-serif;
			text-transform: uppercase;
			line-height: 1.5;
		}
		#guidance{
			margin: 0 auto;
		    width: 90%;
		    text-align: left;
		    margin-top: 50px;
	        font-size: 10px;
	        letter-spacing: 0px;
	        font-family: sans-serif;
		}

		#signatures{
			position: relative;
			width: 90%;
	    	margin: 0 auto;
	    	font-weight: bold;
	    	font-size: 13px;;
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
		    margin-left: 60px;
		}

		table{
			width: 90%;
			margin: 0 auto;
			border-collapse: collapse;
	    	border-spacing: 0;
		}

		table td,table th {
			border:1px solid #000;
			padding: 5px;
			margin: 0;
			text-align: center;
		}

		table th {
			font-size: 10px;
		}
		table td {
			font-size: 11px;
		}
		#brief_table{
			position: relative;
			font-size: 10px !important;
		}
		#header span{
			display: block;
			font-size: 10px;
			font-weight: bold;
		}
		section { page-break-after: always; }
	    section:last-child { page-break-after: never; }
  	</style>
</head>
<body>
  	<div id="logo">
		<img src="images/tmc_logo.png">
	</div>
  	<main>
  		<section>
  			<div id="header">                   
				<span>Test Report</span>
				<span>NAME OF THE LABORATORY/ VRDL : RGMC & CSMH, THANE</span>
			</div>
	    	<table id="brief_table" style="margin-top:40px;">
				<tbody>
					<tr>
						<td style="width: 50%">DATE AND TIME OF REPORTING TO STATE AUTHORITY ( DD/MM/YYYY) :12 HOUR FORMAT</td>
						<td style="width: 50%">{{date('d/m/Y H:i a', strtotime($mailed_at))}}</td>
					</tr>
					<tr>
						<td style="width: 50%">REPORTING DETAILS</td>
						<td style="width: 50%">SARS- COV 2 RTPCR</td>
					</tr>
					<tr>
						<td style="width: 50%">REPORT ID</td>
						<td style="width: 50%">GOVT. MEDICAL COLLEGE</td>
					</tr>
				</tbody>
			</table>
			<table style="margin-top:50px;">
			  	<thead>
				    <tr>
				      	<th>SR. NO</th>
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
                        @foreach($data as $key => $inward)
                            <tr>
                                <td style="text-align: center;">{{ $cnt ?? '' }}</td>
                                <td>{{ $inward->sample_id ?? '' }}</td>
                                <td>
                                	{{ $inward->name ?? '' }}
                                	<?php echo ($inward->patient_id)?' / '.$inward->patient_id:''; ?>
                                </td>
                                <td>{{ $inward->age ?? '' }}</td>
                                <td>{{ $inward->sex ?? '' }}</td>
                                <td>{{ $inward->address ?? '' }}</td>
                                <td>{{ $inward->contact_no ?? '' }}</td>
                                <td>{{ $inward->facility->name ?? '' }}</td>
                                <td>{{ $inward->sample_type->name ?? '' }}</td>
                                <td>{{$inward->reported_at?date('d-m-Y', strtotime($inward->reported_at)):''}}</td>
                                <td>{!! ($inward->status == 'Positive') ? '<b>'.$inward->status.'</b>' : $inward->status !!}</td>
                            </tr>
                            <?php $cnt++; ?>
                        @endforeach
			  	</tbody>
			</table>
	    </section>
	    <div id="guidance">
	    	<div style="font-style: italic;margin-bottom: 30px;position: relative;">
		    	<p>*INFLUENZA A ,CINFLUENZA B, INFLUENZA A(H1N1)HUMAN RHINOVIRUS, HUMAN CORONA VIRUS (OC45,NL63,229E,HKU1), PARAINFLUENZA VIRUS(1,2,3,4), HUMAN BOCAVIRUS, HUMAN METAPNEUMO VIRUS A & B ,HUMAN RESPIRATORY SYNCYTIAL A & B, HUMAN ADENO VIRUS, ENTEROVIRUS, HUMAN PARECHOVIRUS & MYCOPLASMA PNEUMONIA
		    	</p>
	    	</dvi>
	    	<div id="signatures">
				<div id="verified_by">
					<img src="images/sign2.png"><br/>
					Prepared by <br/>
					Dr. Shalmali Dharma Ph. D <br/>
				</div>
				<div id="approved_by">
					<img src="images/sign1.png"><br/>
					Checked and Approved by <br/>
					Dr. Milind Ubale M.D. (Microbiology) <br/>			
				</div>
			</div>

			<div style="clear:both;padding-top: 30px;font-style: italic;">
				<p>
				Note: The results relate only to the specimens tested and should be correlated with clinical findings.<br/>
				Interpretation guidance:-
				</p>
				<ul style="list-style-type:disc">
				  <li>Testing of referred clinical specimens was considered on the basis of request / referral received from / through State Surveillance Officer (SSO) of concerned State   Integrated Disease Surveillance Programme (IDSP)/any other health care facility affirming requirements of the case definition/s.</li>
				  <li>A positive test result is only tentative, and will be reconfirmed by retesting.</li>
				  <li>Repeat sampling and testing of lower respiratory specimen is strongly recommended in severe or progressive disease.</li>
				  <li>The repeat specimens may be considered after a a gap of 2 – 4 days after the collection of the first specimen foradditional testing if required.*</li>
				  <li>A positive alternate pathogen does not necessarily ruleout either, as little is yet known about the role ofcoinfections.</li>
				  <li>Please note that these results are not to be used for any thesis or presentations or for Publication in any Journal without the prior permission of the Director General,ICMR</li>
				</ul> 
			</div>    
		</div>		
  	</main>
</body>
</html>