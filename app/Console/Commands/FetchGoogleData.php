<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;

class FetchGoogleData extends Command
{
    protected $signature = 'google:fetch {count?}';
    protected $description = 'Google Sheets-dan ma\'lumotlarni olish va konsolda chiqarish';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(GoogleSheetsService $googleSheetsService)
    {
        $count = $this->argument('count');
        $data = $googleSheetsService->fetchFromGoogleSheets($count);

        if (empty($data)) {
            $this->info('Ma\'lumot topilmadi.');
            return;
        }

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        foreach ($data as $row) {
            $this->line("ID: {$row['id']} | Name: {$row['name']} | Status: {$row['status']} | Comment: {$row['comment']}");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Google Sheets-dan ma\'lumot olish yakunlandi.');
    }
}
