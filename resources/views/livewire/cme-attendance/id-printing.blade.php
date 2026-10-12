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
                        Verify CME registration before printing an ID.
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

        {{-- PARTICIPANT SEARCH --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Find Participant
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Scan or enter the PSA ID / participant ID.
            </p>

            <form wire:submit="searchMember" class="mt-6">

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

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="searchMember"
                    class="mt-4 flex w-full items-center justify-center rounded-xl bg-blue-700 px-5 py-4 font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="searchMember">
                        Check Registration
                    </span>

                    <span wire:loading wire:target="searchMember">
                        Checking Registration...
                    </span>
                </button>

            </form>


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
                    <li>• Scan the participant's PSA ID.</li>
                    <li>• Registration is checked against the selected CME program.</li>
                    <li>• Only registered participants can print an ID.</li>
                    <li>• The QR code contains the participant ID.</li>
                </ul>
            </div>

        </section>


        {{-- ID PREVIEW --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

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


            @if ($displayMemberId)

                {{-- PRINTABLE ARTWORK --}}
                <div
                    id="printable-id"
                    class="id-card-canvas"
                >

                    {{-- ORIGINAL BACKGROUND --}}
                    <img
                        src="{{ asset('images/ac2026-delegate.png') }}"
                        alt="Annual Convention 2026 Delegate ID"
                        class="id-card-background"
                    >


                    {{-- TRANSPARENT QR CODE --}}
                    @if ($qrCode)
                        <div class="id-qr">
                            <img
                                src="data:image/svg+xml;base64,{{ $qrCode }}"
                                alt="QR code for participant {{ $displayMemberId }}"
                            >
                        </div>
                    @endif


                    {{-- MAIN NAME: HORIZONTAL --}}
                    <div class="id-main-name">
                        <span>
                            {{ $displayFirstName }}
                            @if ($displayMiddleName)
                                {{ $displayMiddleName }}
                            @endif
                            {{ $displayLastName }}, MD
                        </span>
                    </div>


                    {{-- PSA ID: BELOW QR --}}
                    <div class="id-main-psa">
                        <span>PSA ID:</span>
                        <strong>{{ $displayMemberId }}</strong>
                    </div>


                    {{-- RAFFLE NAME: HORIZONTAL --}}
                    <div class="id-raffle-name">
                        <span>
                            {{ $displayFirstName }}
                            @if ($displayMiddleName)
                                {{ $displayMiddleName }}
                            @endif
                            {{ $displayLastName }}, MD
                        </span>
                    </div>


                    {{-- RAFFLE REFERENCE: HORIZONTAL --}}
                    <div class="id-raffle-reference">
                        {{ $displayMemberId }} | {{ $referenceNumber }}
                    </div>

                </div>


                {{-- PRINT --}}
                <button
                    type="button"
                    onclick="printId()"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-4 font-bold text-white transition hover:bg-emerald-700"
                >
                    <span>🖨</span>
                    Print ID
                </button>


                {{-- CLEAR --}}
                <button
                    type="button"
                    wire:click="clear"
                    class="mt-2 w-full rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Clear
                </button>

            @else

                {{-- EMPTY PREVIEW --}}
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

        </section>

    </div>

</div>


{{-- PRINT SCRIPT --}}
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
            alert('Please allow pop-ups for this page so the ID can be printed.');
            return;
        }

        const cssUrl = @js(asset('css/id-printing.css'));

        /*
         * Only override the physical print dimensions here.
         * Text positions, alignment, font sizes, and artwork positioning
         * come from id-printing.css, just like the on-screen preview.
         */
        const printOverrides = `
            <style>
                /* MAIN PARTICIPANT NAME */
                .id-main-name {
                    left: 17% !important;
                    top: 27% !important;
                    width: 31.5% !important;
                    height: 45% !important;

                    font-family: Aptos, "Aptos Display", "Segoe UI", Arial, sans-serif !important;
                    font-size: 35   px !important;
                    font-weight: 700 !important;

                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    text-align: center !important;
                }

                /* PSA ID */
                .id-main-psa {
                    left: 1.7% !important;
                    top: 66.8% !important;
                    width: 13.5% !important;

                    font-family: Aptos, "Aptos Display", "Segoe UI", Arial, sans-serif !important;
                    font-size: 17px !important;
                    text-align: center !important;
                }

                .id-main-psa strong {
                    font-size: 17px !important;
                    font-weight: 700 !important;
                }

                /* RAFFLE STUB NAME */
                .id-raffle-name {
                    left: 52% !important;
                    top: 69% !important;
                    width: 21.5% !important;
                    height: 12% !important;

                    font-family: Aptos, "Aptos Display", "Segoe UI", Arial, sans-serif !important;
                    font-size: 14px !important;
                    font-weight: 700 !important;

                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    text-align: center !important;
                }

                /* RAFFLE REFERENCE NUMBER */
                .id-raffle-reference {
                    left: 52% !important;
                    top: 79.5% !important;
                    width: 21.5% !important;

                    font-family: Aptos, "Aptos Display", "Segoe UI", Arial, sans-serif !important;
                    font-size: 12px !important;
                    font-weight: 600 !important;
                    text-align: center !important;
                }
                    
                @page {
                    size: 267mm 100mm;
                    margin: 0;
                }

                html,
                body {
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 267mm !important;
                    height: 100mm !important;
                    overflow: hidden !important;
                    background: #fff;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                body {
                    display: block !important;
                }

                #printable-id {
                    width: 267mm !important;
                    height: 100mm !important;
                    max-width: none !important;
                    aspect-ratio: auto !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow: hidden !important;
                    position: relative !important;
                    background: #fff;
                    font-family: Aptos, "Aptos Display", "Segoe UI", Arial, sans-serif;
                }

                #printable-id > .id-card-background {
                    position: absolute !important;
                    inset: 0 !important;
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: fill !important;
                }

                /*
                 * Use physical print dimensions without changing
                 * the CSS percentage-based text alignment.
                 */
                #printable-id .id-qr,
                #printable-id .id-main-name,
                #printable-id .id-main-psa,
                #printable-id .id-raffle-name,
                #printable-id .id-raffle-reference {
                    box-sizing: border-box;
                }
            </style>
        `;

        printWindow.document.open();

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>PSA Annual Convention 2026 ID</title>
                <link rel="stylesheet" href="${cssUrl}">
                ${printOverrides}
            </head>
            <body>
                ${idCard.outerHTML}
            </body>
            </html>
        `);

        printWindow.document.close();

        /*
         * Wait for the external stylesheet and images before printing.
         */
        const waitForStylesheet = Array.from(
            printWindow.document.styleSheets
        ).length;

        const images = Array.from(printWindow.document.images);

        const waitForImages = Promise.all(
            images.map(image => {
                if (image.complete) {
                    return Promise.resolve();
                }

                return new Promise(resolve => {
                    image.onload = resolve;
                    image.onerror = resolve;
                });
            })
        );

        const waitForCss = new Promise(resolve => {
            const link = printWindow.document.querySelector(
                'link[rel="stylesheet"]'
            );

            if (!link) {
                resolve();
                return;
            }

            link.onload = resolve;
            link.onerror = resolve;

            if (waitForStylesheet > 0 && link.sheet) {
                resolve();
            }
        });

        Promise.all([waitForImages, waitForCss]).then(() => {
            printWindow.focus();

            setTimeout(() => {
                printWindow.print();
            }, 400);
        });

        printWindow.onafterprint = () => {
            printWindow.close();
        };
    };
</script>
@endscript