<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LanguageMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will add data to the new languages table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('new_Language')->truncate();

        DB::table('new_Language')->insert([
            'Id' => 1,
            'Name' => 'English',
            'LanguageCode' => 'gb',
        ]);

        DB::table('new_Language')->insert([
            'Id' => 2,
            'Name' => 'Dutch',
            'LanguageCode' => 'nl',
        ]);
    }
}
