<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate all tables';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $this->info('Migrating all tables');

        $this->info('Migrating Addresses table');
        Artisan::call('migrate:addresses');
        $this->info('Addresses table migrated');

        $this->info('Migrating Assignments table');
        Artisan::call('migrate:assignments');
        $this->info('Assignments table migrated');

        $this->info('Migrating Companies table');
        Artisan::call('migrate:companies');
        $this->info('Companies table migrated');

        $this->info('Migrating Contacts table');
        Artisan::call('migrate:contacts');
        $this->info('Contacts table migrated');

        $this->info('Migrating Notes table');
        Artisan::call('migrate:notes');
        $this->info('Notes table migrated');

        $this->info('Migrating Specializations table');
        Artisan::call('migrate:specializations');
        $this->info('Specializations table migrated');

        $this->info('Migrating Users table');
        Artisan::call('migrate:users');
        $this->info('Users table migrated');

        $this->info('Migrating Partners table');
        Artisan::call('migrate:partners');
        $this->info('Partners table migrated');

        $this->info('Migrating Files table');
        Artisan::call('migrate:files');
        $this->info('Files table migrated');

        $this->info('Migrating Languages table');
        Artisan::call('migrate:languages');
        $this->info('Languages table migrated');
    }
}
