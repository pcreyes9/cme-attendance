<div class="min-h-screen bg-slate-100 p-6">

    {{-- ================================================================ --}}
    {{-- HEADER --}}
    {{-- ================================================================ --}}

    <div class="mx-auto mb-6 max-w-6xl">

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                {{-- TITLE --}}
                <div>

                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">
                        Philippine Society of Anesthesiologists
                    </p>

                    <h1 class="mt-1 text-2xl font-bold text-slate-900">
                        CME ID Printing
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Verify CME registration before printing an ID.
                    </p>

                </div>


                {{-- PROGRAM --}}
                <div class="rounded-xl bg-slate-50 px-5 py-3 text-right">

                    <p class="text-xs text-slate-500">
                        CME Program
                    </p>

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


    {{-- ================================================================ --}}
    {{-- MAIN CONTENT --}}
    {{-- ================================================================ --}}

    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 lg:grid-cols-2">


        {{-- ============================================================ --}}
        {{-- FIND PARTICIPANT --}}
        {{-- ============================================================ --}}

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Find Participant
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Scan or enter the PSA ID / participant ID.
            </p>


            {{-- ======================================================== --}}
            {{-- SEARCH FORM --}}
            {{-- ======================================================== --}}

            <form
                wire:submit="searchMember"
                class="mt-6"
            >

                <label
                    for="memberId"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Participant ID
                </label>


                <input
                    id="memberId"
                    type="text"
                    wire:model="memberId"
                    autofocus
                    autocomplete="off"
                    placeholder="Scan or enter ID..."
                    class="w-full rounded-xl border border-slate-300 px-4 py-4 text-lg font-semibold uppercase outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                >


                {{-- CHECK REGISTRATION --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="mt-4 flex w-full items-center justify-center rounded-xl bg-blue-700 px-5 py-4 font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                >

                    <span wire:loading.remove>
                        Check Registration
                    </span>

                    <span wire:loading>
                        Checking Registration...
                    </span>

                </button>

            </form>


            {{-- ======================================================== --}}
            {{-- MESSAGE --}}
            {{-- ======================================================== --}}

            @if ($message)

                <div
                    class="mt-5 rounded-xl border p-4
                    {{
                        $messageType === 'success'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                            : 'border-red-200 bg-red-50 text-red-800'
                    }}"
                >

                    <div class="flex items-start gap-3">

                        @if ($messageType === 'success')

                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                ✓
                            </div>

                        @else

                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 font-bold text-red-700">
                                !
                            </div>

                        @endif


                        <div class="text-sm font-medium">
                            {{ $message }}
                        </div>

                    </div>

                </div>

            @endif


            {{-- ======================================================== --}}
            {{-- INSTRUCTIONS --}}
            {{-- ======================================================== --}}

            <div class="mt-6 rounded-xl bg-slate-50 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Instructions
                </p>

                <ul class="mt-2 space-y-1 text-xs text-slate-500">

                    <li>
                        • Scan the participant's PSA ID.
                    </li>

                    <li>
                        • Registration will be checked automatically.
                    </li>

                    <li>
                        • Only registered participants can print an ID.
                    </li>

                    <li>
                        • The QR code contains the participant ID.
                    </li>

                </ul>

            </div>

        </div>


        {{-- ============================================================ --}}
        {{-- ID PREVIEW --}}
        {{-- ============================================================ --}}

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <div class="mb-5 flex items-center justify-between">

                <div>

                    <h2 class="text-lg font-bold text-slate-900">
                        ID Preview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Annual Convention 2026 Delegate
                    </p>

                </div>


                @if ($displayMemberId)

                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Registered
                    </span>

                @endif

            </div>


            {{-- ======================================================== --}}
            {{-- REGISTERED PARTICIPANT --}}
            {{-- ======================================================== --}}

            @if ($displayMemberId)

                <div
                    id="printable-id"
                    class="id-card-canvas"
                >

                    {{-- ================================================= --}}
                    {{-- CONVENTION BACKGROUND --}}
                    {{-- ================================================= --}}

                    <img
                        src="{{ asset('images/ac2026-delegate.png') }}"
                        alt="Annual Convention 2026 Delegate ID"
                        class="absolute inset-0 h-full w-full"
                    >


                    {{-- ================================================= --}}
                    {{-- QR CODE --}}
                    {{-- ================================================= --}}

                    @if ($qrCode)

                        <div class="id-qr">

                            <img
                                src="data:image/svg+xml;base64,{{ $qrCode }}"
                                alt="PSA ID QR Code"
                            >

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- MAIN MEMBER NAME --}}
                    {{-- ================================================= --}}

                    <div class="id-main-name">

                        <span>
                            {{ $displayFirstName }}

                            @if ($displayMiddleName)
                                {{ $displayMiddleName }}
                            @endif

                            {{ $displayLastName }}, MD
                        </span>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MAIN PSA ID --}}
                    {{-- ================================================= --}}

                    <div class="id-main-psa">

                        <span>
                            PSA ID:
                        </span>
                        {{ $displayMemberId }}

                    </div>


                    {{-- ================================================= --}}
                    {{-- RAFFLE STUB NAME --}}
                    {{-- ================================================= --}}

                    <div class="id-raffle-name">

                        <span>
                            {{ $displayFirstName }}

                            @if ($displayMiddleName)
                                {{ $displayMiddleName }}
                            @endif

                            {{ $displayLastName }}
                        </span>

                    </div>


                    {{-- ================================================= --}}
                    {{-- RAFFLE STUB REFERENCE --}}
                    {{-- ================================================= --}}

                    <div class="id-raffle-reference">

                        {{ $displayMemberId }}

                        <span>
                            |
                        </span>

                        {{ $referenceNumber }}

                    </div>

                </div>


                {{-- ==================================================== --}}
                {{-- PRINT BUTTON --}}
                {{-- ==================================================== --}}

                <button
                    type="button"
                    onclick="printId()"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-4 font-bold text-white transition hover:bg-emerald-700"
                >

                    <span>
                        🖨
                    </span>

                    Print ID

                </button>


                {{-- ==================================================== --}}
                {{-- CLEAR BUTTON --}}
                {{-- ==================================================== --}}

                <button
                    type="button"
                    wire:click="clear"
                    class="mt-2 w-full rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Clear
                </button>


            @else

                {{-- ==================================================== --}}
                {{-- EMPTY PREVIEW --}}
                {{-- ==================================================== --}}

                <div class="flex min-h-[400px] items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50">

                    <div class="text-center">

                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-3xl shadow-sm">
                            🪪
                        </div>

                        <p class="mt-4 font-semibold text-slate-700">
                            No participant selected
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Scan or enter an ID to check registration.
                        </p>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>


