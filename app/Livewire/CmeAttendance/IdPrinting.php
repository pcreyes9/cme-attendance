<?php

namespace App\Livewire\CmeAttendance;

use App\Models\CmeProgramRegistration;
use App\Models\CmeProgramRegistrationNM;
use App\Models\Member;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IdPrinting extends Component
{
    public string $cmeYear = '2026';

    public string $cmeProgramCode = 'CME2026002';

    public string $memberId = '';

    public string $manualName = '';

    public bool $isManualEntry = false;

    public array $searchResults = [];

    public ?string $message = null;

    public ?string $messageType = null;

    public ?string $displayMemberId = null;

    public ?string $displayFirstName = null;

    public ?string $displayLastName = null;

    public ?string $displayMiddleName = null;

    public ?string $displayMemberType = null;

    public ?string $displayChapter = null;

    public ?string $referenceNumber = null;

    public ?string $qrCode = null;


    /*
    |--------------------------------------------------------------------------
    | AUTOMATIC PSA ID SEARCH
    |--------------------------------------------------------------------------
    |
    | Automatically searches when the user enters a numeric PSA ID.
    | Name searches are handled separately through searchMember().
    |
    */

    public function updatedMemberId($value): void
    {
        $search = trim((string) $value);

        $this->resetDisplay();
        $this->searchResults = [];
        $this->isManualEntry = false;
        $this->manualName = '';
        $this->resetMessage();

        // Only automatically search numeric PSA IDs.
        if ($search === '' || !ctype_digit($search)) {
            return;
        }

        $this->lookupParticipantById($search);
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH PARTICIPANTS BY NAME
    |--------------------------------------------------------------------------
    |
    | Called only when the Search button is clicked.
    | Numeric IDs are handled separately.
    |
    */

    public function searchMember(): void
    {
        $this->resetDisplay();

        $this->isManualEntry = false;
        $this->manualName = '';
        $this->searchResults = [];

        $this->resetMessage();

        $search = trim($this->memberId);

        if ($search === '') {
            $this->showMessage(
                'Enter a PSA ID or participant name to search.',
                'error'
            );

            return;
        }

        // Numeric input should use exact ID lookup.
        if (ctype_digit($search)) {
            $this->lookupParticipantById($search);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH REGISTERED MEMBERS
        |--------------------------------------------------------------------------
        */

        $members = Member::query()
            ->select([
                'member_id_no',
                'mem_first_name',
                'mem_middle_name',
                'mem_last_name',
                'psa_mem_type',
                'psa_chapter_code',
            ])
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('cme_program_registration as cpr')
                    ->whereColumn(
                        'cpr.member_id_no',
                        'member.member_id_no'
                    )
                    ->where('cpr.cme_year', $this->cmeYear)
                    ->where('cpr.cme_program_code', $this->cmeProgramCode);
            })
            ->where(function ($query) use ($search) {
                $query->where('mem_first_name', 'like', $search . '%')
                    ->orWhere('mem_middle_name', 'like', $search . '%')
                    ->orWhere('mem_last_name', 'like', $search . '%');
            })
            ->orderBy('mem_last_name')
            ->orderBy('mem_first_name')
            ->limit(30)
            ->get();

        foreach ($members as $member) {
            $fullName = trim(implode(' ', array_filter([
                $member->mem_first_name,
                $member->mem_middle_name,
                $member->mem_last_name,
            ])));

            $this->searchResults[] = [
                'type' => 'member',
                'id' => (string) $member->member_id_no,
                'name' => $fullName,
                'member_type' => $member->psa_mem_type ?? 'N/A',
                'chapter' => $member->psa_chapter_code ?? 'N/A',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH REGISTERED NON-MEMBERS
        |--------------------------------------------------------------------------
        */

        $nonMembers = CmeProgramRegistrationNM::query()
            ->select([
                'nm_id_no',
                'nm_first_name',
                'nm_middle_name',
                'nm_last_name',
            ])
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where(function ($query) use ($search) {
                $query->where('nm_first_name', 'like', $search . '%')
                    ->orWhere('nm_middle_name', 'like', $search . '%')
                    ->orWhere('nm_last_name', 'like', $search . '%');
            })
            ->orderBy('nm_last_name')
            ->orderBy('nm_first_name')
            ->limit(30)
            ->get();

        foreach ($nonMembers as $nonMember) {
            $fullName = trim(implode(' ', array_filter([
                $nonMember->nm_first_name,
                $nonMember->nm_middle_name,
                $nonMember->nm_last_name,
            ])));

            $this->searchResults[] = [
                'type' => 'non-member',
                'id' => (string) $nonMember->nm_id_no,
                'name' => $fullName,
                'member_type' => 'NON-MEMBER',
                'chapter' => 'N/A',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH RESULT MESSAGE
        |--------------------------------------------------------------------------
        */

        if (empty($this->searchResults)) {
            $this->showMessage(
                'No registered participant matched your search.',
                'error'
            );

            return;
        }

        $this->showMessage(
            count($this->searchResults) .
            ' matching participant(s) found. Select a participant to continue.',
            'success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXACT PSA ID LOOKUP
    |--------------------------------------------------------------------------
    |
    | Searches the registration tables first.
    | Only retrieves member information when a registration exists.
    |
    */

    private function lookupParticipantById(string $id): void
    {
        $this->resetDisplay();

        $this->searchResults = [];
        $this->isManualEntry = false;

        /*
        |--------------------------------------------------------------------------
        | CHECK REGISTERED MEMBER
        |--------------------------------------------------------------------------
        */

        $registeredMember = CmeProgramRegistration::query()
            ->select([
                'member_id_no',
                'payment_ref_no',
            ])
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $id)
            ->first();

        if ($registeredMember) {

            $member = Member::query()
                ->select([
                    'member_id_no',
                    'mem_first_name',
                    'mem_middle_name',
                    'mem_last_name',
                    'psa_mem_type',
                    'psa_chapter_code',
                ])
                ->where('member_id_no', $id)
                ->first();

            if (!$member) {
                $this->showMessage(
                    'Member information could not be found.',
                    'error'
                );

                return;
            }

            $this->displayMemberId = (string) $member->member_id_no;

            $this->displayFirstName = $member->mem_first_name;
            $this->displayMiddleName = $member->mem_middle_name;
            $this->displayLastName = $member->mem_last_name;

            $this->displayMemberType = $member->psa_mem_type ?? 'N/A';
            $this->displayChapter = $member->psa_chapter_code ?? 'N/A';

            $this->referenceNumber = $registeredMember->payment_ref_no;

            $this->generateQrCode($this->displayMemberId);

            $this->showMessage(
                'Participant is registered. ID is ready for printing.',
                'success'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK REGISTERED NON-MEMBER
        |--------------------------------------------------------------------------
        */

        $registeredNonMember = CmeProgramRegistrationNM::query()
            ->select([
                'nm_id_no',
                'nm_first_name',
                'nm_middle_name',
                'nm_last_name',
                'payment_ref_no',
            ])
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $id)
            ->first();

        if ($registeredNonMember) {

            $this->displayMemberId = (string) $registeredNonMember->nm_id_no;

            $this->displayFirstName = $registeredNonMember->nm_first_name;
            $this->displayMiddleName = $registeredNonMember->nm_middle_name;
            $this->displayLastName = $registeredNonMember->nm_last_name;

            $this->displayMemberType = 'NON-MEMBER';
            $this->displayChapter = 'N/A';

            $this->referenceNumber = $registeredNonMember->payment_ref_no;

            $this->generateQrCode($this->displayMemberId);

            $this->showMessage(
                'Non-member is registered. ID is ready for printing.',
                'success'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | NO REGISTRATION FOUND
        |--------------------------------------------------------------------------
        */

        $this->showMessage(
            'No registered participant found with PSA ID: ' . $id,
            'error'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SELECT PARTICIPANT FROM SEARCH RESULTS
    |--------------------------------------------------------------------------
    */

    public function selectParticipant(string $type, string $id): void
    {
        $this->resetDisplay();

        $this->isManualEntry = false;
        $this->manualName = '';
        $this->memberId = $id;

        $this->searchResults = [];

        $this->resetMessage();


        /*
        |--------------------------------------------------------------------------
        | REGULAR MEMBER
        |--------------------------------------------------------------------------
        */

        if ($type === 'member') {

            $this->lookupSelectedMember($id);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | NON-MEMBER
        |--------------------------------------------------------------------------
        */

        if ($type === 'non-member') {

            $this->lookupSelectedNonMember($id);

            return;
        }

        $this->showMessage(
            'Invalid participant type.',
            'error'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD SELECTED MEMBER
    |--------------------------------------------------------------------------
    */

    private function lookupSelectedMember(string $id): void
    {
        $registered = CmeProgramRegistration::query()
            ->select([
                'member_id_no',
                'payment_ref_no',
            ])
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $id)
            ->first();

        if (!$registered) {
            $this->showMessage(
                'This member is not registered for the selected CME program.',
                'error'
            );

            return;
        }

        $member = Member::query()
            ->select([
                'member_id_no',
                'mem_first_name',
                'mem_middle_name',
                'mem_last_name',
                'psa_mem_type',
                'psa_chapter_code',
            ])
            ->where('member_id_no', $id)
            ->first();

        if (!$member) {
            $this->showMessage(
                'Member information could not be found.',
                'error'
            );

            return;
        }

        $this->displayMemberId = (string) $member->member_id_no;

        $this->displayFirstName = $member->mem_first_name;
        $this->displayMiddleName = $member->mem_middle_name;
        $this->displayLastName = $member->mem_last_name;

        $this->displayMemberType = $member->psa_mem_type ?? 'N/A';
        $this->displayChapter = $member->psa_chapter_code ?? 'N/A';

        $this->referenceNumber = $registered->payment_ref_no;

        $this->generateQrCode($this->displayMemberId);

        $this->showMessage(
            'Participant is registered. ID is ready for printing.',
            'success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD SELECTED NON-MEMBER
    |--------------------------------------------------------------------------
    */

    private function lookupSelectedNonMember(string $id): void
    {
        $registered = CmeProgramRegistrationNM::query()
            ->select([
                'nm_id_no',
                'nm_first_name',
                'nm_middle_name',
                'nm_last_name',
                'payment_ref_no',
            ])
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $id)
            ->first();

        if (!$registered) {
            $this->showMessage(
                'This non-member is not registered for the selected CME program.',
                'error'
            );

            return;
        }

        $this->displayMemberId = (string) $registered->nm_id_no;

        $this->displayFirstName = $registered->nm_first_name;
        $this->displayMiddleName = $registered->nm_middle_name;
        $this->displayLastName = $registered->nm_last_name;

        $this->displayMemberType = 'NON-MEMBER';
        $this->displayChapter = 'N/A';

        $this->referenceNumber = $registered->payment_ref_no;

        $this->generateQrCode($this->displayMemberId);

        $this->showMessage(
            'Non-member is registered. ID is ready for printing.',
            'success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MANUAL NAME-ONLY ENTRY
    |--------------------------------------------------------------------------
    */

    public function printManualEntry(): void
    {
        $this->resetMessage();

        $this->resetDisplay();

        $this->searchResults = [];
        $this->memberId = '';

        $name = trim(preg_replace('/\s+/', ' ', $this->manualName));

        if ($name === '') {
            $this->showMessage(
                'Please enter the participant name.',
                'error'
            );

            return;
        }

        if (mb_strlen($name) > 255) {
            $this->showMessage(
                'The participant name must not exceed 255 characters.',
                'error'
            );

            return;
        }

        $this->manualName = $name;

        $this->isManualEntry = true;

        $this->displayFirstName = $name;
        $this->displayLastName = '';
        $this->displayMiddleName = '';

        $this->displayMemberId = null;
        $this->displayMemberType = 'MANUAL';
        $this->displayChapter = null;

        $this->referenceNumber = null;
        $this->qrCode = null;

        $this->showMessage(
            'Manual name entry is ready for printing.',
            'success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE QR CODE
    |--------------------------------------------------------------------------
    */

    private function generateQrCode(?string $memberId): void
    {
        if (!$memberId) {
            $this->qrCode = null;

            return;
        }

        $qr = QrCode::format('svg')
            ->size(300)
            ->margin(0)
            ->generate($memberId);

        $this->qrCode = base64_encode($qr);
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAR
    |--------------------------------------------------------------------------
    */

    public function clear(): void
    {
        $this->memberId = '';
        $this->manualName = '';

        $this->isManualEntry = false;
        $this->searchResults = [];

        $this->resetDisplay();
        $this->resetMessage();
    }


    /*
    |--------------------------------------------------------------------------
    | RESET DISPLAY
    |--------------------------------------------------------------------------
    */

    private function resetDisplay(): void
    {
        $this->displayMemberId = null;

        $this->displayFirstName = null;
        $this->displayMiddleName = null;
        $this->displayLastName = null;

        $this->displayMemberType = null;
        $this->displayChapter = null;

        $this->referenceNumber = null;
        $this->qrCode = null;
    }


    /*
    |--------------------------------------------------------------------------
    | MESSAGES
    |--------------------------------------------------------------------------
    */

    private function showMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }


    private function resetMessage(): void
    {
        $this->message = null;
        $this->messageType = null;
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view('livewire.cme-attendance.id-printing')
            ->layout('layouts.app');
    }
}
