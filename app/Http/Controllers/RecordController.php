<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;
use App\Models\Record;
use Illuminate\Support\Facades\Artisan;

class RecordController extends Controller
{
    protected $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    public function index()
    {
        $items = Record::all();
        return view('home', compact('items'));
    }

    public function setGoogleSheetUrl(Request $request)
    {
        $request->validate(['google_sheet_url' => 'required|url']);
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $request->google_sheet_url, $matches);
        $spreadsheetId = $matches[1] ?? null;

        if (!$spreadsheetId) {
            return back()->withErrors(['google_sheet_url' => 'Invalid Google Sheet URL']);
        }

        \DB::table('settings')->updateOrInsert(
            ['key' => 'google_spreadsheet_id'],
            ['value' => $spreadsheetId]
        );

        return back()->with('success', 'Google Sheet URL set successfully!');
    }

    public function generate()
    {
        Record::factory()->count(1000)->create([
            'status' => fn () => ['Allowed', 'Prohibited'][array_rand(['Allowed', 'Prohibited'])],
        ]);
        return back()->with('success', '1000 rows generated successfully!');
    }

    public function clear()
    {
        Record::truncate();
        $this->googleSheetsService->clearGoogleSheet();
        return back()->with('success', 'Table cleared successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Allowed,Prohibited',
        ]);

        Record::create($request->only('name', 'status'));
        return redirect()->route('home')->with('success', 'Item created successfully!');
    }

    public function edit($id)
    {
        $item = Record::findOrFail($id);
        return view('edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Allowed,Prohibited',
        ]);

        $item = Record::findOrFail($id);
        $item->update($request->only('name', 'status'));
        return redirect()->route('home')->with('success', 'Item updated successfully!');
    }

    public function destroy($id)
    {
        $item = Record::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Item deleted successfully!');
    }

    public function syncToGoogleSheets()
    {
        $this->googleSheetsService->syncToGoogleSheets();
        return back()->with('success', 'Data synced to Google Sheets!');
    }

    public function fetchFromGoogleSheets($count = null)
    {
        ob_start();
        Artisan::call('google:fetch', array_filter(['count' => $count]));
        $output = ob_get_clean();
        return '<pre>' . $output . '</pre>';
    }

}