<?php

namespace App\Livewire;

use App\Models\CmeAttendance;
use App\Models\CmeProgramRegistration;
use App\Models\CmeProgramRegistrationNM;
use App\Models\Member;
use Livewire\Component;

class ManualAttendance extends Component
{
    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

    public string $search = '';

    public ?string $selectedMemberId = null;

    public string $attendanceDate = '';

    public string $timeIn = '';

    public string $remarks = '';

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->attendanceDate = today()->format('Y-m-d');

        $this->timeIn = now()->format('H:i');
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH REGISTERED MEMBERS + NON-MEMBERS
    |--------------------------------------------------------------------------
    */

    public function getMembersProperty()
    {
        if (trim($this->search) === '') {
            return collect();
        }

        $search = '%' . trim($this->search) . '%';

        /*
        |--------------------------------------------------------------------------
        | REGULAR MEMBERS
        |--------------------------------------------------------------------------
        */

        $members = Member::query()
            ->where(function ($query) use ($search) {
                $query->where('member_id_no', 'like', $search)
                    ->orWhere('mem_first_name', 'like', $search)
                    ->orWhere('mem_last_name', 'like', $search)
                    ->orWhere('mem_middle_name', 'like', $search);
            })
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('cme_program_registration')
                    ->whereColumn(
                        'cme_program_registration.member_id_no',
                        'member.member_id_no'
                    )
                    ->where(
                        'cme_year',
                        $this->cmeYear
                    )
                    ->where(
                        'cme_program_code',
                        $this->cmeProgramCode
                    );
            })
            ->orderBy('mem_last_name')
            ->orderBy('mem_first_name')
            ->take(10)
            ->get()
            ->map(function ($member) {
                return (object) [
                    'id' => $member->member_id_no,

                    'first_name' => $member->mem_first_name,

                    'last_name' => $member->mem_last_name,

                    'middle_name' => $member->mem_middle_name,

                    'mem_type' => $member->psa_mem_type,

                    'is_nm' => false,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | NON-MEMBERS
        |--------------------------------------------------------------------------
        */

        $nonMembers = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where(function ($query) use ($search) {
                $query->where('nm_id_no', 'like', $search)
                    ->orWhere('nm_first_name', 'like', $search)
                    ->orWhere('nm_last_name', 'like', $search)
                    ->orWhere('nm_middle_name', 'like', $search);
            })
            ->orderBy('nm_last_name')
            ->orderBy('nm_first_name')
            ->take(10)
            ->get()
            ->map(function ($nonMember) {
                return (object) [
                    'id' => $nonMember->nm_id_no,

                    'first_name' => $nonMember->nm_first_name,

                    'last_name' => $nonMember->nm_last_name,

                    'middle_name' => $nonMember->nm_middle_name,

                    'mem_type' => 'NM',

                    'is_nm' => true,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | COMBINE RESULTS
        |--------------------------------------------------------------------------
        */

        return $members
            ->concat($nonMembers)
            ->take(10)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT MEMBER / NON-MEMBER
    |--------------------------------------------------------------------------
    */

    public function selectMember(string $memberId): void
    {
        /*
        |--------------------------------------------------------------------------
        | CHECK NON-MEMBER FIRST
        |--------------------------------------------------------------------------
        */

        $nonMember = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $memberId)
            ->first();

        if ($nonMember) {

            $this->selectedMemberId = $nonMember->nm_id_no;

            $this->search = $this->formatNonMemberName($nonMember);

            $this->resetValidation('selectedMemberId');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK REGULAR MEMBER
        |--------------------------------------------------------------------------
        */

        $member = Member::query()
            ->where('member_id_no', $memberId)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('cme_program_registration')
                    ->whereColumn(
                        'cme_program_registration.member_id_no',
                        'member.member_id_no'
                    )
                    ->where(
                        'cme_year',
                        $this->cmeYear
                    )
                    ->where(
                        'cme_program_code',
                        $this->cmeProgramCode
                    );
            })
            ->first();

        if (! $member) {
            $this->addError(
                'selectedMemberId',
                'Member is not registered for this CME program.'
            );

            return;
        }

        $this->selectedMemberId = $member->member_id_no;

        $this->search = $this->formatMemberName($member);

        $this->resetValidation('selectedMemberId');
    }

    /*
    |--------------------------------------------------------------------------
    | SELECTED MEMBER
    |--------------------------------------------------------------------------
    */

    public function getSelectedMemberProperty()
    {
        if (! $this->selectedMemberId) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK NM
        |--------------------------------------------------------------------------
        */

        $nonMember = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $this->selectedMemberId)
            ->first();

        if ($nonMember) {
            return (object) [
                'member_id_no' => $nonMember->nm_id_no,

                'mem_first_name' => $nonMember->nm_first_name,

                'mem_last_name' => $nonMember->nm_last_name,

                'mem_middle_name' => $nonMember->nm_middle_name,

                'psa_mem_type' => 'NM',

                'is_nm' => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK REGULAR MEMBER
        |--------------------------------------------------------------------------
        */

        $member = Member::query()
            ->where('member_id_no', $this->selectedMemberId)
            ->first();

        if (! $member) {
            return null;
        }

        $member->is_nm = false;

        return $member;
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        $this->validate([
            'selectedMemberId' => [
                'required',
            ],

            'attendanceDate' => [
                'required',
                'date',
            ],

            'timeIn' => [
                'required',
                'date_format:H:i',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'selectedMemberId.required' =>
                'Please select a registered member.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CHECK IF SELECTED PERSON IS NM
        |--------------------------------------------------------------------------
        */

        $nonMember = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $this->selectedMemberId)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | SAVE NM ATTENDANCE
        |--------------------------------------------------------------------------
        */

        if ($nonMember) {

            /*
            |--------------------------------------------------------------------------
            | CHECK DUPLICATE
            |--------------------------------------------------------------------------
            */

            $duplicate = CmeAttendance::query()
                ->where('cme_year', $this->cmeYear)
                ->where(
                    'cme_program_code',
                    $this->cmeProgramCode
                )
                ->where(
                    'member_id_no',
                    $nonMember->nm_id_no
                )
                ->whereDate(
                    'date',
                    $this->attendanceDate
                )
                ->exists();

            if ($duplicate) {
                $this->addError(
                    'attendanceDate',
                    'This non-member already has attendance for this date.'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE NM ATTENDANCE
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

                'time_in' => $this->timeIn,

                'time_out' => null,

                'date' => $this->attendanceDate,

                'remarks' => $this->remarks ?: null,
            ]);

            session()->flash(
                'success',
                'Manual attendance added successfully for '
                . $this->formatNonMemberName($nonMember)
                . '.'
            );

            $this->resetForm();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | REGULAR MEMBER
        |--------------------------------------------------------------------------
        */

        $member = Member::query()
            ->where(
                'member_id_no',
                $this->selectedMemberId
            )
            ->first();

        if (! $member) {
            $this->addError(
                'selectedMemberId',
                'Member could not be found.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK REGULAR MEMBER REGISTRATION
        |--------------------------------------------------------------------------
        */

        $registered = CmeProgramRegistration::query()
            ->where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'member_id_no',
                $member->member_id_no
            )
            ->exists();

        if (! $registered) {
            $this->addError(
                'selectedMemberId',
                'This member is not registered for the selected CME program.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE
        |--------------------------------------------------------------------------
        */

        $duplicate = CmeAttendance::query()
            ->where(
                'cme_year',
                $this->cmeYear
            )
            ->where(
                'cme_program_code',
                $this->cmeProgramCode
            )
            ->where(
                'member_id_no',
                $member->member_id_no
            )
            ->whereDate(
                'date',
                $this->attendanceDate
            )
            ->exists();

        if ($duplicate) {
            $this->addError(
                'attendanceDate',
                'This member already has attendance for this date.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE REGULAR MEMBER ATTENDANCE
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

            'time_in' => $this->timeIn,

            'time_out' => null,

            'date' => $this->attendanceDate,

            'remarks' => $this->remarks ?: null,
        ]);

        session()->flash(
            'success',
            'Manual attendance added successfully for '
            . $this->formatMemberName($member)
            . '.'
        );

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAR MEMBER
    |--------------------------------------------------------------------------
    */

    public function clearMember(): void
    {
        $this->selectedMemberId = null;

        $this->search = '';

        $this->resetValidation('selectedMemberId');
    }

    /*
    |--------------------------------------------------------------------------
    | RESET FORM
    |--------------------------------------------------------------------------
    */

    public function resetForm(): void
    {
        $this->selectedMemberId = null;

        $this->search = '';

        $this->attendanceDate = today()->format('Y-m-d');

        $this->timeIn = now()->format('H:i');

        $this->remarks = '';

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT REGULAR MEMBER NAME
    |--------------------------------------------------------------------------
    */

    protected function formatMemberName($member): string
    {
        return trim(
            $member->mem_last_name
            . ', '
            . $member->mem_first_name
            . ' '
            . ($member->mem_middle_name ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT NON-MEMBER NAME
    |--------------------------------------------------------------------------
    */

    protected function formatNonMemberName($nonMember): string
    {
        return trim(
            $nonMember->nm_last_name
            . ', '
            . $nonMember->nm_first_name
            . ' '
            . ($nonMember->nm_middle_name ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view('livewire.manual-attendance')
            ->layout('layouts.app');
    }
}