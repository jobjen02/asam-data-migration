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

                if($company?->Id) {
                    $number = $oldContact->PhoneNumber;
                    $number = str_replace([' ', '-', '(', ')'], ['', '', '', ''], $number);

                    if(str_starts_with($number, '003')) {
                        $number = substr_replace($number, '+3', 0, 3);
                    }

                    if(str_starts_with($number, '0')) {
                        $number = substr_replace($number, '+31', 0, 1);
                    }

                    if(str_starts_with($number, '6') && strlen($number) === 9) {
                        $number = substr_replace($number, '+31', 0, 0);
                    }

                    if(str_starts_with($number, '31') && strlen($number) === 11) {
                        $number = substr_replace($number, '+', 0, 0);
                    }

                    if(str_starts_with($number, '+3106')) {
                        $number = str_replace('+3106', '+316', $number);
                    }

                    if (str_contains($number, '@')) {
                        $number = '';
                    }

                    DB::table('new_Contacts')->insert([
                        'Name' => $oldContact->Name,
                        'Email' => $oldContact->Email,
                        'Phone' => $number,
                        'CompanyId' => $company?->Id,
                        'old_id' => $oldContact->Id
                    ]);
                }
            }
        }
    }
}
