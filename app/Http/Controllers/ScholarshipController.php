<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Response\BaseController;
use App\Http\Requests\ScholarshipRequest;
use App\Models\Scholarship;

class ScholarshipController extends BaseController
{
    public function index()
    {
        $scholarships = Scholarship::orderByDesc('post_at')->get();
        return $this->sendSuccess($scholarships, "fetch scolarship list");
    }
    public function store(ScholarshipRequest $request)
    {

        $validated = $request->validated();
        $scholarship = Scholarship::create($validated);
        return $this->sendSuccess([$scholarship], "create scholarship successful");
    }
    public function upcomingScholarships()
    {
        $currentDate = now();
        $scholarships = Scholarship::where('deadline', '>', $currentDate)->orderBy('deadline')->get();
        return $this->sendSuccess($scholarships, "Fetched scholarships with upcoming deadlines");
    }
    public function updateScholarship(ScholarshipRequest $request, Scholarship $scholarship)
    {
        $validated = $request->validated();
        $scholarship->update($validated);
        return $this->sendSuccess([$scholarship], "update scholarship successful");
    }
    
    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return $this->sendSuccess([], "remove scholarship successful");
    }
    public function showByCountry($country_id)
    {
        $scholarships = Scholarship::where('country_id', $country_id)->get();
        
        if ($scholarships->isEmpty()) {
            return $this->sendError("No scholarships found for the specified country.");
        }

        return $this->sendSuccess($scholarships, "Fetched scholarships for the selected country.");
    }
}
