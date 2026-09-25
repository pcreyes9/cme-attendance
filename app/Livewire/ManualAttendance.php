<?php

namespace App\Livewire;

use App\Models\CmeAttendance;
use App\Models\CmeProgramRegistration;
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

    public function mount(): void
    {
        $this->attendanceDate = today()->format('Y-m-d');
        $this->timeIn = now()->format('H:i');
    }

    public function getMembersProperty()
    {
        if (trim($this->search) === '') {
            return collect();
        }

        $search = '%' . trim($this->search) . '%';

        return Member::query()
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
                    ->where('cme_year', $this->cmeYear)
                    ->where('cme_program_code', $this->cmeProgramCode);
            })
            ->orderBy('mem_last_name')
            ->orderBy('mem_first_name')
            ->take(10)
            ->get();
    }

    public function selectMember(string $memberId): void
    {
        $member = Member::query()
            ->where('member_id_no', $memberId)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('cme_program_registration')
                    ->whereColumn(
                        'cme_program_registration.member_id_no',
                        'member.member_id_no'
                    )
                    ->where('cme_year', $this->cmeYear)
                    ->where('cme_program_code', $this->cmeProgramCode);
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

    public function getSelectedMemberProperty()
    {
        if (! $this->selectedMemberId) {
            return null;
        }

        return Member::query()
            ->where('member_id_no', $this->selectedMemberId)
            ->first();
    }

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
            'selectedMemberId.required' => 'Please select a registered member.',
        ]);

        $member = Member::query()
            ->where('member_id_no', $this->selectedMemberId)
            ->first();

        if (! $member) {
            $this->addError(
                'selectedMemberId',
                'Member could not be found.'
            );

            return;
        }

        // Make sure the member is registered for this CME program.
        $registered = CmeProgramRegistration::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $member->member_id_no)
            ->exists();

        if (! $registered) {
            $this->addError(
                'selectedMemberId',
                'This member is not registered for the selected CME program.'
            );

            return;
        }

        // Prevent duplicate attendance.
        $duplicate = CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $member->member_id_no)
            ->whereDate('date', $this->attendanceDate)
            ->exists();

        if ($duplicate) {
            $this->addError(
                'attendanceDate',
                'This member already has attendance for this date.'
            );

            return;
        }

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

    public function clearMember(): void
    {
        $this->selectedMemberId = null;
        $this->search = '';

        $this->resetValidation('selectedMemberId');
    }

    public function resetForm(): void
    {
        $this->selectedMemberId = null;
        $this->search = '';
        $this->attendanceDate = today()->format('Y-m-d');
        $this->timeIn = now()->format('H:i');
        $this->remarks = '';

        $this->resetValidation();
    }

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

    public function render()
    {
        return view('livewire.manual-attendance')
            ->layout('layouts.app');
    }
}