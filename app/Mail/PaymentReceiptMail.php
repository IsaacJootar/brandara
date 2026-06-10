<?php

namespace App\Mail;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $planLabel;

    public string $intervalLabel;

    public string $formattedAmount;

    public string $nextBillingDate;

    public string $workspaceName;

    public function __construct(
        public Workspace $workspace,
        public array $paymentData
    ) {
        $this->workspaceName = $workspace->name;
        $this->planLabel = match ($paymentData['plan'] ?? 'starter') {
            'pro' => 'Growth',
            'agency' => 'Agency',
            default => 'Basic',
        };
        $this->intervalLabel = ($paymentData['interval'] ?? 'monthly') === 'yearly' ? 'Annual' : 'Monthly';

        $symbols = ['USD' => '$', 'NGN' => '₦', 'GBP' => '£', 'EUR' => '€', 'GHS' => 'GH₵', 'KES' => 'KSh', 'ZAR' => 'R'];
        $symbol = $symbols[$paymentData['currency'] ?? 'USD'] ?? '';
        $this->formattedAmount = $symbol.number_format((float) ($paymentData['amount'] ?? 0), 2);

        $this->nextBillingDate = (($paymentData['interval'] ?? 'monthly') === 'yearly')
            ? now()->addYear()->format('d M Y')
            : now()->addMonth()->format('d M Y');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Brandara {$this->planLabel} plan is active — receipt enclosed",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-receipt');
    }

    public function attachments(): array
    {
        return [];
    }
}
