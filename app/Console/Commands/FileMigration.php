<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FileMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will add data to the new files table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('new_Files')->truncate();
        DB::table('new_Media')->truncate();
        DB::table('new_MediaCarouselItem')->truncate();

        $companies = Db::table('new_Companies')->get();

        foreach ($companies as $i => $company) {
            DB::table('new_Files')->insert([
                'Id' => $i + 1,
                'Name' => 'Fontys-Logo.svg.png',
                'Path' => 'https://www.foodtechbrainport.com/wp-content/uploads/2021/08',
                'UploadDateTime' => now(),
            ]);

            DB::table('new_Media')->insert([
                'Id' => $i + 1,
                'FileId' => $i + 1,
                'EmbeddedMedia' => null,
            ]);

            DB::table('new_MediaCarouselItem')->insert([
                'MediaId' => $i + 1,
                'Position' => 0,
                'CompanyId' => $company->Id,
            ]);
        }
    }
}
