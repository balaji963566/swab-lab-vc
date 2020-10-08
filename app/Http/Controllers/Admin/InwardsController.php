<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Inward;
use App\Facility;
use App\SampleType;
use App\SampleStatus;
use App\TestType;
use App\SampleRejectedReason;
use App\Imports\InwardsImport;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Config;
use Carbon\Carbon;
use DB;
use Excel;

class InwardsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('inwards_all_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $formData = $request->all();
       	$condition = array();
		$curDateCol = 'received_at';
       	$dateColumn = array('reported_at'=>array('Positive','Negative','Resample Required','Sample Spillage / Damaged','Inconclusive (Repeat Fresh Sample)'), 'testing_at'=>array('sent_for_testing'),'received_at'=>array('All','pending','rejected','On Hold'));
       	
       	if(!empty($formData)){
       		if($formData['facility_id'])
       			$condition[] = 'facility_id = '.$formData['facility_id'];

       		if($formData['status'] && $formData['status']!='All'){
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
       		else if($formData['from_date'] && !$formData['to_date']){
       			$from_date = Carbon::createFromFormat('d/m/Y', $formData['from_date']);
       			$formData['from_date'] = $from_date = date('Y-m-d', strtotime($from_date));
       			$condition[] = 'date(i.'.$curDateCol.') >= '.$from_date;
       		}
       		else if(!$formData['from_date'] && $formData['to_date']){
       			$to_date = Carbon::createFromFormat('d/m/Y', $formData['to_date']);
       			$formData['to_date'] = $to_date = date('Y-m-d', strtotime($to_date));
       			$condition[] = 'date(i.'.$curDateCol.') <= '.$to_date;
       		}
       	}
		
		if(empty($from_date) && empty($to_date)){
			$from_date = $to_date = date('Y-m-d');
			$condition[] = 'date(i.'.$curDateCol.') between "'.$from_date.'" and "'.$to_date.'"';
		}

       	$condStr = '';
       	if(!empty($condition)){
       		$condStr = ' where '.implode(' and ',$condition);
       	}
       	//echo 'Select i.sample_id,f.name facility_name,s.name sample_type_name,i.name patient_name,i.contact_no,i.age,i.sex,i.address,i.status from inwards i inner join facilities f on i.facility_id = f.id inner join sample_types s on i.sample_type_id = s.id '.$condStr.' order by i.id desc'; exit;
       	$inwardData = DB::select('Select i.id,i.sample_id,f.name facility_name,i.reported_at,s.name sample_type_name,i.name patient_name,i.contact_no,i.age,i.sex,i.address,i.received_at,i.tested_at,i.status from inwards i inner join facilities f on i.facility_id = f.id inner join sample_types s on i.sample_type_id = s.id '.$condStr.' order by i.id desc');

        $facilities = Facility::where('facility_type','facility')->get();
        $sampleStatus = SampleStatus::all();

        return view('admin.inwards.index', compact('facilities','inwardData','sampleStatus','formData'));
    }

    public function create()
    {
        abort_if(Gate::denies('inwards_add_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $facilities = Facility::where('facility_type','facility')->get();
        $sampleTypes = SampleType::all();
        $testTypes = TestType::all();
        $lastInwards = Inward::where('received_at', Inward::max('received_at'))->orderBy('id', 'asc')->get()->toArray();
        $fromSampleId = $toSampleId = 0;

        if(!empty($lastInwards)){
	        $fromSampleId = $lastInwards[0]['sample_id'];
	        $toSampleId = $lastInwards[(count($lastInwards) - 1)]['sample_id'];
	    }

        return view('admin.inwards.create', compact('facilities','sampleTypes', 'fromSampleId', 'toSampleId'));
    }

    public function checkSampleIds(Request $request){
    	$formData = $request->all();
    	$skippedSample = '';

        if (isset($formData['check']) && $formData['check'] == 'onlyDuplicate') {
            $sampleIds[] = $formData['sample_id_prefix'] . $formData['sample_id'];
        } else {
            parse_str($formData['data'], $postData);

            $sampleIds = array_combine(array_column($postData['detail'], 'sample_id_prefix'), array_column($postData['detail'], 'sample_id'));
            //dd($postData);
            /* for ($i = 1, $n = count($sampleIds); $i < $n; $i++) {
              $curSample = filter_var($sampleIds[$i], FILTER_SANITIZE_NUMBER_INT);
              $prevSample = filter_var($sampleIds[$i-1], FILTER_SANITIZE_NUMBER_INT);
              $diffs = $curSample - $prevSample;

			    if($diffs != 1){
			    	$skippedSample = $sampleIds[$i-1];
			    	break;
			    }
			}*/
		}

        $prefixedSamplesArr = [];
//                dd($sampleIds);
        foreach ($sampleIds as $key => $id) {
            $prefixedSamplesArr[] = $key . $id;
        }

    	$inwardData = Inward::with('facility')->whereIn('sample_id',$prefixedSamplesArr)->get()->toArray();
    	if(empty($skippedSample) && empty($inwardData)){
    		return response()->json(array('status'=>'success'));
    	}

    	$returnHTML = view('admin.inwards.check_sample_ids', compact('inwardData','skippedSample'))->render();
		return response()->json(array('status' => 'fail', 'html'=>$returnHTML));
    }

    public function store(Request $request)
    {
    	abort_if(Gate::denies('inwards_add_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	//$default_sample_id = Config::get('params.sample.default_id');
    	//$default_sample_prefix = Config::get('params.sample.prefix');

    	//$maxSampleId = Inward::max('sample_id');
    	$lastId = Inward::max('id');
    	$lastId = $lastId ? $lastId : 1;
    	//$sample_id = (!$maxSampleId)? $default_sample_id:($maxSampleId+1);

    	$formData = $request->all();
    	//echo "<pre>"; print_r($formData); exit;
    	if(!empty($formData)){
	    	//$receivedAt = date('Y-m-d H:i:s', strtotime($formData['received_at']));
	    	$receivedAt = Carbon::createFromFormat('d/m/Y H:i a', $formData['received_at']);
	    	$collectedAt = Carbon::createFromFormat('d/m/Y', $formData['collected_at']);

	    	$postData = array();
	    	foreach($formData['detail'] as $k=>$data){
	    		$postData[$k]['facility_id'] = $formData['facility_id'];
	    		$postData[$k]['sample_id'] = $data['sample_id_prefix'].$data['sample_id'];
	    		$postData[$k]['name'] = $data['name'];
	    		$postData[$k]['sample_type_id'] = $data['sample_type_id'];
    			$postData[$k]['patient_id'] = ($data['patient_id'])?$data['patient_id']:'';
    			$postData[$k]['contact_no'] = ($data['contact_no'])?$data['contact_no']:'';	
    			$postData[$k]['age'] = ($data['age'])?$data['age']:null;
    			$postData[$k]['sex'] = ($data['sex'])?$data['sex']:null;
    			$postData[$k]['address'] = ($data['address'])?$data['address']:'';
    			$postData[$k]['received_at'] = $receivedAt;
    			$postData[$k]['collected_at'] = $collectedAt;
	    		$postData[$k]['status'] = 'pending';
	    		
	    		//$sample_id++;
	    	}

	    	//echo "<pre>"; print_r($postData); exit;
	    	
	    	if(Inward::insert($postData)){
	    		$request->session()->flash('alert-success', 'Patient details added successfully!');
	    		$latestId = Inward::max('id');

	    		return redirect()->route('admin.inwards.review', ['last_id'=>$lastId, 'latest_id'=>$latestId]);
	    	}
	    	else{
	    		$request->session()->flash('alert-danger', 'Some error has occured!');
	    		return redirect()->route('admin.inwards.create');
	    	}
	    }
    }

    public function review($lastId, $latestId)
    {
    	$data = array();
    	$fromId = ($lastId == 1)? $lastId : $lastId + 1;
        $data['newInwards'] = Inward::with('facility','sample_type')->whereBetween('id', [$fromId, $latestId])->where('status','pending')->get();

        if(!empty($data['newInwards'])){
        	$data['facilityId'] = $data['newInwards'][0]->facility->id;
        	$data['facility_name'] = $data['newInwards'][0]->facility->name;
        	$data['receivedAt'] = $data['newInwards'][0]->received_at;
        }

        $data['facilities'] = Facility::where('facility_type','facility')->get();
        $data['sampleTypes'] = SampleType::all();
        $data['rejectedReasons'] = SampleRejectedReason::all();
        
        return view('admin.inwards.review', compact('data'));
    }

    public function bulk_update(Request $request)
    {
    	$formData = $request->all();
    	
    	if(!empty($formData)){
	    	$receivedAt = Carbon::createFromFormat('d/m/Y H:i a', $formData['received_at']);

	    	$postData = array();
	    	foreach($formData['detail'] as $k=>$data){
	    		$inward = Inward::find($data['id']);			

	    		$inward->facility_id = $formData['facility_id'];
	    		$inward->name = $data['name'];
	    		$inward->patient_id = ($data['patient_id'])?$data['patient_id']:'';
	    		$inward->contact_no = ($data['contact_no'])?$data['contact_no']:'';
	    		$inward->sample_type_id = $data['sample_type_id'];
	    		$inward->age = ($data['age'])?$data['age']:null;
	    		$inward->sex = ($data['sex'])?$data['sex']:null;
	    		$inward->address = ($data['address'])?$data['address']:'';
	    		$inward->received_at = $receivedAt;

	    		if($data['rejected_reason_id'] != 0){
	    			$inward->rejected_reason_id = $data['rejected_reason_id'];
	    			$inward->status = 'rejected';
	    		}
	    		
	    		$inward->save();
	    	}
	    	
    		$request->session()->flash('alert-success', 'Patient details updated successfully!');
		 }

        return redirect()->route('admin.inwards.create');
    }

    public function pickSamples()
    {
        abort_if(Gate::denies('inwards_pick_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inwardData = Inward::with('facility','sample_type')->where('status','pending')->get();

        return view('admin.inwards.pick', compact('inwardData'));
    }

    public function pick_update(Request $request)
    {
    	abort_if(Gate::denies('inwards_pick_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$formData = $request->all();
    	
    	if(!empty($formData['id'])){
	    	$postData = array();
	    	$testingDate = Carbon::now();
	    	foreach($formData['id'] as $id){
	    		$inward = Inward::find($id);	    		
	    		$inward->testing_at = $testingDate;
	    		$inward->status = 'sent_for_testing';
	    		
	    		$inward->save();
	    	}
	    	
    		$request->session()->flash('alert-success', count($formData['id']).' samples sent for testing successfully!');
		 }

        return redirect()->route('admin.inwards.pick');
    }

    public function statusSamples()
    {
        abort_if(Gate::denies('inwards_sample_status'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inwardData = Inward::with('facility','sample_type')->whereIn('status',array('sent_for_testing','On Hold'))->get();
        $sampleStatus = SampleStatus::all();

        return view('admin.inwards.status', compact('inwardData','sampleStatus'));
    }

    public function status_update(Request $request)
    {
    	abort_if(Gate::denies('inwards_sample_status'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    	$formData = $request->all();
    	
    	if(!empty($formData['data'])){
	    	$postData = array();
	    	$testedDate = Carbon::now();
	    	foreach($formData['data'] as $val){
	    		$inward = Inward::find($val['id']);	    		
	    		$inward->tested_at = $testedDate;
	    		$inward->status = $val['status'];
	    		
	    		$inward->save();
	    	}
	    	
    		$request->session()->flash('alert-success', count($formData['data']).' samples updated successfully!');
	 	}

        return redirect()->route('admin.inwards.status');
    }

    public function bulkSample(Request $request) {
        abort_if(Gate::denies('inwards_bulk_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();
        $rejectedReasons = array();
        $exlData = array();
        //$file = $request->file('xlsfile');
        //echo "<pre>"; print_r($formData); exit;

        if (!empty($formData)) {
            $receivedAt = Carbon::createFromFormat('d/m/Y H:i a', $formData['received_at']);
            $formData['received_at'] = date('Y-m-d H:i', strtotime($receivedAt));
            $collectedAt = Carbon::createFromFormat('d/m/Y', $formData['collected_at']);
            $formData['collected_at'] = date('Y-m-d', strtotime($collectedAt));
            //dd($formData);
            if (isset($formData['detail']) && !empty($formData['detail'])) {
                $postData = array();
                foreach ($formData['detail'] as $k => $data) {
                    $postData[$k]['facility_id'] = $formData['facility_id'];
                    $postData[$k]['sample_id'] = $data['sample_id_prefix'] . $data['sample_id'];
                    $postData[$k]['name'] = $data['name'];
                    $postData[$k]['sample_type_id'] = $data['sample_type_id'];
                    $postData[$k]['patient_id'] = ($data['patient_id']) ? $data['patient_id'] : '';
                    $postData[$k]['contact_no'] = ($data['contact_no']) ? ((strlen($data['contact_no']) == 10) ? '0' . $data['contact_no'] : $data['contact_no']) : '';
                    $postData[$k]['age'] = ($data['age']) ? $data['age'] : null;
                    $postData[$k]['sex'] = ($data['sex']) ? $data['sex'] : null;
                    $postData[$k]['address'] = ($data['address']) ? $data['address'] : '';
                    $postData[$k]['rejected_reason_id'] = ($data['rejected_reason_id']) ? $data['rejected_reason_id'] : null;
                    $postData[$k]['received_at'] = $receivedAt;
                    $postData[$k]['collected_at'] = $collectedAt;
                    $postData[$k]['status'] = 'pending';

                    //$sample_id++;
                }
                //echo "<pre>"; print_r($postData); exit;

                if (!empty($postData)) {
                    if (Inward::insert($postData)) {
                        $request->session()->flash('alert-success', 'Bulk patient details added successfully!');

			    		return redirect()->route('admin.inwards.bulkSample');
			    	}
		    	}
    		}
    		else{
	    		$this->validate($request, [
		      		'xlsfile'  => 'required|mimes:xls,xlsx'
		     	]);
		     	$path = $request->file('xlsfile');
				$data = (new InwardsImport)->toArray($path);
				$excelData = $data[0];
				unset($excelData[0]);

	    		if(count($excelData) > 0){
		       		foreach($excelData as $row){
		       			if($row[0] && $row[1]){
							$sampleType = (empty($row[4]))?'Nasopharyngeal Swab':$row[4];
							
		        			$exlData[] = array(
			         			'sample_id'  => preg_replace('/[^0-9]/', '', $row[0]),
			         			'name'   => $row[1],
			         			'patient_id'   => $row[2],
			         			'contact_no'    => (strlen($row[3]) < 11)?$row[3]:$row[3],
			         			'sample_type_id'  => SampleType::select('id')->where('name',$sampleType)->value('id'),
			         			'age'   => $row[5],
			         			'sex' => $row[6],
			         			'address' => $row[7]
			        		);
		        		}
		       		}
		     	}
		     	//echo "<pre>"; print_r($exlData); exit;
		     	$rejectedReasons = SampleRejectedReason::all();
	     	}
    	}

        $facilities = Facility::where('facility_type','facility')->get();
        $sampleTypes = SampleType::all();
        $lastInwards = Inward::where('received_at', Inward::max('received_at'))->orderBy('id','asc')->get()->toArray();
        $fromSampleId = $toSampleId = 0;

        if(!empty($lastInwards)){
	        $fromSampleId = $lastInwards[0]['sample_id'];
	        $toSampleId = $lastInwards[(count($lastInwards) - 1)]['sample_id'];
	    }

        return view('admin.inwards.bulk_samples', compact('facilities','sampleTypes', 'fromSampleId', 'toSampleId','exlData','rejectedReasons','formData'));
    }

    public function editSample(Request $request){
        abort_if(Gate::denies('inwards_add_samples'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $formData = $request->all();
        
        if(!empty($formData['pid'])){
        	if(isset($formData['data']) && !empty($formData['data'])){
        		parse_str($formData['data'], $postData);

        		if(!empty($postData)){
        			$inward = Inward::find($formData['pid']);			
        			//echo "<pre>"; print_r($postData); exit;
		    		$inward->facility_id = $postData['data']['facility_id'];
		    		$inward->name = $postData['data']['name'];
		    		$inward->patient_id = ($postData['data']['patient_id'])?$postData['data']['patient_id']:'';
		    		$inward->contact_no = ($postData['data']['contact_no'])?$postData['data']['contact_no']:'';
		    		$inward->sample_type_id = $postData['data']['sample_type_id'];
		    		$inward->age = ($postData['data']['age'])?$postData['data']['age']:null;
		    		$inward->sex = ($postData['data']['sex'])?$postData['data']['sex']:null;
		    		$inward->address = ($postData['data']['address'])?$postData['data']['address']:'';

		    		if($postData['data']['rejected_reason_id'] != 0){
		    			$inward->rejected_reason_id = $postData['data']['rejected_reason_id'];
		    			$inward->status = 'rejected';
		    		}
		    		
		    		$inward->save();

		    		$data = Inward::with('facility','sample_type')->where('id',$formData['pid'])->first();
		    		return response()->json(array('status' => 'success','data' => $data));
        		}
        		return response()->json(array('status' => 'success'));
        	}
        	else{
        		$facilities = Facility::where('facility_type','facility')->get();
		        $sampleTypes = SampleType::all();
		        $rejectedReasons = SampleRejectedReason::all();

		        $inwardData = Inward::with('facility','sample_type','rejected_reason')->where('id',$formData['pid'])->first();
		    	
		    	$returnHTML = view('admin.inwards._edit_sample', compact('facilities','sampleTypes', 'rejectedReasons', 'inwardData'))->render();

				return response()->json(array('status' => 'success', 'for'=>'show', 'sample_id'=>$inwardData->sample_id, 'html'=>$returnHTML));
        	}
        }
    }
}