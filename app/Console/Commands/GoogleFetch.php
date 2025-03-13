<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;

class GoogleFetch extends Command
{
    protected $signature = 'google:fetch {count?}';
    protected $description = 'Fetch data from Google Sheets';

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->googleSheetsService = $googleSheetsService;
    }

    public function handle()
    {
        $count = $this->argument('count') ?? 0;
        $values = $this->googleSheetsService->fetchFromGoogleSheets();
        $total = count($values);
        $limit = $count > 0 ? min($count, $total) : $total;

        $bar = $this->output->createProgressBar($limit);
        $bar->start();

        foreach (array_slice($values, 0, $limit) as $row) {
            $id = $row[0] ?? 'N/A';
            $comment = $row[3] ?? 'No comment';
            $this->line("ID: $id | Comment: $comment");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}