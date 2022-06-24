<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddressMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the addresses table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $oldAddresses = DB::table('CompanyAddresses')->get();

        DB::table('new_Address')->truncate();

        foreach ($oldAddresses as $oldAddress) {
            $company = null;

            if($oldAddress->CompanyId) {
                $company = DB::table('new_Companies')->where('old_id', $oldAddress->CompanyId)->first();
            }

            DB::table('new_Address')->insert([
                'Street' => $oldAddress->Street,
                'City' => $oldAddress->City,
                'PostalCode' => rtrim(preg_replace('/(\d+)/', '${1} ', str_replace(' ', '', $oldAddress->PostalCode))),
                'TypeOfAddress' => $oldAddress->TypeOfAddress,
                'Location' => $oldAddress->Location,
                'CompanyId' => $company?->Id,
                'LocationName' => $oldAddress->Street . ' ' . $oldAddress->PostalCode . ' ' . $oldAddress->City,
                'old_id' => $oldAddress->Id
            ]);
        }
    }
}
