<div class="min-h-screen bg-gray-100">

    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        CME Attendance Reports
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Attendance report for
                        <span class="font-medium text-gray-700">
                            {{ $cmeProgramCode }}
                        </span>
                    </p>
                </div>

                <button
                    onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                           bg-gray-900 text-white rounded-lg text-sm font-medium
                           hover:bg-gray-800 transition print:hidden"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z"
                        />
                    </svg>

                    Print Report
                </button>

            </div>

        </div>
    </div>


    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


        <!-- ========================================================= -->
        <!-- REPORT FILTERS -->
        <!-- ========================================================= -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Report Filters
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Select the date range and member type.
                    </p>
                </div>

                <button
                    wire:click="clearFilters"
                    class="text-sm text-gray-500 hover:text-gray-900 transition print:hidden"
                >
                    Clear Filters
                </button>

            </div>


            <!-- 3 Column Filter Layout -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <!-- From Date -->
                <div>

                    <label
                        for="fromDate"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        From Date
                    </label>

                    <input
                        id="fromDate"
                        type="date"
                        wire:model.live="fromDate"
                        class="w-full rounded-lg border-gray-300
                               focus:border-gray-500 focus:ring-gray-500"
                    >

                </div>


                <!-- To Date -->
                <div>

                    <label
                        for="toDate"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        To Date
                    </label>

                    <input
                        id="toDate"
                        type="date"
                        wire:model.live="toDate"
                        class="w-full rounded-lg border-gray-300
                               focus:border-gray-500 focus:ring-gray-500"
                    >

                </div>


                <!-- Member Type -->
                <div>

                    <label
                        for="filterMemType"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Member Type
                    </label>

                    <select
                        id="filterMemType"
                        wire:model.live="filterMemType"
                        class="w-full rounded-lg border-gray-300
                               focus:border-gray-500 focus:ring-gray-500"
                    >
                        <option value="">
                            All Member Types
                        </option>

                        <option value="RM">
                            Regular Member (RM)
                        </option>

                        <option value="LM">
                            Life Member (LM)
                        </option>

                        <option value="TM">
                            Trainee Member (TM)
                        </option>

                        <option value="NM">
                            Non-Member (NM)
                        </option>
                    </select>

                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- SUMMARY CARDS -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">


            <!-- Registered -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <div class="text-sm text-gray-500">
                    Registered
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->totalRegistered) }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    Total registered
                </div>

            </div>


            <!-- Attendance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <div class="text-sm text-gray-500">
                    Attendance
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->totalAttendance) }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    Total check-ins
                </div>

            </div>


            <!-- RM -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <div class="text-sm text-gray-500">
                    Regular Members
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->rmCount) }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    RM attendance
                </div>

            </div>


            <!-- LM -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <div class="text-sm text-gray-500">
                    Life Members
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->lmCount) }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    LM attendance
                </div>

            </div>


            <!-- TM -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <div class="text-sm text-gray-500">
                    Trainee Members
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->tmCount) }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    TM attendance
                </div>

            </div>


            <!-- NM -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <div class="text-sm text-gray-500">
                    Non-Members
                </div>

                <div class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->nmCount) }}
                </div>

                <div class="mt-1 text-xs text-gray-400">
                    NM attendance
                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- ATTENDANCE RATE -->
        <!-- ========================================================= -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">

            <div class="flex items-center justify-between mb-3">

                <div>
                    <h2 class="font-semibold text-gray-900">
                        Attendance Rate
                    </h2>

                    <p class="text-sm text-gray-500">
                        Attendance compared with registered participants.
                    </p>
                </div>

                <div class="text-2xl font-bold text-gray-900">
                    {{ number_format($this->attendancePercentage, 1) }}%
                </div>

            </div>


            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">

                <div
                    class="bg-gray-900 h-3 rounded-full transition-all duration-300"
                    style="width: {{ min($this->attendancePercentage, 100) }}%"
                ></div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- DAILY SUMMARY -->
        <!-- ========================================================= -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">

            <div class="px-6 py-5 border-b border-gray-200">

                <h2 class="text-lg font-semibold text-gray-900">
                    Daily Summary
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Attendance grouped by date and member type.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Date
                            </th>

                            <th class="px-6 py-3 text-center font-semibold text-gray-600">
                                Total
                            </th>

                            <th class="px-6 py-3 text-center font-semibold text-gray-600">
                                RM
                            </th>

                            <th class="px-6 py-3 text-center font-semibold text-gray-600">
                                LM
                            </th>

                            <th class="px-6 py-3 text-center font-semibold text-gray-600">
                                TM
                            </th>

                            <th class="px-6 py-3 text-center font-semibold text-gray-600">
                                NM
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($this->dailySummary as $row)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-3 text-gray-900 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($row->date)->format('F d, Y') }}
                                </td>

                                <td class="px-6 py-3 text-center font-semibold">
                                    {{ number_format($row->total) }}
                                </td>

                                <td class="px-6 py-3 text-center">
                                    {{ number_format($row->rm) }}
                                </td>

                                <td class="px-6 py-3 text-center">
                                    {{ number_format($row->lm) }}
                                </td>

                                <td class="px-6 py-3 text-center">
                                    {{ number_format($row->tm) }}
                                </td>

                                <td class="px-6 py-3 text-center">
                                    {{ number_format($row->nm) }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    No attendance records found for the selected filters.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- DETAILED ATTENDANCE -->
        <!-- ========================================================= -->

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="px-6 py-5 border-b border-gray-200">

                <h2 class="text-lg font-semibold text-gray-900">
                    Detailed Attendance
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Complete attendance records for the selected period.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Date
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Time In
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Member ID
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Member Name
                            </th>

                            <th class="px-6 py-3 text-center font-semibold text-gray-600">
                                Type
                            </th>

                            <th class="px-6 py-3 text-left font-semibold text-gray-600">
                                Remarks
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($this->attendanceRecords as $record)

                            <tr class="hover:bg-gray-50">

                                <!-- Date -->
                                <td class="px-6 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}
                                </td>


                                <!-- Time In -->
                                <td class="px-6 py-3 whitespace-nowrap font-medium">
                                    {{ \Carbon\Carbon::parse($record->time_in)->format('h:i A') }}
                                </td>


                                <!-- Member ID -->
                                <td class="px-6 py-3 whitespace-nowrap">
                                    {{ $record->member_id_no }}
                                </td>


                                <!-- Member Name -->
                                <td class="px-6 py-3 whitespace-nowrap font-medium text-gray-900">
                                    {{ $record->last_name }},
                                    {{ $record->first_name }}
                                    {{ $record->middle_name }}
                                </td>


                                <!-- Member Type -->
                                <td class="px-6 py-3 text-center">

                                    @php
                                        $typeClasses = match ($record->mem_type) {
                                            'RM' => 'bg-gray-100 text-gray-700',
                                            'LM' => 'bg-gray-100 text-gray-700',
                                            'TM' => 'bg-gray-100 text-gray-700',
                                            'NM' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex items-center px-2.5 py-1
                                               rounded-full text-xs font-medium
                                               {{ $typeClasses }}"
                                    >
                                        {{ $record->mem_type }}
                                    </span>

                                </td>


                                <!-- Remarks -->
                                <td class="px-6 py-3 text-gray-500">
                                    {{ $record->remarks ?: '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center text-gray-500"
                                >
                                    No attendance records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- ============================================================= -->
    <!-- PRINT STYLES -->
    <!-- ============================================================= -->

    <style>
        @media print {

            /*
             * Hide navigation and interactive controls
             */
            nav,
            button,
            input,
            select {
                display: none !important;
            }


            /*
             * Remove page background
             */
            body {
                background: white !important;
            }


            /*
             * Remove unnecessary minimum height
             */
            .min-h-screen {
                min-height: 0 !important;
            }


            /*
             * Remove shadows
             */
            .shadow-sm {
                box-shadow: none !important;
            }


            /*
             * Keep borders clean
             */
            .border {
                border: 1px solid #ddd !important;
            }


            /*
             * Prevent table rows from splitting
             */
            tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }


            /*
             * Repeat table headers on printed pages
             */
            thead {
                display: table-header-group;
            }


            /*
             * Print page margins
             */
            @page {
                margin: 12mm;
            }

        }
    </style>

</div>