{{-- ==================================================================== --}}
{{-- PRINT SCRIPT --}}
{{-- ==================================================================== --}}

@script

<script>

    window.printId = function () {

        const idCard = document.getElementById('printable-id');

        if (!idCard) {
            return;
        }


        const printWindow = window.open(
            '',
            '_blank',
            'width=1400,height=700'
        );


        if (!printWindow) {

            alert(
                'Please allow pop-ups for this page so the ID can be printed.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Print CSS
        |--------------------------------------------------------------------------
        */

        const printStyles = `
            <style>

                @page {

                    size: 267mm 100mm;

                    margin: 0;

                }


                * {

                    box-sizing: border-box;

                }


                html,
                body {

                    margin: 0;

                    padding: 0;

                    width: 267mm;

                    height: 100mm;

                    overflow: hidden;

                    background: #fff;

                    -webkit-print-color-adjust: exact !important;

                    print-color-adjust: exact !important;

                }


                body {

                    display: flex;

                    align-items: flex-start;

                    justify-content: flex-start;

                }


                #printable-id {

                    position: relative;

                    width: 267mm;

                    height: 100mm;

                    margin: 0;

                    padding: 0;

                    overflow: hidden;

                    background: #fff;

                    font-family:
                        Aptos,
                        "Aptos Display",
                        "Segoe UI",
                        Arial,
                        sans-serif;

                }


                #printable-id > img {

                    position: absolute;

                    left: 0;

                    top: 0;

                    width: 100%;

                    height: 100%;

                    object-fit: fill;

                }


                /*
                ------------------------------------------------------------
                QR
                ------------------------------------------------------------
                */

                .id-qr {

                    position: absolute;

                    left: 3.7%;

                    top: 42%;

                    width: 12.5%;

                    aspect-ratio: 1 / 1;

                    background: #fff;

                    padding: 0.35%;

                }


                .id-qr img {

                    display: block;

                    width: 100%;

                    height: 100%;

                }


                /*
                ------------------------------------------------------------
                Main name
                ------------------------------------------------------------
                */

                .id-main-name {

                    position: absolute;

                    left: 18%;

                    top: 41%;

                    width: 29%;

                    height: 18%;

                    display: flex;

                    align-items: center;

                    justify-content: center;

                    text-align: center;

                    font-family:
                        Aptos,
                        "Aptos Display",
                        "Segoe UI",
                        Arial,
                        sans-serif;

                    font-size: 28px;

                    font-weight: 700;

                    line-height: 1.08;

                    text-transform: uppercase;

                    color: #000;

                    overflow: hidden;

                }


                /*
                ------------------------------------------------------------
                PSA ID
                ------------------------------------------------------------
                */

                .id-main-psa {

                    position: absolute;

                    left: 3.7%;

                    top: 78%;

                    width: 12.5%;

                    text-align: center;

                    font-family:
                        Aptos,
                        "Aptos Display",
                        "Segoe UI",
                        Arial,
                        sans-serif;

                    font-size: 13px;

                    font-weight: 600;

                    line-height: 1.2;

                    color: #000;

                }


                .id-main-psa strong {

                    display: block;

                    margin-top: 2px;

                    font-size: 17px;

                    font-weight: 700;

                }


                /*
                ------------------------------------------------------------
                Raffle name
                ------------------------------------------------------------
                */

                .id-raffle-name {

                    position: absolute;

                    left: 50.7%;

                    top: 68.5%;

                    width: 22.5%;

                    height: 9%;

                    display: flex;

                    align-items: center;

                    justify-content: center;

                    text-align: center;

                    font-family:
                        Aptos,
                        "Aptos Display",
                        "Segoe UI",
                        Arial,
                        sans-serif;

                    font-size: 15px;

                    font-weight: 700;

                    line-height: 1.05;

                    text-transform: uppercase;

                    color: #000;

                    overflow: hidden;

                }


                /*
                ------------------------------------------------------------
                Raffle reference
                ------------------------------------------------------------
                */

                .id-raffle-reference {

                    position: absolute;

                    left: 50.7%;

                    top: 82.5%;

                    width: 22.5%;

                    text-align: center;

                    font-family:
                        Aptos,
                        "Aptos Display",
                        "Segoe UI",
                        Arial,
                        sans-serif;

                    font-size: 11px;

                    font-weight: 600;

                    line-height: 1.1;

                    color: #000;

                }

            </style>
        `;


        printWindow.document.open();


        printWindow.document.write(`
            <!DOCTYPE html>

            <html>

            <head>

                <meta charset="UTF-8">

                <title>
                    PSA Annual Convention 2026 ID
                </title>

                ${printStyles}

            </head>


            <body>

                ${idCard.outerHTML}

            </body>

            </html>
        `);


        printWindow.document.close();


        /*
        |--------------------------------------------------------------------------
        | Wait for images
        |--------------------------------------------------------------------------
        */

        const images = printWindow.document.images;

        if (images.length === 0) {

            printWindow.focus();

            setTimeout(function () {

                printWindow.print();

                printWindow.close();

            }, 300);

            return;
        }


        let loaded = 0;


        function imageLoaded() {

            loaded++;

            if (loaded >= images.length) {

                printWindow.focus();

                setTimeout(function () {

                    printWindow.print();

                    printWindow.close();

                }, 300);

            }

        }


        Array.from(images).forEach(function (image) {

            if (image.complete) {

                imageLoaded();

            } else {

                image.onload = imageLoaded;

                image.onerror = imageLoaded;

            }

        });

    };

</script>

@endscript