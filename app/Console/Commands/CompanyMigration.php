<?php

namespace App\Console\Commands;

use Carbon\Carbon;
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

            if($oldCompany->Description === null) {
                $description = '';
            } else {
                $description = strip_tags($oldCompany->Description);
                $description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $description);
                $description = str_replace(['&nbsp;', '&amp;'], [' ', '&'], $description);


                if(strlen($description) > 996) {
                    $description = substr($description,0,996) . '...';
                }
            }

            DB::table('new_Companies')->insert([
                'Name' => $oldCompany->Name,
                'Description' => $description,
                'AddressId' => $address?->Id,
                'ContactEmailAddress' => $oldCompany->DefaultContactEmailaddress,
                'DutchRegistrationNumber' => $oldCompany->DutchRegistrationNumber,
                'LogoFileId' => 1,
                'Blacklisted' => false,
                'ColorCode' => '#000000',
                'CreatedAt' => $oldCompany->CreatedAt,
                'UpdatedAt' => $oldCompany->CreatedAt,
                'old_id' => $oldCompany->Id
            ]);
        }


        $companiesWithoutAddress = DB::table('new_Companies')->where('AddressId', null)->get();

        foreach ($companiesWithoutAddress as $company) {
            $address = DB::table('new_Address')->where('CompanyId', $company->Id)->first();

            if($address) {
                DB::table('new_Companies')
                    ->where('id', $company->Id)
                    ->update(['AddressId' => $address->Id]);
            } else {
                DB::table('new_Companies')
                    ->where('id', $company->Id)
                    ->delete();
            }
        }
    }
}
