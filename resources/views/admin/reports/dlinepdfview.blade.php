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
				<h3>Format D- Line list of Confirmed COVID-19 cases &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date - {{date('d-m-Y', strtotime($to_date))}}</h3>
			</div>
			<table style="margin-top:50px;">
			  	<thead>
				    <tr>
				      	<th>Case. No</th>
				      	<th>Name</th>
				      	<th>Age</th>
				      	<th>Gender</th>
				      	<th>Address</th>
				      	<th>Contact Number</th>
				      	<th>NAME OF REFERRING FACILITY</th>
				      	<th>Date of samle collection</th>
				      	<th>Date of samle EXAMINATION</th>
				      	<th>RESULT (POSITIVE/NEGATIVE) 2019-nCoV (DETECTED/NOT DETECTED)</th>
						<th>Remarks</th>
				    </tr>
			  	</thead>
			  	<tbody>
                    @foreach($data as $key => $inward)
                        <tr>
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
                            <td>{{$inward->collected_at?date('d-m-Y', strtotime($inward->collected_at)):''}}</td>
                             <td>{{$inward->tested_at?date('d-m-Y', strtotime($inward->tested_at)):''}}</td>
                            <td><b>{{$inward->status}}</b></td>
							<td>{{$inward->remarks}}</td>
                        </tr>
                    @endforeach
			  	</tbody>
			</table>
	    </section>		
  	</main>
</body>
</html>