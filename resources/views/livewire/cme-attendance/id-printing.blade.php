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
                    <p class="text-lg font-bold text-slate-900">{{ $cmeProgramCode }}</p>
                    <p class="text-xs text-slate-500">Year {{ $cmeYear }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- PARTICIPANT SEARCH --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900">Find Participant</h2>
            <p class="mt-1 text-sm text-slate-500">
                Scan or enter the PSA ID / participant ID.
            </p>

            <form wire:submit="searchMember" class="mt-6">
                <label for="memberId" class="mb-2 block text-sm font-semibold text-slate-700">
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

                        <div class="text-sm font-medium">{{ $message }}</div>
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
                    <h2 class="text-lg font-bold text-slate-900">ID Preview</h2>
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
                {{-- PRINTABLE CONTENT ONLY: NO BACKGROUND ARTWORK --}}
                <div id="printable-id" class="id-card-canvas" aria-label="Participant ID print layout">

                    {{-- TRANSPARENT QR CODE --}}
                    @if ($qrCode)
                        <div class="id-qr">
                            <img
                                src="data:image/svg+xml;base64,{{ $qrCode }}"
                                alt="QR code for participant {{ $displayMemberId }}"
                            >
                        </div>
                    @endif

                    {{-- MAIN PARTICIPANT NAME --}}
                    <div class="id-main-name">
                        <span>
                            {{ trim($displayFirstName . ' ' . ($displayMiddleName ?? '') . ' ' . $displayLastName) }}, MD
                        </span>
                    </div>

                    {{-- PSA ID BELOW QR CODE --}}
                    <div class="id-main-psa">
                        <span>PSA ID:</span>
                        <strong>{{ $displayMemberId }}</strong>
                    </div>

                    {{-- RAFFLE STUB NAME --}}
                    <div class="id-raffle-name">
                        <span>
                            {{ trim($displayFirstName . ' ' . ($displayMiddleName ?? '') . ' ' . $displayLastName) }}, MD
                        </span>
                    </div>

                    {{-- RAFFLE REFERENCE --}}
                    <div class="id-raffle-reference">
                        {{ $displayMemberId }} | {{ $referenceNumber }}
                    </div>
                </div>

                <button
                    type="button"
                    onclick="window.printId()"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-4 font-bold text-white transition hover:bg-emerald-700"
                >
                    <span>🖨</span>
                    Print ID
                </button>

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
        const printable = document.getElementById('printable-id');

        if (!printable) {
            return;
        }

        const printWindow = window.open('', '_blank', 'width=1400,height=900');

        if (!printWindow) {
            alert('Please allow pop-ups for this page so the ID can be printed.');
            return;
        }

        const cssUrl = @js(asset('css/id-printing.css'));

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

                #printable-id {
                    position: relative !important;
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
                }

                /* QR code: position measured from the supplied 14 x 8.5 in PDF. */
                #printable-id .id-qr {
                    position: absolute !important;
                    left: 6.24in !important;
                    top: 3.65in !important;
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

                 /* PSA member number directly below the QR code. */
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

                /* Main participant name, to the right of the QR code. */
                #printable-id .id-main-name {
                    position: absolute !important;
                    left: 7.25in !important;
                    top: 3.78in !important;
                    width: 2.62in !important;
                    height: 1.00in !important;
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

               
                /* Vertical raffle name at the far right of the printed group. */
                #printable-id .id-raffle-name {
                    position: absolute !important;
                    left: 10.30in !important;
                    top: 4.55in !important;
                    width: 1.40in !important;
                    height: 0.25in !important;

                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;

                    margin: 0 !important;
                    padding: 0 !important;

                    transform: none !important;

                    color: #000 !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 9pt !important;
                    font-weight: 700 !important;
                    line-height: 1 !important;
                    text-align: center !important;
                    text-transform: uppercase !important;
                    white-space: nowrap !important;
                }

                #printable-id .id-raffle-name span {
                    display: inline !important;
                }

                /* Vertical reference number */
                #printable-id .id-raffle-reference {
                    position: absolute !important;
                    left: 10.30in !important;
                    top: 4.75in !important;
                    width: 1.50in !important;
                    height: 0.20in !important;

                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;

                    margin: 0 !important;
                    padding: 0 !important;

                    transform: none !important;

                    color: #000 !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-size: 7pt !important;
                    font-weight: 400 !important;
                    line-height: 1 !important;
                    text-align: center !important;
                    white-space: nowrap !important;
                }
            </style>
        `;

        printWindow.document.open();
        printWindow.document.write(`
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>PSA CME Participant ID</title>
                <link rel="stylesheet" href="${cssUrl}">
                ${printStyles}
            </head>
            <body>
                ${printable.outerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();

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

        const stylesheet = printWindow.document.querySelector('link[rel="stylesheet"]');
        const waitForStylesheet = stylesheet
            ? new Promise((resolve) => {
                if (stylesheet.sheet) {
                    resolve();
                    return;
                }

                stylesheet.addEventListener('load', resolve, { once: true });
                stylesheet.addEventListener('error', resolve, { once: true });
            })
            : Promise.resolve();

        Promise.all([waitForImages, waitForStylesheet]).then(() => {
            printWindow.focus();
            printWindow.requestAnimationFrame(() => {
                printWindow.requestAnimationFrame(() => printWindow.print());
            });
        });

        printWindow.onafterprint = () => printWindow.close();
    };
</script>
@endscript