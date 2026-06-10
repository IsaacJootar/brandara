<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    use HasUuids;

    protected $fillable = [
        'plan', 'interval', 'currency', 'amount',
        'flutterwave_plan_id', 'paystack_plan_id', 'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /** Human-readable plan label. */
    public function planLabel(): string
    {
        return match ($this->plan) {
            'starter' => 'Basic',
            'pro' => 'Growth',
            'agency' => 'Agency',
            default => ucfirst($this->plan),
        };
    }

    /** Formatted display price e.g. "$19.00" or "₦30,000". */
    public function formattedAmount(): string
    {
        $symbols = [
            'USD' => '$', 'NGN' => '₦', 'GBP' => '£',
            'EUR' => '€', 'GHS' => 'GH₵', 'KES' => 'KSh', 'ZAR' => 'R',
        ];
        $symbol = $symbols[$this->currency] ?? $this->currency.' ';

        // NGN and similar: no decimal
        $noDecimal = in_array($this->currency, ['NGN', 'GHS', 'KES', 'ZAR']);

        return $symbol.($noDecimal
            ? number_format((float) $this->amount, 0)
            : number_format((float) $this->amount, 2));
    }

    /** Yearly savings vs 12× monthly (in same currency). */
    public function yearlySavings(): ?string
    {
        if ($this->interval !== 'yearly') {
            return null;
        }

        $monthly = static::where('plan', $this->plan)
            ->where('interval', 'monthly')
            ->where('currency', $this->currency)
            ->where('is_active', true)
            ->first();

        if (! $monthly) {
            return null;
        }

        $saving = ((float) $monthly->amount * 12) - (float) $this->amount;

        if ($saving <= 0) {
            return null;
        }

        $temp = clone $this;
        $temp->amount = $saving;

        return $temp->formattedAmount();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
