<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CompanyMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the companies table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldCompanies = DB::table('Companies')->get();

        DB::table('new_Companies')->truncate();

        foreach ($oldCompanies as $oldCompany) {
            $address = Db::table('new_Address')->where('CompanyId', $oldCompany->Id)->first();


            DB::table('new_Companies')->insert([
                'Name' => $oldCompany->Name,
                'Description' => $oldCompany->Description,
                'AddressId' => $address?->Id,
                'ContactEmailAddress' => $oldCompany->DefaultContactEmailaddress,
                'DutchRegistrationNumber' => $oldCompany->DutchRegistrationNumber,
                'CreatedAt' => $oldCompany->CreatedAt,
                'UpdatedAt' => $oldCompany->CreatedAt,
                'old_id' => $oldCompany->Id
            ]);
        }
    }
}
