
<div class="min-h-screen bg-slate-100 p-6">

    {{-- PAGE HEADER --}}
    <div class="mx-auto mb-6 max-w-6xl">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">
                        Philippine Society of Anesthesiologists
                    </p>

                    <h1 class="mt-1 text-2xl font-bold text-slate-900">
                        CME ID Printing
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Search registered participants or manually enter a name.
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 px-5 py-3 text-right">
                    <p class="text-xs text-slate-500">CME Program</p>
                    <p class="text-lg font-bold text-slate-900">
                        {{ $cmeProgramCode }}
                    </p>
                    <p class="text-xs text-slate-500">
                        Year {{ $cmeYear }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- SEARCH AND MANUAL ENTRY --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Find Participant
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Search using a PSA ID or participant name.
            </p>

            {{-- SEARCH FORM --}}
            <form wire:submit="searchMember" class="mt-6">

                <label for="memberId" class="mb-2 block text-sm font-semibold text-slate-700">
                    PSA ID or Participant Name
                </label>

                <input
                    id="memberId"
                    type="text"
                    wire:model="memberId"
                    autocomplete="off"
                    placeholder="Scan PSA ID or enter a name..."
                    class="w-full rounded-xl border border-slate-300 px-4 py-4 text-lg font-semibold outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="searchMember"
                    class="mt-4 flex w-full items-center justify-center rounded-xl bg-blue-700 px-5 py-4 font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="searchMember">
                        Search Participant
                    </span>

                    <span wire:loading wire:target="searchMember">
                        Searching...
                    </span>
                </button>

            </form>

            {{-- SEARCH RESULTS --}}
            @if (count($searchResults) > 0)

                <div class="mt-6">

                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">
                            Search Results
                        </h3>

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            {{ count($searchResults) }} result(s)
                        </span>
                    </div>

                    <div class="max-h-96 space-y-2 overflow-y-auto">

                        @foreach ($searchResults as $result)

                            <button
                                type="button"
                                wire:key="participant-{{ $result['type'] }}-{{ $result['id'] }}"
                                wire:click="selectParticipant('{{ $result['type'] }}', '{{ $result['id'] }}')"
                                wire:loading.attr="disabled"
                                wire:target="selectParticipant"
                                class="w-full rounded-xl border border-slate-200 p-4 text-left transition hover:border-blue-400 hover:bg-blue-50 disabled:opacity-50"
                            >

                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0">
                                        <p class="font-bold uppercase text-slate-900">
                                            {{ $result['name'] }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            ID: {{ $result['id'] }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $result['member_type'] }}

                                            @if ($result['chapter'] !== 'N/A')
                                                &bull; {{ $result['chapter'] }}
                                            @endif
                                        </p>
                                    </div>

                                    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $result['type'] === 'member'
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-amber-100 text-amber-700' }}">
                                        {{ $result['type'] === 'member' ? 'Member' : 'Non-Member' }}
                                    </span>

                                </div>

                            </button>

                        @endforeach

                    </div>

                </div>

            @endif

            {{-- DIVIDER --}}
            <div class="my-7 flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-200"></div>

                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    OR
                </span>

                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            {{-- MANUAL NAME ENTRY --}}
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">

                <div class="mb-4">
                    <h3 class="font-bold text-slate-900">
                        Manual Name Entry
                    </h3>

                    <p class="mt-1 text-sm text-slate-600">
                        Use this for participants who need an ID printed using their name only.
                        No QR code, PSA ID, or reference number will be printed.
                    </p>
                </div>

                <form wire:submit="printManualEntry">

                    <label for="manualName" class="mb-2 block text-sm font-semibold text-slate-700">
                        Participant Name
                    </label>

                    <input
                        id="manualName"
                        type="text"
                        wire:model="manualName"
                        autocomplete="off"
                        maxlength="255"
                        placeholder="Enter full name..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base font-semibold uppercase outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                    >

                    @error('manualName')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="printManualEntry"
                        class="mt-4 flex w-full items-center justify-center rounded-xl bg-amber-600 px-5 py-3 font-bold text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="printManualEntry">
                            Prepare Manual ID
                        </span>

                        <span wire:loading wire:target="printManualEntry">
                            Preparing...
                        </span>
                    </button>

                </form>

            </div>

            {{-- STATUS MESSAGE --}}
            @if ($message)

                <div
                    role="status"
                    class="mt-5 rounded-xl border p-4 {{
                        $messageType === 'success'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                            : 'border-red-200 bg-red-50 text-red-800'
                    }}"
                >
                    <div class="flex items-start gap-3">

                        @if ($messageType === 'success')
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                ✓
                            </span>
                        @else
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 font-bold text-red-700">
                                !
                            </span>
                        @endif

                        <div class="text-sm font-medium">
                            {{ $message }}
                        </div>

                    </div>
                </div>

            @endif

            {{-- INSTRUCTIONS --}}
            <div class="mt-6 rounded-xl bg-slate-50 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Instructions
                </p>

                <ul class="mt-2 space-y-1 text-xs text-slate-500">
                    <li>• Scan or enter a registered participant's PSA ID.</li>
                    <li>• You can also search using the participant's first or last name.</li>
                    <li>• Select the correct participant from the search results.</li>
                    <li>• Manual entry prints only the name, without QR or ID details.</li>
                </ul>

            </div>

        </section>

        {{-- PARTICIPANT INFORMATION --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            {{-- HEADER --}}
            <div class="mb-6 flex items-center justify-between">

                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Participant Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Participant details and ID printing
                    </p>
                </div>

                @if ($displayMemberId && !$isManualEntry)
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Registered
                    </span>
                @elseif ($isManualEntry)
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        Manual Entry
                    </span>
                @endif

            </div>

            @if ($displayMemberId || $isManualEntry)

                {{-- PARTICIPANT NAME --}}
                <div class="rounded-xl bg-slate-50 p-5">

                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Participant Name
                    </p>

                    <h3 class="mt-2 text-2xl font-bold uppercase text-slate-900">
                        @if ($isManualEntry)
                            {{ $manualName }}
                        @else
                            {{ trim($displayFirstName . ' ' . ($displayMiddleName ?? '') . ' ' . $displayLastName) }}
                        @endif
                    </h3>

                </div>

                {{-- DETAILS --}}
                @if (!$isManualEntry)

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        {{-- MEMBERSHIP TYPE --}}
                        <div class="rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Membership Type
                            </p>

                            <p class="mt-2 text-xl font-bold text-slate-900">
                                {{ $displayMemberType ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- CHAPTER --}}
                        <div class="rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Chapter
                            </p>

                            <p class="mt-2 text-xl font-bold text-slate-900">
                                {{ $displayChapter ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- PSA ID --}}
                        <div class="rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                PSA ID Number
                            </p>

                            <p class="mt-2 text-xl font-bold text-slate-900">
                                {{ $displayMemberId }}
                            </p>
                        </div>

                        {{-- REFERENCE NUMBER --}}
                        <div class="rounded-xl border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Reference Number
                            </p>

                            <p class="mt-2 break-all text-xl font-bold tracking-wide text-slate-900">
                                {{ $referenceNumber ?? 'N/A' }}
                            </p>
                        </div>

                    </div>

                @else

                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">
                            Entry Type
                        </p>

                        <p class="mt-2 text-lg font-bold text-slate-900">
                            Manual Name Entry
                        </p>

                        <p class="mt-1 text-sm text-slate-600">
                            Only the participant's name will appear on the printed ID.
                        </p>
                    </div>

                @endif

                {{-- PRINT BUTTON --}}
                <button
                    type="button"
                    onclick="window.printId()"
                    class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-4 font-bold text-white transition hover:bg-emerald-700"
                >
                    <span>🖨</span>
                    Print ID
                </button>

                {{-- CLEAR BUTTON --}}
                <button
                    type="button"
                    wire:click="clear"
                    class="mt-2 w-full rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Clear
                </button>

                {{--
                |--------------------------------------------------------------------------
                | HIDDEN PRINT SOURCE
                |--------------------------------------------------------------------------
                | The print source is copied into a separate print window.
                | It is hidden from the main page.
                --}}
                <div
                    id="printable-id"
                    aria-hidden="true"
                    style="
                        position: fixed;
                        left: -20000px;
                        top: 0;
                        width: 14in;
                        height: 8.5in;
                        overflow: hidden;
                        background: #ffffff;
                        color: #000000;
                        pointer-events: none;
                        z-index: -1;
                    "
                >

                    {{-- QR CODE: REGISTERED PARTICIPANTS ONLY --}}
                    @if (!$isManualEntry && $qrCode)

                        <div class="id-qr">
                            <img
                                src="data:image/svg+xml;base64,{{ $qrCode }}"
                                alt="Participant QR Code"
                            >
                        </div>

                    @endif

                    {{-- PSA ID: REGISTERED PARTICIPANTS ONLY --}}
                    @if (!$isManualEntry)

                        <div class="id-main-psa">
                            <span>PSA ID:</span>
                            <strong>{{ $displayMemberId }}</strong>
                        </div>

                    @endif

                    {{-- MAIN PARTICIPANT NAME --}}
                    <div class="id-main-name {{ $isManualEntry ? 'id-manual-name' : '' }}">
                        <span>
                            @if ($isManualEntry)
                                {{ $manualName }}
                            @else
                                {{ trim($displayFirstName . ' ' . ($displayMiddleName ?? '') . ' ' . $displayLastName) }}, MD
                            @endif
                        </span>
                    </div>

                    {{-- RAFFLE STUB --}}
                    {{-- ONLY ONE RAFFLE CONTAINER --}}
                    {{-- REGISTERED PARTICIPANTS ONLY --}}
                    <div class="raffle-stub-details">

                        @if (!$isManualEntry)

                            {{-- PARTICIPANT NAME --}}
                            <div class="id-raffle-name">
                                {{ trim($displayFirstName . ' ' . ($displayMiddleName ?? '') . ' ' . $displayLastName) }}
                            </div>

                            {{-- PSA ID AND REFERENCE NUMBER --}}
                            <div class="id-raffle-reference">
                                {{ $displayMemberId }} | {{ $referenceNumber }}
                            </div>

                        @endif

                    </div>

                </div>

            @else

                {{-- EMPTY STATE --}}
                <div class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50">

                    <div class="text-center">

                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-3xl shadow-sm">
                            👤
                        </div>

                        <p class="mt-4 font-semibold text-slate-700">
                            No participant selected
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Search for a registered participant or enter a name manually.
                        </p>

                    </div>

                </div>

            @endif

        </section>

    </div>

</div>


{{-- PRINT SCRIPT --}}
@script
<script>
    window.printId = function () {

        const printable = document.getElementById('printable-id');

        if (!printable) {
            alert('No participant ID is available for printing.');
            return;
        }

        const printWindow = window.open(
            '',
            '_blank',
            'width=1400,height=900'
        );

        if (!printWindow) {
            alert('Please allow pop-ups for this page so the ID can be printed.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PRINT STYLES
        |--------------------------------------------------------------------------
        | Keep the existing 14 x 8.5 inch measurements and element positions.
        */

        const printStyles = `
            <style>

                @page {
                    size: 14in 8.5in;
                    margin: 0;
                }

                * {
                    box-sizing: border-box;
                }

                html,
                body {
                    width: 14in !important;
                    height: 8.5in !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow: hidden !important;
                    background: #fff !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                body {
                    display: block !important;
                }

                /* PRINT SOURCE CONTAINER */

                #printable-id {
                    position: relative !important;
                    left: 0 !important;
                    top: 0 !important;
                    display: block !important;
                    width: 14in !important;
                    min-width: 14in !important;
                    max-width: 14in !important;
                    height: 8.5in !important;
                    min-height: 8.5in !important;
                    max-height: 8.5in !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow: hidden !important;
                    aspect-ratio: auto !important;
                    background: #fff !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    pointer-events: auto !important;
                    z-index: auto !important;
                }

                /* QR CODE */

                #printable-id .id-qr {
                    position: absolute !important;
                    left: 6.24in !important;
                    top: 3.60in !important;
                    width: 0.94in !important;
                    height: 0.94in !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    background: transparent !important;
                    z-index: 2 !important;
                }

                #printable-id .id-qr img {
                    display: block !important;
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: contain !important;
                }

                /* PSA MEMBER NUMBER */

                #printable-id .id-main-psa {
                    position: absolute !important;
                    left: 6.24in !important;
                    top: 4.65in !important;
                    width: 0.94in !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    color: #000 !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 11pt !important;
                    font-weight: 400 !important;
                    line-height: 1.1 !important;
                    text-align: center !important;
                    white-space: nowrap !important;
                    z-index: 2 !important;
                }

                #printable-id .id-main-psa strong {
                    margin-left: 2px !important;
                    font-size: inherit !important;
                    font-weight: 700 !important;
                }

                /* MAIN PARTICIPANT NAME */

                #printable-id .id-main-name {
                    position: absolute !important;
                    left: 7.25in !important;
                    top: 3.40in !important;
                    width: 2.62in !important;
                    height: 1.50in !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    overflow: hidden !important;
                    color: #000 !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 22pt !important;
                    font-weight: 700 !important;
                    line-height: 1.08 !important;
                    letter-spacing: 0 !important;
                    text-align: center !important;
                    text-transform: uppercase !important;
                    overflow-wrap: normal !important;
                    z-index: 2 !important;
                }

                #printable-id .id-main-name span {
                    display: block !important;
                    max-width: 100% !important;
                }

                /* MANUAL ENTRY - OCCUPY QR CODE + MAIN NAME AREA */

                #printable-id .id-main-name.id-manual-name {
                    left: 6.00in !important;
                    top: 3.40in !important;
                    width: 4.00in !important;
                    height: 1.50in !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0 0.10in !important;
                    font-size: 23pt !important;
                    font-weight: 700 !important;
                    text-align: center !important;
                    text-transform: uppercase !important;
                    overflow: hidden !important;
                }

                /* RAFFLE STUB CONTAINER */

                #printable-id .raffle-stub-details {
                    position: absolute !important;
                    left: 10.30in !important;
                    top: 4.70in !important;
                    width: 1.50in !important;
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    gap: 0.05in !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }

                /* RAFFLE STUB NAME */

                #printable-id .id-raffle-name {
                    position: relative !important;
                    display: block !important;
                    width: 100% !important;
                    height: auto !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    color: #000 !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 9pt !important;
                    font-weight: 700 !important;
                    line-height: 1.1 !important;
                    text-align: center !important;
                    text-transform: uppercase !important;
                    white-space: normal !important;
                    overflow-wrap: break-word !important;
                    word-break: normal !important;
                }

                /* RAFFLE STUB REFERENCE NUMBER */

                #printable-id .id-raffle-reference {
                    position: relative !important;
                    display: block !important;
                    width: 100% !important;
                    height: auto !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    color: #000 !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 7pt !important;
                    font-weight: 400 !important;
                    line-height: 1.2 !important;
                    text-align: center !important;
                    white-space: nowrap !important;
                }

            </style>
        `;

        /*
        |--------------------------------------------------------------------------
        | CREATE PRINT DOCUMENT
        |--------------------------------------------------------------------------
        */

        try {

            printWindow.document.open();

            printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                    <head>
                        <meta charset="UTF-8">

                        <meta
                            name="viewport"
                            content="width=device-width, initial-scale=1"
                        >

                        <title>PSA CME Participant ID</title>

                        ${printStyles}
                    </head>

                    <body>
                        ${printable.outerHTML}
                    </body>
                </html>
            `);

            printWindow.document.close();

        } catch (error) {

            console.error('Unable to create print document:', error);

            printWindow.close();

            alert('Unable to prepare the ID for printing.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | WAIT FOR IMAGES BEFORE PRINTING
        |--------------------------------------------------------------------------
        */

        const waitForImages = Promise.all(
            Array.from(printWindow.document.images).map((img) => {

                if (img.complete) {
                    return Promise.resolve();
                }

                return new Promise((resolve) => {

                    img.addEventListener('load', resolve, { once: true });
                    img.addEventListener('error', resolve, { once: true });

                });

            })
        );

        Promise.all([waitForImages])
            .then(() => {

                if (printWindow.closed) {
                    return;
                }

                printWindow.focus();

                printWindow.requestAnimationFrame(() => {

                    printWindow.requestAnimationFrame(() => {
                        printWindow.print();
                    });

                });

            })
            .catch((error) => {
                console.error('Print preparation error:', error);
            });

        printWindow.onafterprint = () => {
            printWindow.close();
        };

    };
</script>
@endscript
