<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use App\Models\Record;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $this->service = new Google_Service_Sheets($this->client);
    }

    protected function getSpreadsheetId()
    {
        if (!$this->spreadsheetId) {
            $this->spreadsheetId = DB::table('settings')
                ->where('key', 'google_sheet_url')
                ->value('value');

            if ($this->spreadsheetId) {
                $this->setSpreadsheetIdFromUrl($this->spreadsheetId);
            } else {
                $this->spreadsheetId = config('google.spreadsheet_id') ?? null;
            }
        }

        return $this->spreadsheetId;
    }

    public function syncToGoogleSheets()
    {
        $spreadsheetId = $this->getSpreadsheetId();
        if (!$spreadsheetId) {
            Log::warning('No Google Sheet URL.');
            return;
        }

        $records = Record::allowed()->get();
        $values = [['ID', 'Name', 'Status', 'Comments']]; 

        $existingData = $this->service->spreadsheets_values->get($spreadsheetId, 'Sheet1!A2:D')->getValues() ?? [];
        $existingComments = [];
        foreach ($existingData as $row) {
            if (isset($row[0]) && isset($row[3])) {
                $existingComments[$row[0]] = $row[3];
            }
        }

        foreach ($records as $record) {
            $comment = $existingComments[$record->id] ?? '';
            $values[] = [$record->id, $record->name, $record->status, $comment];
        }

        $range = 'Sheet1!A1:D' . count($values);
        $body = new \Google_Service_Sheets_ValueRange(['values' => $values]);

        $this->service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );

        Log::info(' Google Sheets');
    }

    public function fetchFromGoogleSheets($count = null)
    {
        $spreadsheetId = $this->getSpreadsheetId();
        if (!$spreadsheetId) {
            return ['No Google Sheet URL set.'];
        }

        $range = 'Sheet1!A2:D';
        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues() ?? [];

        $output = [];
        $limit = $count ? min($count, count($values)) : count($values);

        for ($i = 0; $i < $limit; $i++) {
            $row = $values[$i];
            $id = $row[0] ?? 'N/A';
            $comment = $row[3] ?? 'No comment';
            $output[] = "ID: $id | Comment: $comment";
        }

        return $output;
    }

    public function setSpreadsheetIdFromUrl($url)
    {
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);
        $this->spreadsheetId = $matches[1] ?? null;

        if ($this->spreadsheetId) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'google_sheet_url'],
                ['value' => $url, 'updated_at' => now()]
            );
        }
    }

    public function clearGoogleSheet()
    {
        $spreadsheetId = $this->getSpreadsheetId();
        if (!$spreadsheetId) {
            Log::warning('No Google Sheet URL set.');
            return;
        }

  
        $range = 'Sheet1!A2:D';
        $this->service->spreadsheets_values->clear(
            $spreadsheetId,
            $range,
            new \Google_Service_Sheets_ClearValuesRequest()
        );

        Log::info('Google Sheet cleared.');
    }
}