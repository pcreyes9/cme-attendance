<div class="min-h-screen bg-gray-100">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between h-16">

                <div>
                    <h1 class="text-xl font-bold text-gray-900">
                        CME Attendance
                    </h1>

                    <p class="text-sm text-gray-500">
                        Attendance Records
                    </p>
                </div>

                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">
                        {{ now()->format('F d, Y') }}
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ now()->format('h:i A') }}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- Navigation --}}
    {{-- <div class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center gap-6 h-12 text-sm">

                <a
                    href="{{ route('dashboard') }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Dashboard
                </a>

                <a
                    href="{{ url('/cme-attendance') }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Time In
                </a>

                <a
                    href="{{ url('/attendance-records') }}"
                    class="font-semibold text-indigo-600"
                >
                    Attendance Records
                </a>

                <a
                    href="{{ url('/manual-attendance') }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Manual Attendance
                </a>

                <a
                    href="{{ url('/reports') }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Reports
                </a>

                <div class="ml-auto">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="text-gray-600 hover:text-red-600"
                        >
                            Log Out
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </div> --}}


    {{-- Main --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Heading --}}
        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-900">
                Attendance Records
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                View and manage CME attendance records.
            </p>

        </div>


        {{-- Flash Message --}}
        @if (session()->has('success'))

            <div
                class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                {{ session('success') }}
            </div>

        @endif


        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Search --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Search
                    </label>

                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Member ID or name..."
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >

                </div>


                {{-- Date --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date
                    </label>

                    <input
                        type="date"
                        wire:model.live="filterDate"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >

                </div>


                {{-- Member Type --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Member Type
                    </label>

                    <select
                        wire:model.live="filterMemType"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >

                        <option value="">
                            All Types
                        </option>

                        <option value="RM">
                            RM - Regular
                        </option>

                        <option value="LM">
                            LM - Life
                        </option>

                        <option value="TM">
                            TM - Trainee
                        </option>

                    </select>

                </div>

            </div>


            {{-- Clear --}}
            <div class="mt-4">

                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Clear Filters
                </button>

            </div>

        </div>


        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h3 class="font-semibold text-gray-900">
                            Attendance List
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Program: {{ $cmeProgramCode }}
                        </p>
                    </div>

                    <div wire:loading class="text-sm text-gray-500">
                        Loading...
                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Member
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Type
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Date
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Time In
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Remarks
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse ($this->records as $record)

                            <tr
                                wire:key="attendance-{{ $record->id }}"
                                class="hover:bg-gray-50"
                            >

                                {{-- Member --}}
                                <td class="px-6 py-4 whitespace-nowrap">

                                    <div class="font-medium text-gray-900">
                                        {{ $record->last_name }},
                                        {{ $record->first_name }}
                                        {{ $record->middle_name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        ID: {{ $record->member_id_no }}
                                    </div>

                                </td>


                                {{-- Type --}}
                                <td class="px-6 py-4 whitespace-nowrap">

                                    @php
                                        $typeClass = match ($record->mem_type) {
                                            'RM' => 'bg-blue-100 text-blue-700',
                                            'LM' => 'bg-purple-100 text-purple-700',
                                            'TM' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $typeClass }}"
                                    >
                                        {{ $record->mem_type }}
                                    </span>

                                </td>


                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">

                                    {{ $record->date
                                        ? $record->date->format('M d, Y')
                                        : ''
                                    }}

                                </td>


                                {{-- Time --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">

                                    {{ $record->time_in
                                        ? date('h:i A', strtotime($record->time_in))
                                        : ''
                                    }}

                                </td>


                                {{-- Remarks --}}
                                <td class="px-6 py-4 text-sm text-gray-600">

                                    {{ $record->remarks ?: '—' }}

                                </td>


                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">

                                    <div class="flex justify-end gap-2">

                                        <button
                                            type="button"
                                            wire:click="edit({{ $record->id }})"
                                            class="px-3 py-1.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="delete({{ $record->id }})"
                                            wire:confirm="Are you sure you want to delete this attendance record?"
                                            class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg"
                                        >
                                            Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center"
                                >

                                    <div class="text-gray-500">

                                        <p class="font-medium">
                                            No attendance records found.
                                        </p>

                                        <p class="text-sm mt-1">
                                            Try changing your search or filters.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($this->records->hasPages())

                <div class="px-6 py-4 border-t border-gray-200">

                    {{ $this->records->links() }}

                </div>

            @endif

        </div>

    </main>


    {{-- Edit Modal --}}
    @if ($showEditModal)

        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            wire:click.self="closeEditModal"
        >

            <div class="w-full max-w-lg bg-white rounded-xl shadow-xl">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200">

                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Edit Attendance
                            </h3>

                            <p class="text-sm text-gray-500">
                                Update attendance details.
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="closeEditModal"
                            class="text-gray-400 hover:text-gray-600 text-xl"
                        >
                            ×
                        </button>

                    </div>

                </div>


                {{-- Modal Body --}}
                <div class="p-6 space-y-5">

                    {{-- Date --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date
                        </label>

                        <input
                            type="date"
                            wire:model="editDate"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('editDate')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Time --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Time In
                        </label>

                        <input
                            type="time"
                            wire:model="editTimeIn"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('editTimeIn')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Remarks --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Remarks
                        </label>

                        <textarea
                            wire:model="editRemarks"
                            rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional remarks..."
                        ></textarea>

                        @error('editRemarks')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">

                    <button
                        type="button"
                        wire:click="closeEditModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="update"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="update">
                            Save Changes
                        </span>

                        <span wire:loading wire:target="update">
                            Saving...
                        </span>
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>