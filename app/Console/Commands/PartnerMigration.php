<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PartnerMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:partners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the partners table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldPartners= DB::table('Partners')->get();

        DB::table('new_Partners')->truncate();

        foreach ($oldPartners as $oldPartner) {
            $company = DB::table('new_Companies')->where('old_id', $oldPartner->PartnerId)->first();
            $user = DB::table('new_Users')->where('old_id', $oldPartner->UserId)->first();

            if($company && $user) {
                DB::table('new_Partners')->insert([
                    'PartnerId' => $company->Id,
                    'UserId' => $user?->Id,
                    'CreatedAt' => $oldPartner->CreatedAt,
                    'UpdatedAt' => $oldPartner->UpdatedAt,
                ]);
            }
        }
    }
}
