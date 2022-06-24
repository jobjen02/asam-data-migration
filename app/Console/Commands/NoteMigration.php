<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NoteMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:notes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the notes table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldAssignmentNotes = DB::table('AssignmentNotes')->get();

        DB::table('new_Notes')->truncate();

        foreach ($oldAssignmentNotes as $assignmentNote) {
            $user = DB::table('new_Users')->where('old_id', $assignmentNote->UserId)->first();
            $assignment = DB::table('new_Assignments')->where('old_id', $assignmentNote->AssignmentId)->first();

            if($assignment && $user) {
                DB::table('new_Notes')->insert([
                    'Description' => $assignmentNote->Note,
                    'Coordinatorid' => $user?->Id,
                    'CreatedAt' => $assignmentNote->CreatedAt,
                    'UpdatedAt' => $assignmentNote->UpdatedAt,
                    'AssignmentId' => $assignment->Id,
                    'old_id' => $assignmentNote->Id
                ]);
            }
        }
    }
}
