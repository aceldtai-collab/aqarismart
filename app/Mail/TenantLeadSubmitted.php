<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantLeadSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function build(): self
    {
        $subject = 'New Inquiry'.($this->lead->unit? ' – Unit '.$this->lead->unit->code : '');
        return $this->subject($subject)
            ->markdown('emails.tenant.lead_submitted', [
                'lead' => $this->lead,
            ]);
    }
}

