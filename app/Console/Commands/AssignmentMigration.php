<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:assignments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will migrate the assignments table to the new format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldAssignments = DB::table('Assignments')->get();
        $approvedAssignments = DB::table('ApprovedAssignments')->get();

        DB::table('new_Assignments')->truncate();
        DB::table('new_AssignmentSpecialization')->truncate();

        foreach ($oldAssignments as $oldAssignment) {
            $company = null;
            $contact = null;
            $address = null;

            if($oldAssignment->CompanyId) {
                $company = DB::table('new_Companies')->where('old_id', $oldAssignment->CompanyId)->first();

                if($company !== null) {
                    $contact = DB::table('new_Contacts')->where('CompanyId', $company->Id)->first();
                }
            }

            if($oldAssignment->AddressId) {
                $address = DB::table('new_Address')->where('old_id', $oldAssignment->AddressId)->first();
            }

            $assignmentCreatedAtDate = Carbon::createFromFormat('Y-m-d H:i:s', $oldAssignment->CreatedAt);

            if($assignmentCreatedAtDate->between(Carbon::now()->subYear(), Carbon::now())) {
                $description = strip_tags($oldAssignment->Description);
                $description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $description);
                $description = str_replace(['&nbsp;', '&amp;'], [' ', '&'], $description);


                if(strlen($description) > 996) {
                    $description = substr($description,0,996) . '...';
                }

                $isFullFilled = false;

                if($oldAssignment->Status === 2) {
                    $isFullFilled = true;
                }

                DB::table('new_Assignments')->insert([
                    'Name' => $oldAssignment->Title,
                    'Description' => $description,
                    'CompanyId' => $company?->Id,
                    'Compensation' => $oldAssignment->Compensation,
                    'ContactId' => $contact?->Id,
                    'AddressId' => $address?->Id,
                    'StartingDate' => $oldAssignment->DesiredStartDate ?? $assignmentCreatedAtDate,
                    'ExpirationDate' => $assignmentCreatedAtDate->addYear(),
                    'isFullFilled' => $isFullFilled,
                    'CreatedAt' => $oldAssignment->CreatedAt,
                    'UpdatedAt' => $oldAssignment->UpdatedAt,
                    'old_id' => $oldAssignment->Id
                ]);
            } else {
                Log::info($oldAssignment->CreatedAt);
            }
        }

        foreach ($approvedAssignments as $approvedAssignment) {
            $assignment = DB::table('new_Assignments')->where('old_id', $approvedAssignment->AssignmentId)->first();
            $specialization = DB::table('new_Specializations')->where('old_id', $approvedAssignment->SpecializationId)->first();

            if($assignment?->Id !== null) {
                DB::table('new_AssignmentSpecialization')->insert([
                    'AssignmentId' => $assignment?->Id,
                    'SpecializationId' => $specialization?->Id,
                    'Status' => 1, // Approved
                    'ReviewStatusMotivation' => $approvedAssignment->Notes,
                    'old_id' => $approvedAssignment->Id
                ]);
            }
        }

        $newAssignments = DB::table('new_Assignments')->get();

        foreach ($newAssignments as $assignment) {
            $levels = [];
            $assignmentSpecializations = DB::table('new_AssignmentSpecialization')->where('AssignmentId', $assignment->Id)->get();

            foreach ($assignmentSpecializations as $assignmentSpecialization) {
                $approvedAssignment = DB::table('ApprovedAssignments')->where('Id', $assignmentSpecialization->old_id)->first();

                if($approvedAssignment) {
                    $levels[] = $approvedAssignment->Level;
                }
            }

            if(count($levels) > 0) {
                if(in_array(1, $levels, true)) {
                    DB::table('new_Assignments')
                        ->where('id', $assignment->Id)
                        ->update(['AssignmentTypes' => 1]);
                }

                if(in_array(2, $levels, true)) {
                    DB::table('new_Assignments')
                        ->where('id', $assignment->Id)
                        ->update(['AssignmentTypes' => 2]);
                }

                if(in_array(3, $levels, true) || (in_array(1, $levels, true) && in_array(2, $levels, true))) {
                    DB::table('new_Assignments')
                        ->where('id', $assignment->Id)
                        ->update(['AssignmentTypes' => 3]);
                }
            }
        }

        $newAssignments = DB::table('new_Assignments')->where('AssignmentTypes', null)->get();

        foreach ($newAssignments as $assignment) {
            DB::table('new_Assignments')
                ->where('id', $assignment->Id)
                ->delete();
        }
    }
}
