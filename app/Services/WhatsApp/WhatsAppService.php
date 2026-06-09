<?php

namespace App\Services\WhatsApp;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;

class WhatsAppService
{
    /** Supported copy types and their display labels */
    public const TYPES = [
        'broadcast' => 'Broadcast message',
        'status' => 'Status update',
        'promo' => 'Promo text',
        'follow_up' => 'Customer follow-up',
    ];

    public function __construct(private readonly AiProviderFactory $factory) {}

    /**
     * Generate WhatsApp copy for the given type and context.
     *
     * @return array{
     *   type: string,
     *   messages: array<int, array{label: string, body: string, emoji_note: string}>,
     *   do_tips: string[],
     *   dont_tips: string[]
     * }
     *
     * @throws AiProviderException
     */
    public function generate(Brand $brand, string $type, string $context): array
    {
        $provider = $this->factory->make();

        $raw = $provider->generate(
            $this->buildSystemPrompt($brand),
            $this->buildUserPrompt($brand, $type, $context),
            2048
        );

        return $this->parse($raw, $type);
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function buildSystemPrompt(Brand $brand): string
    {
        $voice = $brand->brand_voice
            ? 'Brand Voice: '.($brand->brand_voice['writing_summary'] ?? json_encode($brand->brand_voice))
            : '';

        $profile = implode("\n", array_filter([
            $brand->name ? "Brand: {$brand->name}" : null,
            $brand->description ? "What they do: {$brand->description}" : null,
            $brand->target_audience ? "Audience: {$brand->target_audience}" : null,
            $brand->negative_brief ? "Never say: {$brand->negative_brief}" : null,
            $voice ?: null,
        ]));

        return <<<PROMPT
You are a WhatsApp copywriter for African business founders and consultants.
WhatsApp is personal. It is not LinkedIn. It is not a press release.

{$profile}

WHATSAPP COPY RULES — non-negotiable:
1. Write like a real person sending a message, not a brand broadcasting.
2. First line must make the reader want to keep reading. No boring openers.
3. Keep it short. Under 200 words per message. Under 100 is better.
4. No hashtags. Ever.
5. One clear action per message. Never two asks.
6. Emojis: maximum 2–3, placed naturally. Never as bullet points.
7. Never sound corporate, stiff, or like a flyer.
8. Use the brand's voice if provided — match their natural tone exactly.
9. African English is valid — do not over-correct to British/American formal.
10. Return valid JSON only. No markdown. No explanation.
PROMPT;
    }

    private function buildUserPrompt(Brand $brand, string $type, string $context): string
    {
        $typeInstructions = match ($type) {
            'broadcast' => <<<'INST'
TYPE: Broadcast message
PURPOSE: Sent to your entire WhatsApp contact list or broadcast group.
GOAL: Share news, an update, or a valuable insight that makes them glad they saved your number.
LENGTH: 80–150 words. Feels like you're personally updating a valued contact.
INST,
            'status' => <<<'INST'
TYPE: WhatsApp Status update
PURPOSE: 24-hour status visible to all your contacts.
GOAL: Stay top of mind — a tip, a result, a quote, a behind-the-scenes moment.
LENGTH: Under 60 words. Punchy. Must stop the thumb mid-scroll.
INST,
            'promo' => <<<'INST'
TYPE: Promo / sales message
PURPOSE: Announce an offer, product, service, or flash sale.
GOAL: Make the reader feel they'd be missing out if they don't act now.
LENGTH: 80–120 words. Specific. Clear price or offer. One CTA.
INST,
            'follow_up' => <<<'INST'
TYPE: Customer follow-up message
PURPOSE: Sent after an enquiry, a meeting, or a purchase.
GOAL: Build the relationship, move toward the next step — without being pushy.
LENGTH: 60–100 words. Warm, personal, one soft next step.
INST,
            default => 'TYPE: Broadcast message',
        };

        return <<<PROMPT
BRAND: {$brand->name}
AUDIENCE: {$brand->target_audience}

{$typeInstructions}

CONTEXT / OFFER / TOPIC:
{$context}

Generate 2 variations of this WhatsApp message (different angles, not just reworded).
Also give 2 quick do's and 2 don'ts for sending this type of message.

Return this exact JSON:
{
  "type": "{$type}",
  "messages": [
    {
      "label": "Variation 1 — [angle name e.g. Direct, Warm, Story]",
      "body": "The full WhatsApp message text",
      "emoji_note": "Optional: one sentence on emoji placement if relevant"
    },
    {
      "label": "Variation 2 — [angle name]",
      "body": "The full WhatsApp message text",
      "emoji_note": "Optional: one sentence on emoji placement if relevant"
    }
  ],
  "do_tips": ["Do tip 1", "Do tip 2"],
  "dont_tips": ["Don't tip 1", "Don't tip 2"]
}

Respond with valid JSON only.
PROMPT;
    }

    // ── Parser ────────────────────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function parse(string $raw, string $type): array
    {
        $clean = trim(preg_replace('/^```json\s*/m', '', preg_replace('/^```\s*/m', '', $raw)));
        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return [
                'type' => $type,
                'messages' => [
                    ['label' => 'Message', 'body' => $raw, 'emoji_note' => ''],
                ],
                'do_tips' => [],
                'dont_tips' => [],
            ];
        }

        return $data;
    }
}
