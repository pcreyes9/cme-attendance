<?php

use App\Livewire\AdminDashboard;
use App\Livewire\AttendanceRecords;
use Illuminate\Support\Facades\Route;
use App\Livewire\CmeAttendance\TimeIn;
use App\Livewire\ManualAttendance;
use App\Livewire\Reports;

Route::get('/', TimeIn::class)
    ->name('cme-attendance.time-in');

Route::get('dashboard', AdminDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('attendance-records', AttendanceRecords::class)
    ->middleware(['auth', 'verified'])
    ->name('attendance-records');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('manual-attendance', ManualAttendance::class)
    ->middleware(['auth', 'verified'])
    ->name('manual-attendance');
    
Route::get('reports', Reports::class)
    ->middleware(['auth', 'verified'])
    ->name('reports');

require __DIR__.'/auth.php';