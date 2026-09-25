 <div
    class="min-h-screen bg-slate-50 p-6"
    x-data="{ now: new Date() }"
    x-init="setInterval(() => now = new Date(), 1000)"
>

    <div class="max-w-7xl mx-auto space-y-6">


        {{-- ========================================================= --}}
        {{-- TOP BAR --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>


                <div>

                    <h1 class="text-lg font-bold text-gray-900">
                        CME Attendance
                    </h1>

                    <p class="text-xs text-gray-400">
                        {{ $cmeYear }} — {{ $cmeProgramCode }}
                    </p>

                </div>

            </div>


            <div class="text-right">

                <div
                    class="text-sm font-mono text-gray-600"
                    x-text="now.toLocaleTimeString()"
                ></div>

                <div
                    class="text-xs text-gray-400"
                    x-text="now.toLocaleDateString(undefined, {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })"
                ></div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MAIN GRID --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">


            {{-- ===================================================== --}}
            {{-- LEFT COLUMN --}}
            {{-- ===================================================== --}}

            <div class="lg:col-span-1 space-y-6">


                {{-- REGISTERED MEMBERS --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    <div class="flex items-center gap-2 text-gray-500 text-sm font-medium mb-3">

                        <svg
                            class="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8z"
                            />
                        </svg>

                        Registered Members

                    </div>


                    <div class="text-4xl font-bold text-gray-900">
                        {{ $this->totalRegistered }}
                    </div>


                    {{-- OVERALL PROGRAM ATTENDANCE --}}
                    <div class="mt-5 pt-4 border-t border-gray-100">

                        <div class="text-xs text-gray-400 mb-1">
                            Total Attendance
                        </div>

                        <div class="text-2xl font-bold text-gray-900">
                            {{ $this->totalAttendance }}
                        </div>

                        <div class="text-xs text-gray-400 mt-1">
                            All dates
                        </div>

                    </div>


                    {{-- PROGRESS --}}
                    <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">

                        <div
                            class="h-full bg-blue-500 rounded-full"
                            style="
                                width:
                                {{
                                    $this->totalRegistered > 0
                                        ? min(
                                            100,
                                            round(
                                                ($this->totalAttendance / $this->totalRegistered) * 100
                                            )
                                        )
                                        : 0
                                }}%
                            "
                        ></div>

                    </div>


                    <div class="text-xs text-gray-400 mt-2">

                        {{ $this->totalAttendance }}

                        checked in

                        (
                        {{
                            $this->totalRegistered > 0
                                ? round(
                                    ($this->totalAttendance / $this->totalRegistered) * 100
                                )
                                : 0
                        }}%
                        )

                    </div>

                </div>


                {{-- TIME IN CARD --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    <label
                        for="memberId"
                        class="block text-sm font-semibold text-gray-700 mb-3"
                    >
                        Scan / Enter Member ID
                    </label>


                    <input
                        id="memberId"
                        type="text"
                        wire:model="memberId"
                        wire:keydown.enter="timeIn"
                        autofocus
                        autocomplete="off"
                        class="w-full text-xl text-center tracking-widest border-2 border-gray-200 rounded-xl px-3 py-4 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none mb-3"
                        placeholder="Member ID"
                    >


                    <button
                        wire:click="timeIn"
                        wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.98] disabled:opacity-50 text-white font-bold py-3.5 rounded-xl transition-all shadow-md shadow-blue-600/20"
                    >

                        <span wire:loading.remove>
                            TIME IN
                        </span>

                        <span wire:loading>
                            RECORDING...
                        </span>

                    </button>


                    {{-- MESSAGE --}}
                    @if ($message)

                        <div
                            wire:key="message-{{ now()->timestamp }}"
                            x-data="{ show: false }"
                            x-init="
                                show = true;
                                setTimeout(() => $wire.set('message', null), 4000)
                            "
                            x-show="show"
                            x-transition
                            class="mt-4 p-3 rounded-lg text-sm font-medium text-center
                            {{
                                $messageType === 'success'
                                    ? 'bg-green-50 text-green-700 border border-green-200'
                                    : 'bg-red-50 text-red-700 border border-red-200'
                            }}"
                        >

                            {{ $message }}

                        </div>

                    @endif

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- CENTER + RIGHT --}}
            {{-- ===================================================== --}}

            <div class="lg:col-span-3 space-y-6">


                {{-- ================================================= --}}
                {{-- TODAY'S ATTENDANCE --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    <div class="flex items-center justify-between mb-5">

                        <h2 class="font-bold text-gray-900">
                            Today's Attendance
                        </h2>

                        <span class="text-xs text-gray-400">
                            {{ now()->format('F d, Y') }}
                        </span>

                    </div>


                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">


                        {{-- TODAY TOTAL --}}
                        <div class="bg-slate-50 rounded-xl p-4">

                            <div class="flex items-center gap-2 text-gray-500 text-xs font-medium mb-2">

                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>

                                Today's Total

                            </div>

                            <div class="text-2xl font-bold text-gray-900">
                                {{ $this->todayAttendance }}
                            </div>

                        </div>


                        {{-- TODAY RM --}}
                        <div class="bg-blue-50 rounded-xl p-4">

                            <div class="flex items-center gap-2 text-blue-600 text-xs font-medium mb-2">

                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>

                                Regular Member

                            </div>

                            <div class="text-2xl font-bold text-blue-600">
                                {{ $this->todayRmCount }}
                            </div>

                            <div class="text-xs text-blue-400 mt-1">
                                RM
                            </div>

                        </div>


                        {{-- TODAY LM --}}
                        <div class="bg-purple-50 rounded-xl p-4">

                            <div class="flex items-center gap-2 text-purple-600 text-xs font-medium mb-2">

                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>

                                Life Member

                            </div>

                            <div class="text-2xl font-bold text-purple-600">
                                {{ $this->todayLmCount }}
                            </div>

                            <div class="text-xs text-purple-400 mt-1">
                                LM
                            </div>

                        </div>


                        {{-- TODAY TM --}}
                        <div class="bg-amber-50 rounded-xl p-4">

                            <div class="flex items-center gap-2 text-amber-600 text-xs font-medium mb-2">

                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                                Trainee Member

                            </div>

                            <div class="text-2xl font-bold text-amber-600">
                                {{ $this->todayTmCount }}
                            </div>

                            <div class="text-xs text-amber-400 mt-1">
                                TM
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- RECENT CHECK-INS + DAILY SUMMARY --}}
                {{-- ================================================= --}}

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


                    {{-- RECENT CHECK-INS --}}
                    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                        <div class="px-6 py-5 border-b flex items-center justify-between">

                            <h2 class="font-bold text-gray-900">
                                Recent Check-Ins
                            </h2>

                            <span class="text-xs text-gray-400">
                                Today, latest first
                            </span>

                        </div>


                        <div class="divide-y divide-gray-100 max-h-[420px] overflow-y-auto">

                            @forelse ($this->recentCheckIns as $checkIn)

                                @php

                                    $badgeColor = match($checkIn->mem_type) {

                                        'RM' =>
                                            'bg-blue-100 text-blue-700',

                                        'LM' =>
                                            'bg-purple-100 text-purple-700',

                                        'TM' =>
                                            'bg-amber-100 text-amber-700',

                                        default =>
                                            'bg-gray-100 text-gray-700',

                                    };


                                    $initials = strtoupper(
                                        substr($checkIn->first_name, 0, 1)
                                        .
                                        substr($checkIn->last_name, 0, 1)
                                    );

                                @endphp


                                <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">


                                    {{-- INITIALS --}}
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600 flex-shrink-0">

                                        {{ $initials }}

                                    </div>


                                    {{-- MEMBER --}}
                                    <div class="flex-1 min-w-0">

                                        <div class="font-semibold text-gray-900 truncate">

                                            {{ $checkIn->first_name }}
                                            {{ $checkIn->last_name }}

                                        </div>

                                        <div class="text-xs text-gray-400">

                                            ID: {{ $checkIn->member_id_no }}

                                        </div>

                                    </div>


                                    {{-- TYPE --}}
                                    <span
                                        class="text-xs font-bold px-2.5 py-1 rounded-full {{ $badgeColor }}"
                                    >

                                        {{ $checkIn->mem_type }}

                                    </span>


                                    {{-- TIME --}}
                                    <div class="text-sm font-mono text-gray-500 w-16 text-right">

                                        {{ \Carbon\Carbon::parse($checkIn->time_in)->format('h:i A') }}

                                    </div>

                                </div>


                            @empty

                                <div class="px-6 py-14 text-center text-gray-400">

                                    <svg
                                        class="w-10 h-10 mx-auto mb-3 text-gray-300"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>

                                    No check-ins yet today.

                                </div>

                            @endforelse

                        </div>

                    </div>


                    {{-- DAILY SUMMARY --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                        <h2 class="font-bold text-gray-900 mb-5">
                            Daily Summary
                        </h2>


                        <div class="space-y-4 max-h-[380px] overflow-y-auto pr-1">

                            @forelse ($this->dailyAttendance as $attendance)

                                <div class="p-4 bg-slate-50 rounded-xl">

                                    <div class="flex items-center justify-between mb-2">

                                        <span class="text-sm font-semibold text-gray-900">

                                            {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}

                                        </span>


                                        <span class="text-lg font-bold text-gray-900">

                                            {{ $attendance->total }}

                                        </span>

                                    </div>


                                    {{-- MEMBER TYPE BAR --}}
                                    <div class="flex gap-1.5 h-1.5 rounded-full overflow-hidden">

                                        @php
                                            $total = max($attendance->total, 1);
                                        @endphp


                                        <div
                                            class="bg-blue-500"
                                            style="width: {{ ($attendance->rm / $total) * 100 }}%"
                                        ></div>


                                        <div
                                            class="bg-purple-500"
                                            style="width: {{ ($attendance->lm / $total) * 100 }}%"
                                        ></div>


                                        <div
                                            class="bg-amber-500"
                                            style="width: {{ ($attendance->tm / $total) * 100 }}%"
                                        ></div>

                                    </div>


                                    {{-- COUNTS --}}
                                    <div class="flex gap-3 mt-2 text-xs text-gray-400">

                                        <span>
                                            RM {{ $attendance->rm }}
                                        </span>

                                        <span>
                                            LM {{ $attendance->lm }}
                                        </span>

                                        <span>
                                            TM {{ $attendance->tm }}
                                        </span>

                                    </div>

                                </div>

                            @empty

                                <div class="text-center text-gray-400 py-10 text-sm">
                                    No records yet.
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>