<?php

namespace App\Livewire\CmeAttendance;

use App\Models\CmeAttendance;
use App\Models\CmeProgramRegistration;
use App\Models\CmeProgramRegistrationNM;
use App\Models\Member;
use Livewire\Component;

class TimeIn extends Component
{
    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

    public string $memberId = '';

    public ?string $message = null;

    public ?string $messageType = null;

    public string $recentSearch = '';


    /*
    |--------------------------------------------------------------------------
    | LAST TIMED-IN PARTICIPANT
    |--------------------------------------------------------------------------
    */

    public ?string $displayFirstName = null;

    public ?string $displayLastName = null;

    public ?string $displayMiddleName = null;

    public ?string $displayMemberId = null;

    public ?string $displayMemberType = null;

    public ?string $displayTimeIn = null;

    public ?string $memberPhoto = null;


    /*
    |--------------------------------------------------------------------------
    | TIME IN
    |--------------------------------------------------------------------------
    */

    public function timeIn(): void
    {
        /*
        |--------------------------------------------------------------------------
        | RESET PREVIOUS MESSAGE / SUMMARY
        |--------------------------------------------------------------------------
        */

        $this->resetMessage();

        $memberId = trim($this->memberId);

        if ($memberId === '') {

            $this->showMessage(
                'Please enter your Member ID.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 1. CHECK NON-MEMBER
        |--------------------------------------------------------------------------
        */

        $nonMember = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $memberId)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | 2. NON-MEMBER TIME IN
        |--------------------------------------------------------------------------
        */

        if ($nonMember) {

            /*
            |--------------------------------------------------------------------------
            | CHECK DUPLICATE
            |--------------------------------------------------------------------------
            */

            $alreadyTimedIn = CmeAttendance::query()
                ->where('cme_year', $this->cmeYear)
                ->where('cme_program_code', $this->cmeProgramCode)
                ->where('member_id_no', $memberId)
                ->whereDate('date', today())
                ->exists();

            if ($alreadyTimedIn) {

                $this->showMessage(
                    'You have already timed in today.',
                    'error'
                );

                $this->memberId = '';

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CURRENT TIME
            |--------------------------------------------------------------------------
            */

            $timeIn = now();


            /*
            |--------------------------------------------------------------------------
            | SAVE NM ATTENDANCE
            |--------------------------------------------------------------------------
            */

            CmeAttendance::create([
                'cme_year' => $this->cmeYear,

                'cme_program_code' => $this->cmeProgramCode,

                'member_id_no' => $nonMember->nm_id_no,

                'first_name' => $nonMember->nm_first_name,

                'last_name' => $nonMember->nm_last_name,

                'middle_name' => $nonMember->nm_middle_name,

                'mem_type' => 'NM',

                'time_in' => $timeIn->format('H:i:s'),

                'time_out' => null,

                'date' => today(),

                'remarks' => null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | HEADER SUMMARY
            |--------------------------------------------------------------------------
            */

            $this->displayFirstName =
                $nonMember->nm_first_name;

            $this->displayLastName =
                $nonMember->nm_last_name;

            $this->displayMiddleName =
                $nonMember->nm_middle_name;

            $this->displayMemberId =
                $nonMember->nm_id_no;

            $this->displayMemberType =
                'NM';

            $this->displayTimeIn =
                $timeIn->format('h:i:s A');

            $this->memberPhoto = null;


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            $this->showMessage(
                'Time In recorded successfully.',
                'success'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 3. CHECK REGULAR CME REGISTRATION
        |--------------------------------------------------------------------------
        */

        $registeredMember = CmeProgramRegistration::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $memberId)
            ->exists();

        if (!$registeredMember) {

            $this->showMessage(
                'You are not registered for this CME program.',
                'error'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 4. GET MEMBER
        |--------------------------------------------------------------------------
        */

        $member = Member::query()
            ->where('member_id_no', $memberId)
            ->first();

        if (!$member) {

            $this->showMessage(
                'Member information not found.',
                'error'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 5. CHECK DUPLICATE TIME IN
        |--------------------------------------------------------------------------
        */

        $alreadyTimedIn = CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $memberId)
            ->whereDate('date', today())
            ->exists();

        if ($alreadyTimedIn) {

            $this->showMessage(
                'You have already timed in today.',
                'error'
            );

            $this->memberId = '';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 6. CURRENT TIME
        |--------------------------------------------------------------------------
        */

        $timeIn = now();


        /*
        |--------------------------------------------------------------------------
        | 7. GET PHOTO
        |--------------------------------------------------------------------------
        */

        $photo = $this->getMemberPhoto($member);


        /*
        |--------------------------------------------------------------------------
        | 8. SAVE ATTENDANCE
        |--------------------------------------------------------------------------
        */

        CmeAttendance::create([
            'cme_year' => $this->cmeYear,

            'cme_program_code' => $this->cmeProgramCode,

            'member_id_no' => $member->member_id_no,

            'first_name' => $member->mem_first_name,

            'last_name' => $member->mem_last_name,

            'middle_name' => $member->mem_middle_name,

            'mem_type' => $member->psa_mem_type,

            'time_in' => $timeIn->format('H:i:s'),

            'time_out' => null,

            'date' => today(),

            'remarks' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 9. SET HEADER SUMMARY
        |--------------------------------------------------------------------------
        */

        $this->displayFirstName =
            $member->mem_first_name;

        $this->displayLastName =
            $member->mem_last_name;

        $this->displayMiddleName =
            $member->mem_middle_name;

        $this->displayMemberId =
            $member->member_id_no;

        $this->displayMemberType =
            $member->psa_mem_type;

        $this->displayTimeIn =
            $timeIn->format('h:i:s A');

        $this->memberPhoto = $photo;


        /*
        |--------------------------------------------------------------------------
        | 10. SUCCESS
        |--------------------------------------------------------------------------
        */

        $this->showMessage(
            'Time In recorded successfully.',
            'success'
        );


        /*
        |--------------------------------------------------------------------------
        | 11. CLEAR INPUT
        |--------------------------------------------------------------------------
        */

        $this->memberId = '';
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER PHOTO
    |--------------------------------------------------------------------------
    */

    private function getMemberPhoto(?Member $member): ?string
    {
        if (!$member || empty($member->mem_pic)) {
            return null;
        }

        $photo = $member->mem_pic;


        /*
        |--------------------------------------------------------------------------
        | SQL SERVER / PDO RESOURCE
        |--------------------------------------------------------------------------
        */

        if (is_resource($photo)) {

            $photo = stream_get_contents($photo);
        }


        if (!is_string($photo) || $photo === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | RAW JPEG
        |--------------------------------------------------------------------------
        */

        if (substr($photo, 0, 2) === "\xFF\xD8") {

            return 'data:image/jpeg;base64,' .
                base64_encode($photo);
        }


        /*
        |--------------------------------------------------------------------------
        | HEX DATA
        |--------------------------------------------------------------------------
        |
        | Some SQL Server configurations may return binary data
        | as hexadecimal text beginning with 0x.
        |
        */

        if (str_starts_with($photo, '0x')) {

            $photo = substr($photo, 2);
        }


        /*
        |--------------------------------------------------------------------------
        | HEX JPEG
        |--------------------------------------------------------------------------
        */

        if (
            ctype_xdigit($photo) &&
            strlen($photo) % 2 === 0
        ) {

            $binary = hex2bin($photo);

            if (
                $binary !== false &&
                substr($binary, 0, 2) === "\xFF\xD8"
            ) {

                return 'data:image/jpeg;base64,' .
                    base64_encode($binary);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAR HEADER SUMMARY
    |--------------------------------------------------------------------------
    */

    public function clearSummary(): void
    {
        $this->displayFirstName = null;

        $this->displayLastName = null;

        $this->displayMiddleName = null;

        $this->displayMemberId = null;

        $this->displayMemberType = null;

        $this->displayTimeIn = null;

        $this->memberPhoto = null;
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function getTotalAttendanceProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S TOTAL ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function getTodayAttendanceProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->whereDate('date', today())
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S RM
    |--------------------------------------------------------------------------
    */

    public function getTodayRmCountProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('mem_type', 'RM')
            ->whereDate('date', today())
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S LM
    |--------------------------------------------------------------------------
    */

    public function getTodayLmCountProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('mem_type', 'LM')
            ->whereDate('date', today())
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S TM
    |--------------------------------------------------------------------------
    */

    public function getTodayTmCountProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('mem_type', 'TM')
            ->whereDate('date', today())
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S NM
    |--------------------------------------------------------------------------
    */

    public function getTodayNmCountProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('mem_type', 'NM')
            ->whereDate('date', today())
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | DAILY ATTENDANCE
    |--------------------------------------------------------------------------
    */
    public function getDailyAttendanceProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->selectRaw("
                CAST([date] AS DATE) AS [date],
                COUNT(*) AS [total],
                SUM(CASE
                    WHEN UPPER(LTRIM(RTRIM(mem_type))) = 'RM'
                    THEN 1 ELSE 0
                END) AS [rm_count],
                SUM(CASE
                    WHEN UPPER(LTRIM(RTRIM(mem_type))) = 'LM'
                    THEN 1 ELSE 0
                END) AS [lm_count],
                SUM(CASE
                    WHEN UPPER(LTRIM(RTRIM(mem_type))) = 'TM'
                    THEN 1 ELSE 0
                END) AS [tm_count],
                SUM(CASE
                    WHEN UPPER(LTRIM(RTRIM(mem_type))) = 'NM'
                    THEN 1 ELSE 0
                END) AS [nm_count]
            ")
            ->groupByRaw('CAST([date] AS DATE)')
            ->orderByRaw('CAST([date] AS DATE) DESC')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL REGISTERED
    |--------------------------------------------------------------------------
    */

    public function getTotalRegisteredProperty(): int
    {
        $members = CmeProgramRegistration::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->distinct()
            ->count('member_id_no');


        $nonMembers = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->distinct()
            ->count('nm_id_no');


        return $members + $nonMembers;
    }


    /*
    |--------------------------------------------------------------------------
    | RECENT CHECK-INS
    |--------------------------------------------------------------------------
    */

    public function getRecentCheckInsProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->whereDate('date', today())
            ->when(trim($this->recentSearch) !== '', function ($query) {
                $search = '%' . trim($this->recentSearch) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('member_id_no', 'LIKE', $search)
                        ->orWhere('first_name', 'LIKE', $search)
                        ->orWhere('last_name', 'LIKE', $search)
                        ->orWhere('middle_name', 'LIKE', $search)
                        ->orWhere('mem_type', 'LIKE', $search);
                });
            })
            ->orderByDesc('time_in')
            ->limit(50)
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

        /*
        |--------------------------------------------------------------------------
        | Clear previous header summary when a new scan starts
        |--------------------------------------------------------------------------
        */

        $this->clearSummary();
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