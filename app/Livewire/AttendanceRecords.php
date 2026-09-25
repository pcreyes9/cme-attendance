<?php

namespace App\Livewire;

use App\Models\CmeAttendance;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceRecords extends Component
{
    use WithPagination;

    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

    public string $search = '';

    public string $filterDate = '';

    public string $filterMemType = '';

    public bool $showEditModal = false;

    public ?int $editingId = null;

    public string $editDate = '';

    public string $editTimeIn = '';

    public string $editRemarks = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDate' => ['except' => ''],
        'filterMemType' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDate(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMemType(): void
    {
        $this->resetPage();
    }

    public function getRecordsProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)

            ->when($this->search !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('member_id_no', 'like', $search)
                        ->orWhere('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('middle_name', 'like', $search);
                });
            })

            ->when($this->filterDate !== '', function ($query) {
                $query->whereDate('date', $this->filterDate);
            })

            ->when($this->filterMemType !== '', function ($query) {
                $query->where('mem_type', $this->filterMemType);
            })

            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->paginate(15);
    }

    public function edit(int $id): void
    {
        $attendance = CmeAttendance::query()
            ->where('id', $id)
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->firstOrFail();

        $this->editingId = $attendance->id;

        $this->editDate = $attendance->date
            ? $attendance->date->format('Y-m-d')
            : '';

        $this->editTimeIn = $attendance->time_in
            ? date('H:i', strtotime($attendance->time_in))
            : '';

        $this->editRemarks = $attendance->remarks ?? '';

        $this->showEditModal = true;
    }

    public function update(): void
    {
        $this->validate([
            'editDate' => ['required', 'date'],
            'editTimeIn' => ['required', 'date_format:H:i'],
            'editRemarks' => ['nullable', 'string', 'max:500'],
        ]);

        $attendance = CmeAttendance::query()
            ->where('id', $this->editingId)
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->firstOrFail();

        // Prevent duplicate attendance for the same member,
        // program, and date.
        $duplicate = CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $attendance->member_id_no)
            ->whereDate('date', $this->editDate)
            ->where('id', '!=', $attendance->id)
            ->exists();

        if ($duplicate) {
            $this->addError(
                'editDate',
                'This member already has an attendance record for this date.'
            );

            return;
        }

        $attendance->update([
            'date' => $this->editDate,
            'time_in' => $this->editTimeIn,
            'remarks' => $this->editRemarks ?: null,
        ]);

        $this->closeEditModal();

        session()->flash(
            'success',
            'Attendance record updated successfully.'
        );
    }

    public function delete(int $id): void
    {
        $attendance = CmeAttendance::query()
            ->where('id', $id)
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->firstOrFail();

        $attendance->delete();

        session()->flash(
            'success',
            'Attendance record deleted successfully.'
        );

        $this->resetPage();
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;

        $this->editingId = null;
        $this->editDate = '';
        $this->editTimeIn = '';
        $this->editRemarks = '';

        $this->resetValidation();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'filterDate',
            'filterMemType',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.attendance-records')
            ->layout('layouts.app');
    }
}