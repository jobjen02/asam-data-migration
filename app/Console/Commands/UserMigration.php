<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the users table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldUsers = DB::table('Users')->get();

        DB::table('new_Users')->truncate();

        foreach ($oldUsers as $oldUser) {
            DB::table('new_Users')->insert([
                'FirstName' => null,
                'LastName' => null,
                'Username' => $oldUser->Username,
                'Email' => $oldUser->EmailAddress,
                'AccountLoginType' => 1,
                'ExternalIdentityId' => null,
                'Role' => 5,
                'CompanyId' => null,
                'old_id' => $oldUser->Id
            ]);
        }
    }
}
