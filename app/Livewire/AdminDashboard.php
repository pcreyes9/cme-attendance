<?php

namespace App\Livewire;

use App\Models\CmeAttendance;
use App\Models\CmeProgramRegistration;
use Livewire\Component;
use App\Models\CmeProgramRegistrationNM;

class AdminDashboard extends Component
{
    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

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

    public function getTodayAttendanceProperty(): int
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->whereDate('date', today())
            ->count();
    }

    public function getTodayRmCountProperty(): int
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->whereDate('date', today())
            ->where('mem_type', 'RM')
            ->count();
    }

    public function getTodayLmCountProperty(): int
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->whereDate('date', today())
            ->where('mem_type', 'LM')
            ->count();
    }

    public function getTodayTmCountProperty(): int
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->whereDate('date', today())
            ->where('mem_type', 'TM')
            ->count();
    }

    public function getRecentCheckInsProperty()
    {
        return CmeAttendance::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard')
            ->layout('layouts.app');
    }
}