<?php

namespace App\Mail;

use App\Models\Brand;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyDigestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $summary;

    public array $wow;

    public string $brandName;

    public string $brandSlug;

    public function __construct(public Brand $brand)
    {
        $svc = app(AnalyticsService::class);
        $this->brandName = $brand->name;
        $this->brandSlug = $brand->slug;
        $this->summary = $svc->summary($brand, 7);
        $this->wow = $svc->weekOverWeek($brand);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Brandara results for {$this->brandName} this week",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-digest',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
