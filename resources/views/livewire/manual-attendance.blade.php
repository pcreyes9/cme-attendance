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
                        Manual Attendance
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


    {{-- Main --}}
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Heading --}}
        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-900">
                Manual Attendance
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Add an attendance record manually for a registered CME participant.
            </p>

        </div>


        {{-- Success Message --}}
        @if (session()->has('success'))

            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3">

                <div class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>

            </div>

        @endif


        {{-- Program Information --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">

            <div class="grid grid-cols-2 gap-6">

                <div>

                    <div class="text-xs uppercase tracking-wide text-gray-500">
                        CME Year
                    </div>

                    <div class="mt-1 font-semibold text-gray-900">
                        {{ $cmeYear }}
                    </div>

                </div>

                <div>

                    <div class="text-xs uppercase tracking-wide text-gray-500">
                        Program
                    </div>

                    <div class="mt-1 font-semibold text-gray-900">
                        {{ $cmeProgramCode }}
                    </div>

                </div>

            </div>

        </div>


        {{-- Form --}}
        <div class="bg-white rounded-xl border border-gray-200">

            {{-- Form Header --}}
            <div class="px-6 py-5 border-b border-gray-200">

                <h3 class="font-semibold text-gray-900">
                    Attendance Information
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Select a registered member or non-member and enter the attendance details.
                </p>

            </div>


            <div class="p-6 space-y-6">

                {{-- ========================================================= --}}
                {{-- PARTICIPANT --}}
                {{-- ========================================================= --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Registered Participant
                    </label>


                    {{-- Selected Participant --}}
                    @if ($selectedMemberId)

                        <div class="border border-green-200 bg-green-50 rounded-lg p-4">

                            <div class="flex items-center justify-between">

                                <div>

                                    <div class="font-semibold text-gray-900">

                                        {{ $this->selectedMember->mem_last_name }},
                                        {{ $this->selectedMember->mem_first_name }}
                                        {{ $this->selectedMember->mem_middle_name }}

                                    </div>


                                    <div class="text-sm text-gray-600 mt-1">

                                        ID:
                                        {{ $this->selectedMember->member_id_no }}

                                    </div>


                                    <div class="text-sm text-gray-600">

                                        Participant Type:

                                        @if ($this->selectedMember->psa_mem_type === 'NM')

                                            <span class="font-semibold text-purple-700">
                                                NM
                                            </span>

                                        @else

                                            <span class="font-semibold">
                                                {{ $this->selectedMember->psa_mem_type }}
                                            </span>

                                        @endif

                                    </div>

                                </div>


                                <button
                                    type="button"
                                    wire:click="clearMember"
                                    class="text-sm font-medium text-red-600 hover:text-red-800"
                                >
                                    Change
                                </button>

                            </div>

                        </div>

                    @else

                        {{-- Search --}}
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search Member ID, NM ID, or name..."
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            autocomplete="off"
                        >


                        {{-- Search Results --}}
                        @if ($search !== '')

                            <div class="mt-2 border border-gray-200 rounded-lg bg-white shadow-sm overflow-hidden">

                                @forelse ($this->members as $member)

                                    <button
                                        type="button"
                                        wire:click="selectMember('{{ $member->id }}')"
                                        class="w-full px-4 py-3 text-left hover:bg-gray-50 border-b border-gray-100 last:border-0"
                                    >

                                        <div class="flex items-center justify-between gap-4">

                                            <div>

                                                <div class="font-medium text-gray-900">

                                                    {{ $member->last_name }},
                                                    {{ $member->first_name }}
                                                    {{ $member->middle_name }}

                                                </div>

                                                <div class="text-xs text-gray-500 mt-1">

                                                    ID:
                                                    {{ $member->id }}

                                                </div>

                                            </div>


                                            {{-- Member Type --}}
                                            @if ($member->mem_type === 'NM')

                                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                                    NM
                                                </span>

                                            @else

                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                    {{ $member->mem_type }}
                                                </span>

                                            @endif

                                        </div>

                                    </button>

                                @empty

                                    <div class="px-4 py-4 text-sm text-gray-500">

                                        No registered participants found.

                                    </div>

                                @endforelse

                            </div>

                        @endif

                    @endif


                    @error('selectedMemberId')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ========================================================= --}}
                {{-- DATE AND TIME --}}
                {{-- ========================================================= --}}

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Date --}}
                    <div>

                        <label
                            for="attendanceDate"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Attendance Date
                        </label>

                        <input
                            id="attendanceDate"
                            type="date"
                            wire:model="attendanceDate"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('attendanceDate')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Time --}}
                    <div>

                        <label
                            for="timeIn"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Time In
                        </label>

                        <input
                            id="timeIn"
                            type="time"
                            wire:model="timeIn"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('timeIn')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- REMARKS --}}
                {{-- ========================================================= --}}

                <div>

                    <label
                        for="remarks"
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Remarks
                    </label>

                    <textarea
                        id="remarks"
                        wire:model="remarks"
                        rows="3"
                        maxlength="500"
                        placeholder="Optional remarks..."
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    ></textarea>

                    @error('remarks')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FOOTER --}}
            {{-- ========================================================= --}}

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">

                <button
                    type="button"
                    wire:click="resetForm"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg"
                >
                    Clear
                </button>


                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg disabled:opacity-50"
                >

                    <span wire:loading.remove wire:target="save">
                        Save Attendance
                    </span>

                    <span wire:loading wire:target="save">
                        Saving...
                    </span>

                </button>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- NOTE --}}
        {{-- ========================================================= --}}

        <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">

            <p class="text-sm text-blue-800">

                <strong>Note:</strong>

                Only participants registered for
                <strong>{{ $cmeProgramCode }}</strong>
                are available for manual attendance.

                This includes both
                <strong>PSA members</strong>
                and
                <strong>Non-Members (NM)</strong>.

            </p>

        </div>

    </main>

</div>