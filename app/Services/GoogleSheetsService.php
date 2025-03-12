<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use App\Models\Record;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
   
        $this->spreadsheetId = config('services.google.spreadsheet_id');
     
        if (!$this->spreadsheetId) {
            throw new \Exception('Google Sheets ID aniqlanmadi. Iltimos, .env faylini tekshiring.');
        }

        $this->client = new Client();
        $credentialsPath = storage_path('app/credentials.json');

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Google API uchun credentials.json topilmadi: $credentialsPath");
        }

        $this->client->setAuthConfig($credentialsPath);
        $this->client->setScopes([Sheets::SPREADSHEETS]);

        $this->service = new Sheets($this->client);
    }

 
    public function syncToGoogleSheets()
    {
    
        $records = Record::allowed()->get();

      
        $values = [['ID', 'Name', 'Status']]; 
        foreach ($records as $record) {
            $values[] = [$record->id, $record->name, $record->status];
        }


        $body = new Sheets\ValueRange(['values' => $values]);
        $params = ['valueInputOption' => 'RAW'];

        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            'Sheet1!A1',
            $body,
            $params
        );
    }


    public function fetchFromGoogleSheets($count = null)
    {
        $range = 'Sheet1!A2:D'; 
        // dd($range);
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        // dd("kumush");
        $values = $response->getValues();
        // dd($values);
        if (empty($values)) {
            return [];
        }

        $data = [];
        foreach ($values as $index => $row) {
            if ($count !== null && $index >= $count) break;
            $data[] = [
                'id' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'status' => $row[2] ?? null,
                'comment' => $row[3] ?? null, 
            ];
        }

        return $data;
    }
}
