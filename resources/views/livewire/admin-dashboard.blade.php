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
                        Admin Dashboard
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


    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Program Header --}}
        <div class="mb-8">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Dashboard
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Monitor CME attendance for the current program.
                    </p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg px-4 py-3">

                    <div class="text-xs text-gray-500 uppercase tracking-wide">
                        CME Program
                    </div>

                    <div class="mt-1 font-semibold text-gray-900">
                        {{ $cmeProgramCode }}
                    </div>

                    <div class="text-xs text-gray-500">
                        Year {{ $cmeYear }}
                    </div>

                </div>

            </div>

        </div>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8">

            {{-- Registered Members --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Registered Members</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->totalRegistered) }}
                </p>
                <div class="mt-3 text-xs font-medium text-indigo-600">
                    Total Registrations
                </div>
            </div>

            {{-- Today's Attendance --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Today's Attendance</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ number_format($this->todayAttendance) }}
                </p>
                <div class="mt-3 text-xs font-medium text-green-600">
                    Total Check-ins Today
                </div>
            </div>

            {{-- Regular Members --}}
            <div class="bg-white rounded-xl border border-blue-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Regular Members</p>
                        <p class="mt-2 text-3xl font-bold text-blue-700">
                            {{ number_format($this->todayRmCount) }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-sm font-bold text-blue-600">RM</span>
                    </div>
                </div>
            </div>

            {{-- Life Members --}}
            <div class="bg-white rounded-xl border border-purple-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Life Members</p>
                        <p class="mt-2 text-3xl font-bold text-purple-700">
                            {{ number_format($this->todayLmCount) }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-purple-50 flex items-center justify-center">
                        <span class="text-sm font-bold text-purple-600">LM</span>
                    </div>
                </div>
            </div>

            {{-- Trainee Members --}}
            <div class="bg-white rounded-xl border border-amber-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Trainee Members</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">
                            {{ number_format($this->todayTmCount) }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-amber-50 flex items-center justify-center">
                        <span class="text-sm font-bold text-amber-600">TM</span>
                    </div>
                </div>
            </div>

        </div>


        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Check-ins --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">

                <div class="px-6 py-5 border-b border-gray-200">

                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="font-semibold text-gray-900">
                                Recent Check-ins
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Latest attendance records
                            </p>
                        </div>

                        <a
                            href="{{ url('/attendance-records') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                        >
                            View All
                        </a>

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

                            </tr>

                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse ($this->recentCheckIns as $attendance)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="font-medium text-gray-900">
                                            {{ $attendance->last_name }},
                                            {{ $attendance->first_name }}
                                            {{ $attendance->middle_name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            ID: {{ $attendance->member_id_no }}
                                        </div>

                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $attendance->mem_type }}
                                        </span>

                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="px-6 py-10 text-center">

                                        <p class="text-sm text-gray-500">
                                            No attendance records yet.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200">

                <div class="px-6 py-5 border-b border-gray-200">

                    <h3 class="font-semibold text-gray-900">
                        Quick Actions
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Manage CME attendance
                    </p>

                </div>


                <div class="p-6 space-y-3">

                    <a
                        href="{{ url('/cme-attendance') }}"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
                    >
                        <span class="font-medium">
                            Time In
                        </span>

                        <span>→</span>
                    </a>


                    <a
                        href="{{ url('/attendance-records') }}"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
                    >
                        <span class="font-medium">
                            Attendance Records
                        </span>

                        <span>→</span>
                    </a>


                    <a
                        href="{{ url('/manual-attendance') }}"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
                    >
                        <span class="font-medium">
                            Manual Attendance
                        </span>

                        <span>→</span>
                    </a>


                    <a
                        href="{{ url('/reports') }}"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
                    >
                        <span class="font-medium">
                            Reports
                        </span>

                        <span>→</span>
                    </a>

                </div>

            </div>

        </div>

    </main>

</div>