<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;
use App\Models\Record;

class RecordController extends Controller
{
    public function syncToGoogleSheets(GoogleSheetsService $googleSheetsService)
    {
        $googleSheetsService->syncToGoogleSheets();
        return back()->with('success', 'Ma\'lumotlar Google Sheets-ga yuklandi!');
    }

    public function fetchFromGoogleSheets(GoogleSheetsService $googleSheetsService, $count = null)
    {
        $data = $googleSheetsService->fetchFromGoogleSheets($count);
        return response()->json($data);
    }

    public function generate()
    {
        Record::factory()->count(1000)->create([
            'status' => function () {
                return ['Allowed', 'Prohibited'][array_rand(['Allowed', 'Prohibited'])];
            }
        ]);
        return redirect()->back()->with('success', '1000 ta yozuv yaratildi');
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
