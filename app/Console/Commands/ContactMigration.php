<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContactMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the contacts table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldContacts = DB::table('Contacts')->get();

        DB::table('new_Contacts')->truncate();

        foreach ($oldContacts as $oldContact) {
            $company = null;

            if($oldContact->CompanyId) {
                $company = DB::table('new_Companies')->where('old_id', $oldContact->CompanyId)->first();
            }

            DB::table('new_Contacts')->insert([
                'Name' => $oldContact->Name,
                'Email' => $oldContact->Email,
                'Phone' => $oldContact->PhoneNumber,
                'CompanyId' => $company?->Id,
                'old_id' => $oldContact->Id
            ]);
        }
    }
}
