<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFacilityRequest;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Inward;
use App\Facility;
use App\FacilityEmails;
use App\SampleType;
use App\SampleStatus;
use App\Report;
use App\User;
use App\StateReport;
use App\DlineReport;
use App\Remark;
use App\Exports\ReportExport;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Mail;
use Carbon\Carbon;
use DB;
use Excel;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('reports_all'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();
        $facility_id = '';
        $reported_at = '';

        if(!empty($formData)){
        	if($formData['facility_id'])
        		$facility_id = $formData['facility_id'];

        	if($formData['reported_at'])
        		$reported_at = Carbon::createFromFormat('d-m-Y', $formData['reported_at']);
        }
		//echo date('Y-m-d', strtotime($reported_at)); exit;
		$condition = '';
		if($facility_id && $facility_id != 'All')
			$condition = 'where r.facility_id = '.$facility_id;

		if($reported_at){
			$reported_at = date('Y-m-d', strtotime($reported_at));
			if(!empty($condition))
				$condition .= ' and ';
			else
				$condition .= ' where ';

			$condition .= 'date(r.created_at) = "'.$reported_at.'"';
		}

		$reports = DB::select('Select f.name facility_name,r.created_at reported_at,r.total_count,r.id report_id from reports r inner join facilities f on r.facility_id = f.id '.$condition.' order by r.id desc');
		
        $facilities = Facility::where('facility_type','facility')->get();

        return view('admin.reports.index', compact('reports','facilities','facility_id','reported_at'));
    }

    public function generate(Request $request)
    {
        abort_if(Gate::denies('reports_generation_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = array();

        $formData = $request->all();

        if(!empty($formData['data'])){
        	$res = $this->generateReport($formData, $request);

        	if(isset($formData['preview'])){
        		return response()->json(array('status'=>'success', 'file'=>$res['filepath']));
        	}        		
        }

        if(!empty($formData)){
        	if(isset($formData['sample_types']) && !empty($formData['sample_types'])){
        		$formData['specimen_types'] = explode(',', $formData['sample_types']);
        	}
        	if(empty($formData['specimen_types']))
        		$formData['specimen_types'] = array(4); //default Nasopharyngeal

			$data['inwardData'] = Inward::with('facility','sample_type')
        							->where('facility_id',$formData['facility_id'])
        							->whereIn('sample_type_id',$formData['specimen_types'])
        							->whereNotIn('status', ['pending','rejected','sent_for_testing','On Hold'])
        							->whereNull('reported_at')
        							->get();
			
        	$data['facility_id'] = $formData['facility_id'];
        	$data['specimen_types'] = $formData['specimen_types'];
        	//print_r($data['specimen_types']); exit;
        	$data['facility'] = Facility::with('facility_emails')->where('id',$formData['facility_id'])->first();
        }
        
        $data['sampleStatus'] = SampleStatus::all();
        $data['sampleTypes'] = SampleType::all();
        $data['facilities'] = Facility::where('facility_type','facility')->get();

        return view('admin.reports.generate', compact('data'));
    }

    public function reverify(Request $request)
    {
        abort_if(Gate::denies('reports_status_change'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();

        if(!empty($formData)){
        	$response = array('status'=>'fail','message'=>'Entered password do not match our records.');
        	//echo "<pre>"; print_r(\Auth::user()->sign_password); exit;
        	$userId = \Auth::user()->id;
        	$signPass = \Auth::user()->sign_password;
        	
        	if($signPass!=''){
	        	if (\Hash::check($formData['password'], $signPass)) {
	        		if(isset($formData['curId'])){
	        			$status = Inward::select('status')->where('id',$formData['curId'])->first();
	        			$response = array('status'=>'success','for'=>'status','sample_status'=>$status->status);
	        		}
	        		else{
	        			$response = array('status'=>'success');
	        		}
			       	
			   	}
		   	}
		   	else{
		   		$response = array('status'=>'fail','message'=>'You have not set Signatory password yet!');
		   	}
        }

        return response()->json($response);
    }

    public function changeStatus(Request $request){
    	abort_if(Gate::denies('reports_status_change'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();

        if(!empty($formData)){
        	$inward = Inward::find($formData['curId']);	    		
    		$inward->status_changed_at = Carbon::now();
    		$inward->status = $formData['curStatus'];
    		$inward->save();
        }

        return response()->json(array('status'=>'success'));
    }

    private function generateReport($formData, $request)
    {    	
    	if(!empty($formData['data'])){
    		$facility = Facility::where('id',$formData['facility_id'])->first();
	        $sampleTypes = $formData['sample_types'];
	        $sampleTypesArr = explode(',',$sampleTypes);
	        $sampleTypesData = SampleType::whereIn('id',$sampleTypesArr)->get()->toArray();
	        $selectedSampleTypeIds = array_column($sampleTypesData, 'id');
	        $selectedSampleTypes = array_combine($selectedSampleTypeIds, $sampleTypesData);
	        // "<pre>"; print_r($selectedSampleTypes); 
	        $selectedPatientsIds = array_column($formData['data'], 'id');
	        $selectedPatients = array_combine($selectedPatientsIds, $formData['data']);

	        $patientData = Inward::whereIn('id',$selectedPatientsIds)->get()->toArray();
	        foreach($patientData as $patient){
	        	$allPatients[$patient['sample_type_id']][] = $patient;
	        }

	        $reportedDate = date('YmdHis');
	        $reportedAt = date('Y-m-d H:i',strtotime($reportedDate));
	        $reportedAtYear = date('Y',strtotime($reportedDate));
	        $reportedAtMonth = date('m',strtotime($reportedDate));
	        $reportedAtDay = date('d',strtotime($reportedDate));	        

        	if(isset($formData['preview'])){
        		$path = 'Reports/temp';

        		if(!is_dir(public_path($path))) {
	        		mkdir($path, 0777, true);	        		
	        	}


	        	$remarkedPatients = array();
	        	foreach($allPatients as $key=>$patients){
			        $allCnt = 0;
			        $remCnt = 0;
			        foreach($patients as $patient){
			        	if(!empty($selectedPatients[$patient['id']]['remarks'])){
			        		unset($allPatients[$key][$allCnt]); $allCnt++;
			        		if(count($allPatients[$key]) == 0){
			        			unset($allPatients[$key]);
			        		}
			        		$remarkedPatients[$key][$remCnt] = $patient;
			        		$remarkedPatients[$key][$remCnt]['name'] = $selectedPatients[$patient['id']]['name'];
			        		$remarkedPatients[$key][$remCnt]['age'] = $selectedPatients[$patient['id']]['age'];
			        		$remarkedPatients[$key][$remCnt]['sex'] = $selectedPatients[$patient['id']]['sex'];
			        		$remarkedPatients[$key][$remCnt]['remarks'] = $selectedPatients[$patient['id']]['remarks'];
					    	$remCnt++;
			        	}
			        	else{
			        		$allPatients[$key][$allCnt]['name'] = $selectedPatients[$patient['id']]['name'];
			        		$allPatients[$key][$allCnt]['age'] = $selectedPatients[$patient['id']]['age'];
			        		$allPatients[$key][$allCnt]['sex'] = $selectedPatients[$patient['id']]['sex'];
			        		$allPatients[$key][$allCnt]['remarks'] = $selectedPatients[$patient['id']]['remarks'];
					    	$allCnt++;
				    	}
		        	}
		        }
        		
	        	$allArr['data'] = $allPatients;
	        	$allArr['remarkedPatients'] = $remarkedPatients;
	        	$allArr['facility_name'] = $facility->name;
	        	$allArr['reported_at'] = $reportedAt;
	        	$allArr['sampleTypes'] = $selectedSampleTypes;
	        	
	        	$report_name = $facility->name.$reportedDate;

	        	//$allArr = $this->generateReport($formData, $request);
        		$allArr['preview'] = true;
        		$pdf = \PDF::loadView('admin.reports.pdfview', $allArr);
        		$pdf->save(public_path($path).'/'.$report_name.'.pdf');
	        	//return $pdf->download($allArr['report_name'].'.pdf');
	        	$response['filepath'] = asset($path.'/'.$report_name.'.pdf');

	        	return $response;
        	}
        	else if(isset($formData['update'])){
        		$remarkedPatients = array();
        		foreach($allPatients as $key=>$patients){
        			$allCnt = 0;
		        	foreach($patients as $patient){
		        		$allPatients[$key][$allCnt]['name'] = $selectedPatients[$patient['id']]['name'];
		        		$allPatients[$key][$allCnt]['age'] = $selectedPatients[$patient['id']]['age'];
		        		$allPatients[$key][$allCnt]['sex'] = $selectedPatients[$patient['id']]['sex'];
		        		$allPatients[$key][$allCnt]['remarks'] = $selectedPatients[$patient['id']]['remarks'];

		        		$inward = Inward::find($patient['id']);
			    		$inward->name = $allPatients[$key][$allCnt]['name'];
			    		$inward->age = $allPatients[$key][$allCnt]['age'];
			    		$inward->sex = $allPatients[$key][$allCnt]['sex'];
			    		$inward->remarks = $allPatients[$key][$allCnt]['remarks'];
			    		
			    		$inward->save();

			    		$allCnt++;
		        	}
		        }

		        $request->session()->flash('alert-success', 'Patient details updated successfully!');
        	}
        	else{
	        	$path = 'Reports/'.$reportedAtYear.'/'.$reportedAtMonth.'/'.$reportedAtDay.'/'.$facility->name;

	        	if(!is_dir(public_path($path))) {
	        		mkdir($path, 0777, true);	        		
	        	}

	        	$report = new Report;
		        $report->name = $facility->name.$reportedDate;
		        $report->facility_id = $facility->id;
		        //$report->sample_type_id = $key;
		        $report->file_path = $path;
		        //$report->total_count = count($allPatients[$key]);

		        $report->save();
		        $reportId = $report->id;
		        //$allReportsId[] = $reportId;

		        $totalCount = 0;
		        $remarkedPatients = array();
		        foreach($allPatients as $key=>$patients){
			        $allCnt = 0;
			        $remCnt = 0;
			        $totalCount = $totalCount + count($allPatients[$key]);

		        	foreach($patients as $patient){
		        		if(!empty($selectedPatients[$patient['id']]['remarks'])){
			        		unset($allPatients[$key][$allCnt]); $allCnt++;
			        		if(count($allPatients[$key]) == 0){
			        			unset($allPatients[$key]);
			        		}
			        		$remarkedPatients[$key][$remCnt] = $patient;
			        		$remarkedPatients[$key][$remCnt]['name'] = $selectedPatients[$patient['id']]['name'];
			        		$remarkedPatients[$key][$remCnt]['age'] = $selectedPatients[$patient['id']]['age'];
			        		$remarkedPatients[$key][$remCnt]['sex'] = $selectedPatients[$patient['id']]['sex'];
			        		$remarkedPatients[$key][$remCnt]['remarks'] = $selectedPatients[$patient['id']]['remarks'];
					    	$remCnt++;
			        	}
			        	else{
			        		$allPatients[$key][$allCnt]['name'] = $selectedPatients[$patient['id']]['name'];
			        		$allPatients[$key][$allCnt]['age'] = $selectedPatients[$patient['id']]['age'];
			        		$allPatients[$key][$allCnt]['sex'] = $selectedPatients[$patient['id']]['sex'];
			        		$allPatients[$key][$allCnt]['remarks'] = $selectedPatients[$patient['id']]['remarks'];
			        		$allCnt++;
			        	}

		        		$inward = Inward::find($patient['id']);	    		
			    		$inward->name = $selectedPatients[$patient['id']]['name'];
			    		$inward->age = $selectedPatients[$patient['id']]['age'];
			    		$inward->sex = $selectedPatients[$patient['id']]['sex'];
			    		$inward->remarks = $selectedPatients[$patient['id']]['remarks'];
			    		$inward->reported_at = $reportedAt;
			    		$inward->report_id = $reportId;
			    		$inward->save();
		        	}
		        }

		        $updateReport = Report::find($reportId);
	    		$updateReport->total_count = $totalCount;
	    		$updateReport->save();

	        	$allArr['data'] = $allPatients;
	        	$allArr['remarkedPatients'] = $remarkedPatients;
	        	$allArr['facility_name'] = $facility->name;
	        	$allArr['reported_at'] = $reportedAt;
	        	$allArr['sampleTypes'] = $selectedSampleTypes;

	        	$pdf = \PDF::loadView('admin.reports.pdfview', $allArr);
				$pdf->save(public_path($path).'/'.$report->name.'.pdf');

				$this->send_email($reportId);

				$request->session()->flash('alert-success', 'Report generated successfully and sent to facility!');
			}
	 	}
    }

    private function send_email($reportId){
    	$report = Report::where('id',$reportId)->first();
    	$emails = $report->facility->facility_emails->toArray();
    	$emailIds = array_column($emails, 'email');

		Mail::send('admin.reports.mail', [], function($message) use($report, $emailIds) {
			$message->to($emailIds)->subject
			('RPCR Report of covid 19');
			$message->attach($report->file_path.'/'.$report->name.'.pdf');
			$message->from('rgmcmicrocovid19@gmail.com','Rgmc Micro');
		});
      	
      	return true;
    }

    public function downloadPdf($report_id){
    	abort_if(Gate::denies('report_all_download_pdf'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$report = Report::where('id',$report_id)->first();


    	$filepath = public_path($report->file_path.'/'.$report->name.'.pdf');
    	//echo "<pre>"; print_r($filepath); exit;
    	$headers = ['Content-Type: application/pdf'];
    	$newName = $report->name.'.pdf';


    	return response()->download($filepath, $newName, $headers);
    }

    public function downloadExcel($report_id){
    	abort_if(Gate::denies('report_all_download_excel'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$inwardData = Inward::with('facility','sample_type')->where('report_id',$report_id)->get()->toArray();

    	$genderArr = array('M'=>'Male','F'=>'Female','O'=>'Other');
    	$excelData = [];
        foreach($inwardData as $k=>$inward){
        	$excelData[$k]['sample_id'] = $inward['sample_id'];
        	$excelData[$k]['patient_name'] = $inward['name'];
        	$excelData[$k]['age'] = $inward['age'];
        	$excelData[$k]['gender'] = $inward['sex'];
        	$excelData[$k]['date_of_sample_testing'] = $inward['testing_at'];
        	$excelData[$k]['status'] = $inward['status'];
        }
	    return Excel::download(new ReportExport($excelData), 'report.xlsx');
	}

	public function stateReports(Request $request)
    {
        abort_if(Gate::denies('state_reports_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();
        //echo "<pre>"; print_r($formData); exit;
       	$condition = $inwardData = array();
       	$dateColumn = array('reported_at'=>array('Positive','Negative','Resample Required','Sample Spillage / Damaged','Inconclusive (Repeat Fresh Sample)'), 'testing_at'=>array('sent_for_testing'),'received_at'=>array('All','pending','rejected','On Hold'));
       	
       	if(!empty($formData)){
       		$curDateCol = 'received_at';

       		if($formData['facility_id'] && $formData['facility_id'] != 'All')
       			$condition[] = 'facility_id = '.$formData['facility_id'];

       		if($formData['status'] && $formData['status'] != 'All'){
       			$condition[] = 'status = "'.$formData['status'].'"';

       			foreach($dateColumn as $k=>$v){
       				if(in_array($formData['status'], $v)){
       					$curDateCol = $k;
       				}
       			}
       		}

       		if($formData['from_date'] && $formData['to_date']){
       			$from_date = Carbon::createFromFormat('d/m/Y', $formData['from_date']);
       			$formData['from_date'] = $from_date = date('Y-m-d', strtotime($from_date));
       			$to_date = Carbon::createFromFormat('d/m/Y', $formData['to_date']);
       			$formData['to_date'] = $to_date = date('Y-m-d', strtotime($to_date));
       			$condition[] = 'date(i.'.$curDateCol.') between "'.$from_date.'" and "'.$to_date.'"';
       		}

       		$condStr = '';
	       	if(!empty($condition)){
	       		$condStr = ' where '.implode(' and ',$condition);
	       	}
	       	//echo 'Select i.sample_id,f.name facility_name,s.name sample_type_name,i.name patient_name,i.contact_no,i.age,i.sex,i.address,i.status from inwards i inner join facilities f on i.facility_id = f.id inner join sample_types s on i.sample_type_id = s.id '.$condStr.' order by i.id desc'; exit;
	       	
	       	$inwardData = DB::select('Select i.id,i.sample_id,f.name facility_name,s.name sample_type_name,i.name patient_name,i.patient_id,i.contact_no,i.age,i.sex,i.address, i.reported_at,i.status from inwards i inner join facilities f on i.facility_id = f.id inner join sample_types s on i.sample_type_id = s.id '.$condStr.' and i.status not in ("pending","rejected","sent_for_testing") order by i.id asc');

	       	if(isset($formData['report_pdf']) && $formData['report_pdf']=='yes' && !empty($inwardData)){
				ini_set('memory_limit', -1);
				
	       		$mailedDate = date('YmdHis');
		        $mailedAt = date('Y-m-d H:i',strtotime($mailedDate));
		        $mailedAtYear = date('Y',strtotime($mailedDate));
		        $mailedAtMonth = date('m',strtotime($mailedDate));
		        $mailedAtDay = date('d',strtotime($mailedDate));
		        //echo "<pre>"; print_r($formData); exit;

		        $patientData = Inward::with('facility','sample_type')->whereIn('id',$formData['id'])->get();
		        //echo "<pre>"; print_r($patientData); exit;
		        $allArr['data'] = $patientData;
        		$allArr['from_date'] = $formData['from_date'];
        		$allArr['to_date'] = $formData['to_date'];
        		$allArr['mailed_at'] = $mailedAt;

        		$path = 'Reports/state/'.$mailedAtYear.'/'.$mailedAtMonth.'/'.$mailedAtDay;

	        	if(!is_dir(public_path($path))) {
	        		mkdir($path, 0777, true);	        		
	        	}

	        	$curFacilityName = 'All';
	        	if($formData['facility_id'] != 'All'){
	        		$facilityData = Facility::select('name')->where('id',$formData['facility_id'])->first();
	        		$curFacilityName = $facilityData->name;
	        	}
	        	
	        	$report = new StateReport;
		        $report->facility_name = $curFacilityName;
		        $report->from_date = $allArr['from_date'];
		        $report->to_date = $allArr['to_date'];
		        $report->status = $formData['status'];
		        $report->mailed_at = $mailedAt;
		        $report->name = 'state_report'.$mailedDate;
		        $report->file_path = $path;
		        $report->total_count = count($inwardData);
		        $report->save();

        		$pdf = \PDF::loadView('admin.reports.statepdfview', $allArr)->setPaper('a4', 'landscape');
        		$pdf->save(public_path($path).'/'.$report->name.'.pdf');
	        	return $pdf->download($report->name.'.pdf');
        	}
       	}       	

       	$reports = StateReport::all();
        $facilities = Facility::where('facility_type','facility')->get();
        $sampleStatus = SampleStatus::all();

        return view('admin.reports.state_reports', compact('facilities','inwardData','sampleStatus','formData', 'reports'));
    }

    public function downloadStateReportPdf($report_id){
		ini_set('memory_limit', -1);

    	abort_if(Gate::denies('state_report_download_pdf'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$report = StateReport::where('id',$report_id)->first();


    	$filepath = public_path($report->file_path.'/'.$report->name.'.pdf');
    	//echo "<pre>"; print_r($filepath); exit;
    	$headers = ['Content-Type: application/pdf'];
    	$newName = $report->name.'.pdf';


    	return response()->download($filepath, $newName, $headers);
    }

    public function stateMailReview($report_id){
    	abort_if(Gate::denies('state_report_send_email'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$report = StateReport::where('id',$report_id)->first();

    	$stateFacility = Facility::where('facility_type','state')->first();

    	$returnHTML = view('admin.reports.mail_review', compact('report','stateFacility'))->render();
		return response()->json(array('status' => 'success', 'html'=>$returnHTML));
    }

    public function sendReportEmail(Request $request){
		ini_set('memory_limit', -1);

    	abort_if(Gate::denies('state_report_send_email'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$formData = $request->all();

    	if(!empty($formData['report_id']) && !empty($formData['emails'])){
	    	$report = StateReport::where('id',$formData['report_id'])->first();
	    	$emails = FacilityEmails::whereIn('id',$formData['emails'])->get()->toArray();

	    	$emailIds = array_column($emails, 'email');
			
			Mail::send('admin.reports.mail', [], function($message) use($report, $emailIds) {
				$message->to($emailIds)->subject
				('RPCR Report of covid 19');
				$message->attach($report->file_path.'/'.$report->name.'.pdf');
			   
				$message->from('rgmcmicrocovid19@gmail.com','Rgmc Micro');
			});
		}
      	
      	$request->session()->flash('alert-success', 'Report sent to state government successfully!');
      	return redirect()->route('admin.reports.stateReports');
    }

    public function individualPdf(Request $request,$patient_id){
    	$formData = $request->all();
    	$remarks = $formData['remarks'];
    	$inward = Inward::with('facility','sample_type')->where('id',$patient_id)->first();

    	if($remarks){
	    	$remarkData['inward_id'] = $patient_id;
	    	$remarkData['remark'] = $remarks;
			Remark::create($remarkData);
		}

    	if(!empty($inward)){
    		//$data = $inward;
    		//return view('admin.reports.individual_pdfview', compact('data'));
	    	$pdf = \PDF::loadView('admin.reports.individual_pdfview', array('data'=>$inward, 'remarks' => $remarks));
	    	$reportName = str_replace(' ', '_', $inward->name).'_report';
			//$pdf->save(public_path($path).'/'.$reportName.'.pdf');
	    	return $pdf->download($reportName.'.pdf');
	    }

	    return redirect()->route('admin.inwards.index');
    }
	
	//D-line Report
    public function dlineReports(Request $request)
    {
        abort_if(Gate::denies('dline_reports_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();
        //echo "<pre>"; print_r($formData); exit;
       	$inwardData = array();
       	
       	if(!empty($formData)){       		
			$from_date = Carbon::createFromFormat('d/m/Y', $formData['from_date']);
   			$formData['from_date'] = $from_date = date('Y-m-d', strtotime($from_date));
   			$to_date = Carbon::createFromFormat('d/m/Y', $formData['to_date']);
   			$formData['to_date'] = $to_date = date('Y-m-d', strtotime($to_date));

	       	//echo 'Select i.sample_id,f.name facility_name,s.name sample_type_name,i.name patient_name,i.contact_no,i.age,i.sex,i.address,i.status from inwards i inner join facilities f on i.facility_id = f.id inner join sample_types s on i.sample_type_id = s.id '.$condStr.' order by i.id desc'; exit;
	       	
	       	$inwardData = DB::select('Select i.id,i.sample_id,f.name facility_name,s.name sample_type_name,i.name patient_name,i.patient_id,i.contact_no,i.age,i.sex,i.address, i.collected_at,i.tested_at,i.status from inwards i inner join facilities f on i.facility_id = f.id inner join sample_types s on i.sample_type_id = s.id and i.status = "Positive" and date(i.tested_at) between "'.$from_date.'" and "'.$to_date.'" order by i.id asc');

	       	if(isset($formData['report_pdf']) && $formData['report_pdf']=='yes' && !empty($inwardData)){
	       		$mailedDate = date('YmdHis');
		        $mailedAt = date('Y-m-d H:i',strtotime($mailedDate));
		        $mailedAtYear = date('Y',strtotime($mailedDate));
		        $mailedAtMonth = date('m',strtotime($mailedDate));
		        $mailedAtDay = date('d',strtotime($mailedDate));
		        //echo "<pre>"; print_r($formData); exit;
		        $selectedPatientsIds = array_column($formData['data'], 'id');
	        	$selectedPatients = array_combine($selectedPatientsIds, $formData['data']);

		        $patientData = Inward::with('facility','sample_type')
		        				->whereIn('id', $selectedPatientsIds)->get();
		        
		        foreach($patientData as $k=>$v){
		        	$patientData[$k]->remarks = isset($selectedPatients[$v->id])?$selectedPatients[$v->id]['remarks']:'';
		        }

		        $allArr['data'] = $patientData;
        		$allArr['from_date'] = $formData['from_date'];
        		$allArr['to_date'] = $formData['to_date'];
        		$allArr['mailed_at'] = $mailedAt;

        		$path = 'Reports/dline/'.$mailedAtYear.'/'.$mailedAtMonth.'/'.$mailedAtDay;

	        	if(!is_dir(public_path($path))) {
	        		mkdir($path, 0777, true);	        		
	        	}
	        	
	        	$report = new DlineReport;
		        //$report->facility_name = $curFacilityName;
		        $report->from_date = $allArr['from_date'];
		        $report->to_date = $allArr['to_date'];
		        $report->mailed_at = $mailedAt;
		        $report->name = 'dline_report'.$mailedDate;
		        $report->file_path = $path;
		        $report->total_count = count($inwardData);
		        $report->save();

        		$pdf = \PDF::loadView('admin.reports.dlinepdfview', $allArr)->setPaper('a4', 'landscape');
        		$pdf->save(public_path($path).'/'.$report->name.'.pdf');
	        	return $pdf->download($report->name.'.pdf');
        	}
       	}       	

       	$reports = DlineReport::all();

        return view('admin.reports.dline_reports', compact('inwardData','formData', 'reports'));
    }

    public function downloadDlineReportPdf($report_id){
    	abort_if(Gate::denies('dline_report_download_pdf'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$report = DlineReport::where('id',$report_id)->first();


    	$filepath = public_path($report->file_path.'/'.$report->name.'.pdf');
    	//echo "<pre>"; print_r($filepath); exit;
    	$headers = ['Content-Type: application/pdf'];
    	$newName = $report->name.'.pdf';


    	return response()->download($filepath, $newName, $headers);
    }

    public function dlineMailReview($report_id){
    	abort_if(Gate::denies('dline_report_send_email'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$report = DlineReport::where('id',$report_id)->first();

    	$stateFacility = Facility::where('facility_type','state')->first();

    	$returnHTML = view('admin.reports.dline_mail_review', compact('report','stateFacility'))->render();
		return response()->json(array('status' => 'success', 'html'=>$returnHTML));
    }

    public function sendDlineReportEmail(Request $request){
    	ini_set('memory_limit', -1);

    	abort_if(Gate::denies('dline_report_send_email'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$formData = $request->all();

    	if(!empty($formData['report_id']) && !empty($formData['emails'])){
	    	$report = StateReport::where('id',$formData['report_id'])->first();
	    	$emails = FacilityEmails::whereIn('id',$formData['emails'])->get()->toArray();

	    	$emailIds = array_column($emails, 'email');
			
			Mail::send('admin.reports.mail', [], function($message) use($report, $emailIds) {
				$reportDate = date('d F', strtotime($report->to_date));
				$message->to($emailIds)->subject
				('Format D-Line list of Confirmed COVID-19 cases '.$reportDate);
				$message->attach($report->file_path.'/'.$report->name.'.pdf');
			   
				$message->from('rgmcmicrocovid19@gmail.com','Rgmc Micro');
			});
		}
      	
      	$request->session()->flash('alert-success', 'Report sent to state government successfully!');
      	return redirect()->route('admin.reports.stateReports');
    }
}