<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Response\BaseController;
use App\Http\Requests\ScholarshipRequest;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends BaseController
{
    public function index(Request $request)
    {
        $query = Scholarship::query();
        
        // Apply ordering
        $sortBy = $request->get('sort', 'post_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sortBy, $order);
        
        // Apply limit if specified
        if ($request->has('limit')) {
            $limit = min($request->get('limit'), 100); // Cap at 100 to prevent abuse
            $scholarships = $query->take($limit)->get();
        } else {
            $scholarships = $query->get();
        }
        
        return $this->sendSuccess($scholarships, "fetch scholarship list");
    }

    /**
     * Display the specified scholarship.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $scholarship = Scholarship::find($id);
        
        if (!$scholarship) {
            return $this->sendError("Scholarship not found", [], 404);
        }
        
        return $this->sendSuccess($scholarship, "Scholarship details fetched successfully");
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
        
        // First, try to get scholarships with actual upcoming deadlines
        $scholarships = Scholarship::whereNotNull('deadline')
            ->where('deadline', '>', $currentDate)
            ->orderBy('deadline', 'asc')
            ->get();
        
        if ($scholarships->isNotEmpty()) {
            return $this->sendSuccess($scholarships, "Fetched scholarships with upcoming deadlines");
        }
        
        // If no scholarships with valid upcoming deadlines, return recent ones
        $recentScholarships = Scholarship::orderBy('post_at', 'desc')
            ->take(10)
            ->get();
            
        return $this->sendSuccess($recentScholarships, "No upcoming deadlines found. Showing recent scholarships");
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

    public function filterByDegree($degree)
    {
        $scholarships = Scholarship::where('degree_offered', 'LIKE', '%' . $degree . '%')
            ->orderBy('post_at', 'desc')
            ->get();

        if ($scholarships->isEmpty()) {
            return $this->sendError("No scholarships found for the specified degree level.");
        }

        return $this->sendSuccess($scholarships, "Fetched {$degree} scholarships successfully");
    }

    public function filterByRegion($region)
    {
        $regionCountries = [
            'Europe' => ['United Kingdom', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 
                        'Sweden', 'Switzerland', 'Denmark', 'Norway', 'Finland', 'Ireland', 
                        'Belgium', 'Austria', 'Poland', 'Portugal', 'Greece'],
            'Asia' => ['China', 'Japan', 'South Korea', 'Singapore', 'Malaysia', 'Thailand', 
                      'India', 'Indonesia', 'Vietnam', 'Philippines', 'Taiwan'],
            'North America' => ['United States', 'Canada', 'Mexico'],
            'Oceania' => ['Australia', 'New Zealand'],
            'Africa' => ['South Africa', 'Egypt', 'Morocco', 'Kenya', 'Nigeria', 'Ghana'],
            'South America' => ['Brazil', 'Argentina', 'Chile', 'Colombia', 'Peru']
        ];

        if (!isset($regionCountries[$region])) {
            return $this->sendError("Invalid region specified. Available regions: Europe, Asia, North America, Oceania, Africa, South America");
        }

        $scholarships = Scholarship::where(function ($query) use ($regionCountries, $region) {
            foreach ($regionCountries[$region] as $country) {
                $query->orWhere('host_country', 'LIKE', '%' . $country . '%');
            }
        })
        ->orderBy('post_at', 'desc')
        ->get();

        if ($scholarships->isEmpty()) {
            return $this->sendError("No scholarships found for the specified region.");
        }

        return $this->sendSuccess($scholarships, "Fetched scholarships from {$region} region successfully");
    }

    public function filterByCountry($country)
    {
        // Convert country parameter to handle common cases and format variations
        $countryMapping = [
            'usa' => ['United States', 'USA', 'U.S.A', 'U.S', 'America'],
            'uk' => ['United Kingdom', 'UK', 'U.K', 'Britain', 'Great Britain'],
            // Add more mappings as needed
        ];
        
        // Build the query
        $query = Scholarship::query();
        
        if (isset($countryMapping[strtolower($country)])) {
            // If it's a mapped country code/alias, search for all variations
            $query->where(function($q) use ($countryMapping, $country) {
                foreach ($countryMapping[strtolower($country)] as $countryName) {
                    $q->orWhere('host_country', 'LIKE', '%' . $countryName . '%');
                }
            });
        } else {
            // Otherwise, just search directly
            $query->where('host_country', 'LIKE', '%' . $country . '%');
        }
        
        $scholarships = $query->orderBy('post_at', 'desc')->get();
        
        if ($scholarships->isEmpty()) {
            return $this->sendError("No scholarships found for {$country}.");
        }
        
        return $this->sendSuccess($scholarships, "Fetched scholarships for {$country} successfully");
    }

    public function search(Request $request)
    {
        $query = Scholarship::query();

        if ($request->has('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if ($request->has('degree')) {
            $query->where('degree_offered', 'LIKE', '%' . $request->degree . '%');
        }

        if ($request->has('country')) {
            $query->where('host_country', 'LIKE', '%' . $request->country . '%');
        }

        if ($request->has('university')) {
            $query->where('host_university', 'LIKE', '%' . $request->university . '%');
        }

        $scholarships = $query->orderBy('post_at', 'desc')->get();

        if ($scholarships->isEmpty()) {
            return $this->sendError("No scholarships found matching your criteria.");
        }

        return $this->sendSuccess($scholarships, "Fetched filtered scholarships successfully");
    }

    public function searchByTitle(Request $request)
    {
        $title = $request->input('title');
        
        if (!$title) {
            return $this->sendError("Title parameter is required for search.");
        }

        $scholarships = Scholarship::where('title', 'LIKE', '%' . $title . '%')
            ->orderBy('post_at', 'desc')
            ->get();

        if ($scholarships->isEmpty()) {
            return $this->sendError("No scholarships found with the specified title.");
        }

        return $this->sendSuccess($scholarships, "Fetched scholarships matching title '{$title}' successfully");
    }
}
