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

    public ?string $message = null;

    public ?string $messageType = null;

    /*
    |--------------------------------------------------------------------------
    | Displayed Participant Information
    |--------------------------------------------------------------------------
    */

    public ?string $displayMemberId = null;

    public ?string $displayFirstName = null;

    public ?string $displayLastName = null;

    public ?string $displayMiddleName = null;

    public ?string $displayMemberType = null;

    public ?string $referenceNumber = null;

    /*
    |--------------------------------------------------------------------------
    | QR Code
    |--------------------------------------------------------------------------
    */

    public ?string $qrCode = null;


    /*
    |--------------------------------------------------------------------------
    | Search / Check Registration
    |--------------------------------------------------------------------------
    */

    public function searchMember(): void
    {
        $this->resetDisplay();

        $this->message = null;

        $this->messageType = null;

        $memberId = trim($this->memberId);

        if ($memberId === '') {

            $this->showMessage(
                'Please scan or enter a participant ID.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 1. CHECK NON-MEMBER REGISTRATION
        |--------------------------------------------------------------------------
        */

        $nonMember = CmeProgramRegistrationNM::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('nm_id_no', $memberId)
            ->first();

        if ($nonMember) {

            $this->displayMemberId = $nonMember->nm_id_no;

            $this->displayFirstName = $nonMember->nm_first_name;

            $this->displayLastName = $nonMember->nm_last_name;

            $this->displayMiddleName = $nonMember->nm_middle_name;

            $this->displayMemberType = 'NM';

            /*
            |--------------------------------------------------------------------------
            | Reference Number
            |--------------------------------------------------------------------------
            |
            | Use this only if cme_program_registrationNM has
            | a reference_number column.
            |
            */

            $this->referenceNumber = $nonMember->reference_number ?? null;


            /*
            |--------------------------------------------------------------------------
            | Generate QR Code
            |--------------------------------------------------------------------------
            */

            $this->generateQrCode($this->displayMemberId);


            $this->showMessage(
                'Participant is registered for this CME program.',
                'success'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 2. CHECK REGULAR MEMBER REGISTRATION
        |--------------------------------------------------------------------------
        */

        $registration = CmeProgramRegistration::query()
            ->where('cme_year', $this->cmeYear)
            ->where('cme_program_code', $this->cmeProgramCode)
            ->where('member_id_no', $memberId)
            ->first();

        if (!$registration) {

            $this->showMessage(
                'This participant is not registered for this CME program.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 3. GET REFERENCE NUMBER
        |--------------------------------------------------------------------------
        */

        $this->referenceNumber = $registration->payment_ref_no ?? null;


        /*
        |--------------------------------------------------------------------------
        | 4. LOAD MEMBER
        |--------------------------------------------------------------------------
        */

        $member = Member::query()
            ->where('member_id_no', $memberId)
            ->first();

        if (!$member) {

            $this->showMessage(
                'The CME registration was found, but the member record could not be found.',
                'error'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 5. DISPLAY MEMBER
        |--------------------------------------------------------------------------
        */

        $this->displayMemberId = $member->member_id_no;

        $this->displayFirstName = $member->mem_first_name;

        $this->displayLastName = $member->mem_last_name;

        $this->displayMiddleName = $member->mem_middle_name;

        $this->displayMemberType = $member->psa_mem_type;


        /*
        |--------------------------------------------------------------------------
        | 6. GENERATE QR CODE
        |--------------------------------------------------------------------------
        */

        $this->generateQrCode($this->displayMemberId);


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $this->showMessage(
            'Member is registered for this CME program.',
            'success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate QR Code
    |--------------------------------------------------------------------------
    |
    | The QR code contains only the participant/member ID.
    |
    */

    private function generateQrCode(?string $memberId): void
    {
        if (!$memberId) {

            $this->qrCode = null;

            return;
        }


        $svg = QrCode::format('svg')
            ->size(500)
            ->margin(0)
            ->errorCorrection('H')
            ->generate($memberId);


        /*
        |--------------------------------------------------------------------------
        | Remove SVG Background
        |--------------------------------------------------------------------------
        |
        | This removes the white <rect> generated by the QR library
        | so the convention artwork remains visible behind the QR code.
        |
        */

        $svg = preg_replace(
            '/<rect[^>]*fill=["\']#?ffffff["\'][^>]*\/?>/i',
            '',
            $svg
        );


        $this->qrCode = base64_encode($svg);
    }


    /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    public function clear(): void
    {
        $this->memberId = '';

        $this->resetDisplay();

        $this->message = null;

        $this->messageType = null;
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Display
    |--------------------------------------------------------------------------
    */

    private function resetDisplay(): void
    {
        $this->displayMemberId = null;

        $this->displayFirstName = null;

        $this->displayLastName = null;

        $this->displayMiddleName = null;

        $this->displayMemberType = null;

        $this->referenceNumber = null;

        $this->qrCode = null;
    }


    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */

    private function showMessage(
        string $message,
        string $type
    ): void {
        $this->message = $message;

        $this->messageType = $type;
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view('livewire.cme-attendance.id-printing')
            ->layout('layouts.app');
    }
}