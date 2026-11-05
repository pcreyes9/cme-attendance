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
    | SEARCH PARTICIPANTS
    |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | SEARCH REGISTERED MEMBERS
        |--------------------------------------------------------------------------
        */

        $membersQuery = Member::query()
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('cme_program_registration as cpr')
                    ->whereColumn(
                        'cpr.member_id_no',
                        'member.member_id_no'
                    )
                    ->where('cpr.cme_year', $this->cmeYear)
                    ->where('cpr.cme_program_code', $this->cmeProgramCode);
            });

        /*
         * Search by exact PSA ID first, or by first / middle / last name.
         */
        $membersQuery->where(function ($query) use ($search) {
            $query->where('member.member_id_no', $search)
                ->orWhere('member.mem_first_name', 'like', "%{$search}%")
                ->orWhere('member.mem_last_name', 'like', "%{$search}%")
                ->orWhere('member.mem_middle_name', 'like', "%{$search}%");
        });

        $members = $membersQuery
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

        $nonMembersQuery = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where(function ($query) use ($search) {
                $query->where('nm_id_no', $search)
                    ->orWhere('nm_first_name', 'like', "%{$search}%")
                    ->orWhere('nm_last_name', 'like', "%{$search}%")
                    ->orWhere('nm_middle_name', 'like', "%{$search}%");
            });

        $nonMembers = $nonMembersQuery
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
        | SORT RESULTS
        |--------------------------------------------------------------------------
        */

        usort($this->searchResults, function ($a, $b) use ($search) {
            $aExact = strcasecmp($a['id'], $search) === 0;
            $bExact = strcasecmp($b['id'], $search) === 0;

            return $bExact <=> $aExact;
        });

        if (empty($this->searchResults)) {
            $this->showMessage(
                'No registered participant matched your search.',
                'error'
            );

            return;
        }

        $this->showMessage(
            count($this->searchResults) . ' matching participant(s) found. Select a participant to continue.',
            'success'
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
        $this->resetMessage();

        /*
        |--------------------------------------------------------------------------
        | REGULAR MEMBER
        |--------------------------------------------------------------------------
        */

        if ($type === 'member') {
            $registered = CmeProgramRegistration::query()
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

            /*
             * Adjust this field if your registration table uses
             * a different reference-number column.
             */
            $this->referenceNumber = $registered->payment_ref_no;

            $this->generateQrCode($this->displayMemberId);

            $this->searchResults = [];

            $this->showMessage(
                'Participant is registered. ID is ready for printing.',
                'success'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NON-MEMBER
        |--------------------------------------------------------------------------
        */

        if ($type === 'non-member') {
            $registered = CmeProgramRegistrationNM::query()
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

            $this->searchResults = [];

            $this->showMessage(
                'Non-member is registered. ID is ready for printing.',
                'success'
            );

            return;
        }

        $this->showMessage(
            'Invalid participant type.',
            'error'
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

        /*
         * Store the entire name in the first-name display property.
         * This allows the existing print layout to display it
         * without requiring PSA ID or other participant details.
         */
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