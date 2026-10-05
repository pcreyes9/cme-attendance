<div
    class="min-h-screen bg-slate-50 p-4 sm:p-6"
    x-data="{ now: new Date() }"
    x-init="setInterval(() => now = new Date(), 1000)"
>
    {{-- HEADER --}}
    <div class="mb-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">

        @if (!$displayMemberId)
            {{-- DEFAULT HEADER --}}
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 px-6 py-6 text-white">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-blue-200">
                            Philippine Society of Anesthesiologists, INC.
                        </p>

                        <h1 class="mt-2 text-3xl font-bold">
                            CME Attendance
                        </h1>

                        <p class="mt-2 text-sm text-blue-100">
                            Scan your registered member ID to record attendance.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-white/15 px-3 py-1 text-sm">
                                CME Year: {{ $cmeYear }}
                            </span>

                            <span class="rounded-full bg-white/15 px-3 py-1 text-sm">
                                Program: {{ $cmeProgramCode }}
                            </span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white/10 p-4 text-left sm:min-w-48 sm:text-right">
                        <div
                            class="text-3xl font-bold tabular-nums"
                            x-text="now.toLocaleTimeString('en-PH', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: true
                            })"
                        ></div>

                        <div
                            class="mt-1 text-sm text-blue-100"
                            x-text="now.toLocaleDateString('en-PH', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })"
                        ></div>
                    </div>
                </div>
            </div>
        @else
            {{-- SUCCESSFUL TIME-IN MEMBER HEADER --}}
            <div
                class="relative overflow-hidden bg-gradient-to-r from-emerald-700 to-emerald-500 px-6 py-6 text-white"
                x-data="{ showSummary: true }"
                x-init="setTimeout(() => {
                    showSummary = false;
                    $wire.clearSummary();
                }, 8000)"
            >
                <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-center">

                    {{-- MEMBER PHOTO --}}
                    <div class="shrink-0">
                        @if ($memberPhoto)
                            <img
                                src="{{ $memberPhoto }}"
                                alt="Member photo"
                                class="h-28 w-28 rounded-full border-4 border-white/80 bg-white object-cover shadow-lg"
                            >
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-full border-4 border-white/80 bg-white/20 text-3xl font-bold shadow-lg">
                                {{ strtoupper(substr($displayFirstName ?? '', 0, 1) . substr($displayLastName ?? '', 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- MEMBER INFORMATION --}}
                    <div class="min-w-0 flex-1 text-center sm:text-left">
                        <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-sm font-semibold">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 111.414-1.415l2.793 2.793 6.493-6.493a1 1 0 011.415 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>

                            TIME IN RECORDED
                        </div>

                        <h2 class="text-2xl font-bold sm:text-3xl">
                            {{ $displayLastName }},
                            {{ $displayFirstName }}
                            {{ $displayMiddleName }}
                        </h2>

                        <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                            <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-semibold">
                                ID: {{ $displayMemberId }}
                            </span>

                            <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-semibold">
                                {{ match ($displayMemberType) {
                                    'RM' => 'Regular Member',
                                    'LM' => 'Life Member',
                                    'TM' => 'Trainee Member',
                                    'NM' => 'Non-Member',
                                    default => $displayMemberType ?? 'Member'
                                } }}
                                ({{ $displayMemberType }})
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-emerald-50">
                            Attendance Date: {{ now()->format('F d, Y') }}
                        </p>
                    </div>

                    {{-- TIME-IN TIME --}}
                    <div class="rounded-xl bg-white/15 px-5 py-4 text-center sm:min-w-44">
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">
                            Time In
                        </p>

                        <p class="mt-1 text-2xl font-bold tabular-nums">
                            {{ $displayTimeIn }}
                        </p>

                        <p class="mt-2 text-xs text-emerald-100">
                            Welcome!
                        </p>
                    </div>
                </div>

                <div class="mt-5 h-1 overflow-hidden rounded-full bg-white/20">
                    <div
                        class="h-full rounded-full bg-white"
                        x-show="showSummary"
                        x-transition
                        style="animation: summary-progress 8s linear forwards;"
                    ></div>
                </div>
            </div>
        @endif
    </div>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- LEFT COLUMN: REGISTRATION AND TIME-IN --}}
        <div class="space-y-6 xl:col-span-1">

            {{-- REGISTRATION STATISTICS --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Registered Participants
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Total program registrations
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-50 p-3 text-blue-700">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="10" cy="7" r="4" />
                            <path d="M20 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                        </svg>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="text-4xl font-bold text-slate-900">
                        {{ $this->totalRegistered }}
                    </p>

                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-slate-500">Total attendance recorded</span>

                        <span class="font-semibold text-blue-700">
                            {{ $this->totalAttendance }}
                        </span>
                    </div>

                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div
                            class="h-full rounded-full bg-blue-600 transition-all duration-500"
                            style="width: {{ $this->totalRegistered > 0 ? min(100, ($this->totalAttendance / $this->totalRegistered) * 100) : 0 }}%"
                        ></div>
                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Overall check-in progress
                    </p>
                </div>
            </div>

            {{-- TIME-IN FORM --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5">
                    <h2 class="text-lg font-bold text-slate-900">
                        Participant Time In
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Scan the QR code or enter the registered ID.
                    </p>
                </div>

                <form wire:submit="timeIn" class="space-y-4">
                    <div>
                        <label
                            for="memberId"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Member / Participant ID
                        </label>

                        <input
                            id="memberId"
                            type="text"
                            wire:model="memberId"
                            placeholder="Scan or enter ID here..."
                            autocomplete="off"
                            autofocus
                            class="w-full rounded-xl border border-slate-300 px-4 py-4 text-lg font-semibold uppercase text-slate-900 outline-none transition placeholder:font-normal placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('memberId')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="timeIn"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-4 font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="timeIn">
                            Record Time In
                        </span>

                        <span wire:loading wire:target="timeIn">
                            Processing...
                        </span>
                    </button>
                </form>

                {{-- FEEDBACK MESSAGE --}}
                @if ($message)
                    <div
                        class="mt-4 rounded-xl border p-4 text-sm font-medium
                        {{ $messageType === 'success'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                            : 'border-red-200 bg-red-50 text-red-800' }}"
                        role="status"
                        aria-live="polite"
                    >
                        <div class="flex items-start gap-2">
                            @if ($messageType === 'success')
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            @else
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            @endif

                            <span>{{ $message }}</span>
                        </div>
                    </div>
                @endif

                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <p class="text-xs leading-5 text-slate-500">
                        Only participants registered for the selected CME program
                        can record attendance. Duplicate check-ins for the same
                        day are not allowed.
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: ATTENDANCE DASHBOARD --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- TODAY'S ATTENDANCE --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Today's Attendance
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Check-ins recorded for today
                        </p>
                    </div>

                    <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ now()->format('F d, Y') }}
                    </span>
                </div>

                {{-- EACH MEMBER TYPE HAS ITS OWN CARD --}}
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">

                    {{-- TOTAL --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold text-slate-500">
                            Today's Total
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $this->todayAttendance }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Participants
                        </p>
                    </div>

                    {{-- RM --}}
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs font-semibold text-blue-700">
                            Regular Member
                        </p>

                        <p class="mt-2 text-3xl font-bold text-blue-800">
                            {{ $this->todayRmCount }}
                        </p>

                        <p class="mt-1 text-xs text-blue-600">RM</p>
                    </div>

                    {{-- LM --}}
                    <div class="rounded-xl border border-purple-100 bg-purple-50 p-4">
                        <p class="text-xs font-semibold text-purple-700">
                            Life Member
                        </p>

                        <p class="mt-2 text-3xl font-bold text-purple-800">
                            {{ $this->todayLmCount }}
                        </p>

                        <p class="mt-1 text-xs text-purple-600">LM</p>
                    </div>

                    {{-- TM --}}
                    <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
                        <p class="text-xs font-semibold text-amber-700">
                            Trainee Member
                        </p>

                        <p class="mt-2 text-3xl font-bold text-amber-800">
                            {{ $this->todayTmCount }}
                        </p>

                        <p class="mt-1 text-xs text-amber-600">TM</p>
                    </div>

                    {{-- NM --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-600">
                            Non-Member
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-800">
                            {{ $this->todayNmCount }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500">NM</p>
                    </div>
                </div>
            </div>

            {{-- RECENT CHECK-INS --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Recent Check-Ins
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Latest participants recorded today
                        </p>
                    </div>

                    <div class="flex items-center gap-3">

                        {{-- QUICK SEARCH --}}
                        <div class="relative w-full sm:w-72">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6z"
                                />
                            </svg>

                            <input
                                type="text"
                                wire:model.live.debounce.300ms="recentSearch"
                                placeholder="Search ID or name..."
                                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >

                            @if ($recentSearch)
                                <button
                                    type="button"
                                    wire:click="$set('recentSearch', '')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                    title="Clear search"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            @endif

                        </div>

                        {{-- LIVE INDICATOR --}}
                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            Live
                        </span>

                    </div>
                </div>

                @if ($this->recentCheckIns->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 py-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 8v4l3 2" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>

                        <p class="mt-3 font-semibold text-slate-700">
                            No check-ins yet
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Successful time-ins will appear here.
                        </p>
                    </div>
                @else
                    <div class="max-h-[300px] overflow-y-auto divide-y divide-slate-100 pr-2">
                        @foreach ($this->recentCheckIns as $checkIn)
                            <div
                                wire:key="recent-check-in-{{ $checkIn->id }}"
                                class="flex items-center gap-3 py-4 first:pt-0 last:pb-0"
                            >
                                {{-- INITIALS AVATAR --}}
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                                    {{ match ($checkIn->mem_type) {
                                        'RM' => 'bg-blue-100 text-blue-700',
                                        'LM' => 'bg-purple-100 text-purple-700',
                                        'TM' => 'bg-amber-100 text-amber-700',
                                        'NM' => 'bg-gray-200 text-gray-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    } }}
                                    text-sm font-bold"
                                >
                                    {{ strtoupper(
                                        substr($checkIn->first_name ?? '', 0, 1) .
                                        substr($checkIn->last_name ?? '', 0, 1)
                                    ) }}
                                </div>

                                {{-- NAME AND ID --}}
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-slate-900">
                                        {{ $checkIn->last_name }},
                                        {{ $checkIn->first_name }}
                                        {{ $checkIn->middle_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        ID: {{ $checkIn->member_id_no }}
                                    </p>
                                </div>

                                {{-- TYPE AND TIME --}}
                                <div class="shrink-0 text-right">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold
                                        {{ match ($checkIn->mem_type) {
                                            'RM' => 'bg-blue-100 text-blue-700',
                                            'LM' => 'bg-purple-100 text-purple-700',
                                            'TM' => 'bg-amber-100 text-amber-700',
                                            'NM' => 'bg-gray-200 text-gray-700',
                                            default => 'bg-slate-100 text-slate-700'
                                        } }}"
                                    >
                                        {{ $checkIn->mem_type }}
                                    </span>

                                    <p class="mt-1 text-sm font-semibold tabular-nums text-slate-700">
                                        {{ \Carbon\Carbon::parse($checkIn->time_in)->format('h:i:s A') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- DAILY ATTENDANCE SUMMARY --}}
    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900">
                Daily Attendance Summary
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Attendance totals by date and member classification
            </p>
        </div>

        @if ($this->dailyAttendance->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 py-10 text-center">
                <p class="font-semibold text-slate-700">
                    No attendance records available
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Daily totals will appear after participants check in.
                </p>
            </div>
        @else
            {{-- LEGEND --}}
            <div class="mb-5 flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-sm bg-blue-600"></span>
                    <span class="text-sm text-slate-600">RM</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-sm bg-purple-600"></span>
                    <span class="text-sm text-slate-600">LM</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-sm bg-amber-500"></span>
                    <span class="text-sm text-slate-600">TM</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-sm bg-gray-500"></span>
                    <span class="text-sm text-slate-600">NM</span>
                </div>
            </div>

            {{-- DAILY ROWS --}}
            <div class="space-y-5">
                @foreach ($this->dailyAttendance as $day)
                    @php
                        $total = (int) ($day->total ?? 0);
                        $rm = (int) ($day->rm_count ?? 0);
                        $lm = (int) ($day->lm_count ?? 0);
                        $tm = (int) ($day->tm_count ?? 0);
                        $nm = (int) ($day->nm_count ?? 0);

                        $rmWidth = $total > 0 ? ($rm / $total) * 100 : 0;
                        $lmWidth = $total > 0 ? ($lm / $total) * 100 : 0;
                        $tmWidth = $total > 0 ? ($tm / $total) * 100 : 0;
                        $nmWidth = $total > 0 ? ($nm / $total) * 100 : 0;
                    @endphp

                    <div
                        wire:key="daily-attendance-{{ $day->date }}"
                        class="rounded-xl border border-slate-200 p-4"
                    >
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($day->date)->format('D, M d, Y') }}
                            </p>

                            <p class="text-sm font-bold text-slate-900">
                                {{ $total }} total
                            </p>
                        </div>

                        {{-- STACKED ATTENDANCE BAR --}}
                        <div class="flex h-3 w-full overflow-hidden rounded-full bg-slate-100">
                            @if ($rmWidth > 0)
                                <div
                                    class="h-full bg-blue-600"
                                    style="width: {{ $rmWidth }}%"
                                    title="RM: {{ $rm }}"
                                ></div>
                            @endif

                            @if ($lmWidth > 0)
                                <div
                                    class="h-full bg-purple-600"
                                    style="width: {{ $lmWidth }}%"
                                    title="LM: {{ $lm }}"
                                ></div>
                            @endif

                            @if ($tmWidth > 0)
                                <div
                                    class="h-full bg-amber-500"
                                    style="width: {{ $tmWidth }}%"
                                    title="TM: {{ $tm }}"
                                ></div>
                            @endif

                            @if ($nmWidth > 0)
                                <div
                                    class="h-full bg-gray-500"
                                    style="width: {{ $nmWidth }}%"
                                    title="NM: {{ $nm }}"
                                ></div>
                            @endif
                        </div>

                        {{-- SEPARATE DAILY COUNTS --}}
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-lg bg-blue-50 px-3 py-2">
                                <p class="text-xs font-medium text-blue-700">
                                    Regular Member (RM)
                                </p>

                                <p class="mt-1 text-xl font-bold text-blue-800">
                                    {{ $rm }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-purple-50 px-3 py-2">
                                <p class="text-xs font-medium text-purple-700">
                                    Life Member (LM)
                                </p>

                                <p class="mt-1 text-xl font-bold text-purple-800">
                                    {{ $lm }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-amber-50 px-3 py-2">
                                <p class="text-xs font-medium text-amber-700">
                                    Trainee Member (TM)
                                </p>

                                <p class="mt-1 text-xl font-bold text-amber-800">
                                    {{ $tm }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                <p class="text-xs font-medium text-gray-600">
                                    Non-Member (NM)
                                </p>

                                <p class="mt-1 text-xl font-bold text-gray-800">
                                    {{ $nm }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        @keyframes summary-progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</div>