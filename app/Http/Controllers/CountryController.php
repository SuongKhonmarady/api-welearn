<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the countries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    /**
     * Store a newly created country in storage.
     *
     * @param  \App\Http\Requests\CountryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
        $validated = $request->validated();
        $country = Country::create($validated);

        return response()->json(['message' => 'Country created successfully.', 'country' => $country], 201);
    }

    /**
     * Display the specified country.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);
        return response()->json($country);
    }

    /**
     * Update the specified country in storage.
     *
     * @param  \App\Http\Requests\CountryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request, $id)
    {
        $validated = $request->validated();
        $country = Country::findOrFail($id);
        $country->update($validated);

        return response()->json(['message' => 'Country updated successfully.', 'country' => $country]);
    }

    /**
     * Remove the specified country from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return response()->json(['message' => 'Country deleted successfully.']);
    }

    /**
     * Display scholarships for the specified country.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function scholarships($id)
    {
        $country = Country::findOrFail($id);
        $scholarships = $country->scholarships; // Assuming the relationship is defined in the Country model
        return response()->json(['scholarships' => $scholarships]);
    }
}
