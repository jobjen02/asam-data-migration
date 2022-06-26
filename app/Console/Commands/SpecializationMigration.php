<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SpecializationMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:specializations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the specializations table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldSpecializations = DB::table('Specializations')->get();

        DB::table('new_Specializations')->truncate();

        foreach ($oldSpecializations as $oldSpecialization) {
            DB::table('new_Specializations')->insert([
                'Name' => $oldSpecialization->Name,
                'Active' => true,
                'Used' => true,
                'CreatedAt' => $oldSpecialization->CreatedAt,
                'UpdatedAt' => $oldSpecialization->UpdatedAt,
                'old_id' => $oldSpecialization->Id
            ]);
        }
    }
}
