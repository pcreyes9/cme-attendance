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
        | 1. CHECK NON-MEMBER REGISTRATION
        |--------------------------------------------------------------------------
        */

        $nonMember = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $memberId)
            ->first([
                'nm_id_no',
                'nm_first_name',
                'nm_middle_name',
                'nm_last_name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | 2. RESOLVE PARTICIPANT INFORMATION
        |--------------------------------------------------------------------------
        */

        if ($nonMember) {
            $participant = [
                'id' => $nonMember->nm_id_no,
                'first_name' => $nonMember->nm_first_name,
                'middle_name' => $nonMember->nm_middle_name,
                'last_name' => $nonMember->nm_last_name,
                'type' => 'NM',
            ];

            $member = null;
        } else {
            /*
            |--------------------------------------------------------------------------
            | CHECK REGULAR MEMBER REGISTRATION
            |--------------------------------------------------------------------------
            */

            $registered = CmeProgramRegistration::query()
                ->where('cme_year', $this->cmeYear)
                ->where('cme_program_code', $this->cmeProgramCode)
                ->where('member_id_no', $memberId)
                ->exists();

            if (! $registered) {
                $this->showMessage(
                    'You are not registered for this CME program.',
                    'error'
                );

                $this->memberId = '';

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | LOAD ONLY REQUIRED MEMBER COLUMNS
            |--------------------------------------------------------------------------
            */

            $member = Member::query()
                ->select([
                    'member_id_no',
                    'mem_first_name',
                    'mem_middle_name',
                    'mem_last_name',
                    'psa_mem_type',
                    'mem_pic',
                ])
                ->where('member_id_no', $memberId)
                ->first();

            if (! $member) {
                $this->showMessage(
                    'Member information not found.',
                    'error'
                );

                $this->memberId = '';

                return;
            }

            $participant = [
                'id' => $member->member_id_no,
                'first_name' => $member->mem_first_name,
                'middle_name' => $member->mem_middle_name,
                'last_name' => $member->mem_last_name,
                'type' => $member->psa_mem_type,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. CHECK DUPLICATE TIME IN
        |--------------------------------------------------------------------------
        |
        | Use a date range instead of whereDate().
        | This avoids applying a SQL function to the date column.
        |
        */

        $startOfDay = now()->startOfDay();
        $startOfTomorrow = $startOfDay->copy()->addDay();

        $alreadyTimedIn = CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $participant['id'])
            ->where('date', '>=', $startOfDay)
            ->where('date', '<', $startOfTomorrow)
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
        | 4. SAVE ATTENDANCE
        |--------------------------------------------------------------------------
        */

        $timeIn = now();

        CmeAttendance::create([
            'cme_year' => $this->cmeYear,
            'cme_program_code' => $this->cmeProgramCode,

            'member_id_no' => $participant['id'],

            'first_name' => $participant['first_name'],
            'middle_name' => $participant['middle_name'],
            'last_name' => $participant['last_name'],

            'mem_type' => $participant['type'],

            'time_in' => $timeIn->format('H:i:s'),
            'time_out' => null,
            'date' => $timeIn->toDateString(),
            'remarks' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. SET SUCCESS SUMMARY
        |--------------------------------------------------------------------------
        */

        $this->displayFirstName = $participant['first_name'];
        $this->displayLastName = $participant['last_name'];
        $this->displayMiddleName = $participant['middle_name'];
        $this->displayMemberId = $participant['id'];
        $this->displayMemberType = $participant['type'];

        $this->displayTimeIn = $timeIn->format('h:i:s A');

        /*
        |--------------------------------------------------------------------------
        | 6. MEMBER PHOTO
        |--------------------------------------------------------------------------
        */

        $this->memberPhoto = $member
            ? $this->getMemberPhoto($member)
            : null;

        /*
        |--------------------------------------------------------------------------
        | 7. SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        $this->showMessage(
            'Time In recorded successfully.',
            'success'
        );

        $this->memberId = '';
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER PHOTO
    |--------------------------------------------------------------------------
    */

    private function getMemberPhoto(?Member $member): ?string
    {
        if (! $member || empty($member->mem_pic)) {
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

        if (! is_string($photo) || $photo === '') {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | RAW JPEG
        |--------------------------------------------------------------------------
        */

        if (substr($photo, 0, 2) === "\xFF\xD8") {
            return 'data:image/jpeg;base64,' . base64_encode($photo);
        }

        /*
        |--------------------------------------------------------------------------
        | HEX DATA
        |--------------------------------------------------------------------------
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
                return 'data:image/jpeg;base64,' . base64_encode($binary);
            }
        }

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
    | TODAY'S ATTENDANCE STATISTICS
    |--------------------------------------------------------------------------
    |
    | One aggregate query supplies all five dashboard figures.
    |
    */

    public function getTodayStatsProperty(): array
    {
        $startOfDay = now()->startOfDay();
        $startOfTomorrow = $startOfDay->copy()->addDay();

        $stats = CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('date', '>=', $startOfDay)
            ->where('date', '<', $startOfTomorrow)
            ->selectRaw("
                COUNT(*) AS total,

                SUM(CASE
                    WHEN mem_type = 'RM' THEN 1
                    ELSE 0
                END) AS rm,

                SUM(CASE
                    WHEN mem_type = 'LM' THEN 1
                    ELSE 0
                END) AS lm,

                SUM(CASE
                    WHEN mem_type = 'TM' THEN 1
                    ELSE 0
                END) AS tm,

                SUM(CASE
                    WHEN mem_type = 'NM' THEN 1
                    ELSE 0
                END) AS nm
            ")
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'rm' => (int) ($stats->rm ?? 0),
            'lm' => (int) ($stats->lm ?? 0),
            'tm' => (int) ($stats->tm ?? 0),
            'nm' => (int) ($stats->nm ?? 0),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S TOTAL ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function getTodayAttendanceProperty(): int
    {
        return $this->todayStats['total'];
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S RM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayRmCountProperty(): int
    {
        return $this->todayStats['rm'];
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S LM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayLmCountProperty(): int
    {
        return $this->todayStats['lm'];
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S TM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayTmCountProperty(): int
    {
        return $this->todayStats['tm'];
    }


    /*
    |--------------------------------------------------------------------------
    | TODAY'S NM COUNT
    |--------------------------------------------------------------------------
    */

    public function getTodayNmCountProperty(): int
    {
        return $this->todayStats['nm'];
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function getTotalAttendanceProperty(): int
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->count();
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
    | DAILY ATTENDANCE SUMMARY
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
    | RECENT CHECK-INS
    |--------------------------------------------------------------------------
    */

    public function getRecentCheckInsProperty()
    {
        $startOfDay = now()->startOfDay();
        $startOfTomorrow = $startOfDay->copy()->addDay();

        $search = trim($this->recentSearch);

        return CmeAttendance::query()
            ->select([
                'member_id_no',
                'first_name',
                'middle_name',
                'last_name',
                'mem_type',
                'time_in',
                'date',
            ])
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('date', '>=', $startOfDay)
            ->where('date', '<', $startOfTomorrow)
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($q) use ($term) {
                    $q->where('member_id_no', 'LIKE', $term)
                        ->orWhere('first_name', 'LIKE', $term)
                        ->orWhere('last_name', 'LIKE', $term)
                        ->orWhere('middle_name', 'LIKE', $term)
                        ->orWhere('mem_type', 'LIKE', $term);
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