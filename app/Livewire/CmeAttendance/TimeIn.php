<?php

namespace App\Livewire\CmeAttendance;

use App\Models\CmeAttendance;
use App\Models\CmeProgramRegistration;
use App\Models\Member;
use Livewire\Component;

class TimeIn extends Component
{
    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

    public string $memberId = '';

    public ?string $message = null;

    public ?string $messageType = null;

    public ?Member $member = null;


    /*
    |--------------------------------------------------------------------------
    | TIME IN
    |--------------------------------------------------------------------------
    */

    public function timeIn(): void
    {
        $this->resetMessage();

        $memberId = trim($this->memberId);

        if ($memberId === '') {
            $this->showMessage(
                'Please enter a Member ID.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FIND MEMBER
        |--------------------------------------------------------------------------
        */

        $this->member = Member::where(
            'member_id_no',
            $memberId
        )->first();

        if (!$this->member) {
            $this->showMessage(
                'Member ID not found.',
                'error'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK CME REGISTRATION
        |--------------------------------------------------------------------------
        */

        $registered = CmeProgramRegistration::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'member_id_no',
                $memberId
            )
            ->exists();

        if (!$registered) {
            $this->showMessage(
                'Member is not registered for this CME program.',
                'error'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE TIME IN
        |--------------------------------------------------------------------------
        */

        $alreadyTimedIn = CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'member_id_no',
                $memberId
            )
            ->whereDate(
                'date',
                today()
            )
            ->exists();

        if ($alreadyTimedIn) {
            $this->showMessage(
                'This member has already timed in today.',
                'error'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE ATTENDANCE
        |--------------------------------------------------------------------------
        */

        CmeAttendance::create([
            'cme_year' => $this->cmeYear,
            'cme_program_code' => $this->cmeProgramCode,

            'member_id_no' => $this->member->member_id_no,

            'first_name' => $this->member->mem_first_name,
            'last_name' => $this->member->mem_last_name,
            'middle_name' => $this->member->mem_middle_name,

            'mem_type' => $this->member->psa_mem_type,

            'time_in' => now()->format('H:i:s'),

            'time_out' => null,

            'date' => today(),

            'remarks' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        $this->showMessage(
            "Time In recorded for {$this->member->mem_first_name} {$this->member->mem_last_name}.",
            'success'
        );


        /*
        |--------------------------------------------------------------------------
        | RESET INPUT
        |--------------------------------------------------------------------------
        */

        $this->memberId = '';

        $this->member = null;
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL ATTENDANCE - ALL DATES
    |--------------------------------------------------------------------------
    */

    public function getTotalAttendanceProperty()
    {
        return CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S TOTAL ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function getTodayAttendanceProperty()
    {
        return CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->whereDate(
                'date',
                today()
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S RM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayRmCountProperty()
    {
        return CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'mem_type',
                'RM'
            )
            ->whereDate(
                'date',
                today()
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S LM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayLmCountProperty()
    {
        return CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'mem_type',
                'LM'
            )
            ->whereDate(
                'date',
                today()
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S TM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayTmCountProperty()
    {
        return CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'mem_type',
                'TM'
            )
            ->whereDate(
                'date',
                today()
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | DAILY ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function getDailyAttendanceProperty()
    {
        return CmeAttendance::selectRaw("
                date,
                COUNT(*) as total,

                SUM(
                    CASE
                        WHEN mem_type = 'RM'
                        THEN 1
                        ELSE 0
                    END
                ) as rm,

                SUM(
                    CASE
                        WHEN mem_type = 'LM'
                        THEN 1
                        ELSE 0
                    END
                ) as lm,

                SUM(
                    CASE
                        WHEN mem_type = 'TM'
                        THEN 1
                        ELSE 0
                    END
                ) as tm
            ")
            ->where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL REGISTERED
    |--------------------------------------------------------------------------
    */

    public function getTotalRegisteredProperty()
    {
        return CmeProgramRegistration::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | RECENT CHECK-INS
    |--------------------------------------------------------------------------
    */

    public function getRecentCheckInsProperty()
    {
        return CmeAttendance::where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->whereDate(
                'date',
                today()
            )
            ->orderByDesc('time_in')
            ->limit(8)
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    private function showMessage(
        string $message,
        string $type
    ): void {
        $this->message = $message;

        $this->messageType = $type;
    }


    private function resetMessage(): void
    {
        $this->message = null;

        $this->messageType = null;

        $this->member = null;
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.cme-attendance.time-in'
        )->layout(
            'layouts.app'
        );
    }
}
