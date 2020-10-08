<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFacilityRequest;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Facility;
use App\FacilityEmails;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FacilitiesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('facility_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $facilities = Facility::where('facility_type','facility')->get();

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        abort_if(Gate::denies('facility_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.facilities.create');
    }

    public function store(StoreFacilityRequest $request)
    {
    	$formData = $request->all();
		$formData['facility_type'] = 'facility';
		
        $facility = Facility::create($formData);
        //$facility->facility_emails()->sync($request->input('email', []));
        foreach ($request->email as $mail) {
            $facility->facility_emails()->save(new FacilityEmails(["email" => $mail]));
        }

        return redirect()->route('admin.facilities.index');
    }

    public function edit(Facility $facility)
    {
        abort_if(Gate::denies('facility_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $facility->load('facility_emails');

        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        $facility->update($request->all());

        $facility_emails = Facility::with('facility_emails')->find($facility->id);
        
        $facility->facility_emails()->forceDelete();
        
        if ($request->email) {
            $emails = array_unique($request->email);
            
            foreach ($emails as $email) {   
                $facility->facility_emails()->create([
                    'email' => $email
                ]); 
            }
        }

        if($facility_emails->facility_type == 'state')
        	return redirect()->route('admin.facilities.stateEmail');
        else
        	return redirect()->route('admin.facilities.index');
    }

    public function show(Facility $facility)
    {
        abort_if(Gate::denies('facility_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $facility->load('facility_emails');

        return view('admin.facilities.show', compact('facility'));
    }

    public function destroy(Facility $facility)
    {
        abort_if(Gate::denies('facility_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $facility->delete();

        return back();
    }

    public function massDestroy(MassDestroyFacilityRequest $request)
    {
        Facility::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function stateEmail()
    {
        abort_if(Gate::denies('facility_state_government_email'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $facility = Facility::where('facility_type','state')->first();

        return view('admin.facilities.state_email',compact('facility'));
    }
}