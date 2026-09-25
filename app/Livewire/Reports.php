<?php

namespace App\Livewire;

use App\Models\CmeAttendance;
use App\Models\CmeProgramRegistration;
use Livewire\Component;

class Reports extends Component
{
    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

    public string $fromDate = '';

    public string $toDate = '';

    public string $filterMemType = '';

    public function mount(): void
    {
        $this->fromDate = today()->format('Y-m-d');
        $this->toDate = today()->format('Y-m-d');
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public function clearFilters(): void
    {
        $this->fromDate = '';
        $this->toDate = '';
        $this->filterMemType = '';
    }

    /*
    |--------------------------------------------------------------------------
    | Registered Members
    |--------------------------------------------------------------------------
    */

    public function getTotalRegisteredProperty(): int
    {
        return CmeProgramRegistration::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->distinct()
            ->count('member_id_no');
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Query
    |--------------------------------------------------------------------------
    */

    protected function attendanceQuery()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)

            ->when($this->fromDate !== '', function ($query) {
                $query->whereDate('date', '>=', $this->fromDate);
            })

            ->when($this->toDate !== '', function ($query) {
                $query->whereDate('date', '<=', $this->toDate);
            })

            ->when($this->filterMemType !== '', function ($query) {
                $query->where('mem_type', $this->filterMemType);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    public function getTotalAttendanceProperty(): int
    {
        return $this->attendanceQuery()->count();
    }

    public function getRmCountProperty(): int
    {
        return $this->attendanceQuery()
            ->where('mem_type', 'RM')
            ->count();
    }

    public function getLmCountProperty(): int
    {
        return $this->attendanceQuery()
            ->where('mem_type', 'LM')
            ->count();
    }

    public function getTmCountProperty(): int
    {
        return $this->attendanceQuery()
            ->where('mem_type', 'TM')
            ->count();
    }

    public function getAttendancePercentageProperty(): float
    {
        if ($this->totalRegistered === 0) {
            return 0;
        }

        return round(
            ($this->totalAttendance / $this->totalRegistered) * 100,
            1
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Records
    |--------------------------------------------------------------------------
    */

    public function getAttendanceRecordsProperty()
    {
        return $this->attendanceQuery()
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Daily Summary
    |--------------------------------------------------------------------------
    */

    public function getDailySummaryProperty()
    {
        return $this->attendanceQuery()
            ->selectRaw("
                date,
                COUNT(*) as total,
                SUM(CASE WHEN mem_type = 'RM' THEN 1 ELSE 0 END) as rm,
                SUM(CASE WHEN mem_type = 'LM' THEN 1 ELSE 0 END) as lm,
                SUM(CASE WHEN mem_type = 'TM' THEN 1 ELSE 0 END) as tm
            ")
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view('livewire.reports')
            ->layout('layouts.app');
    }
}