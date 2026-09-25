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


    {{-- Navigation --}}
    {{-- <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center gap-6 h-12 text-sm">

                <a
                    href="{{ route('dashboard') }}"
                    class="font-semibold text-indigo-600"
                >
                    Dashboard
                </a>

                <a
                    href="{{ url('/') }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Time In
                </a>

                <a
                    href="{{ url('/attendance-records') }}"
                    class="text-gray-600 hover:text-gray-900"
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            {{-- Registered --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Registered Members
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($this->totalRegistered) }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-5a4 4 0 10-8 0 4 4 0 008 0zm5 0a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>

                </div>

            </div>


            {{-- Today --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Today's Attendance
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($this->todayAttendance) }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-green-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                </div>

            </div>


            {{-- RM --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Regular Members
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($this->todayRmCount) }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-sm font-bold text-blue-600">
                            RM
                        </span>
                    </div>

                </div>

            </div>


            {{-- LM/TM --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            LM / TM
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($this->todayLmCount + $this->todayTmCount) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            LM: {{ $this->todayLmCount }}
                            ·
                            TM: {{ $this->todayTmCount }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-lg bg-purple-50 flex items-center justify-center">
                        <span class="text-sm font-bold text-purple-600">
                            LM/TM
                        </span>
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