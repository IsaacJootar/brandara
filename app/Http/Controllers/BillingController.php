<?php

namespace App\Http\Controllers;

use App\Models\BillingSetting;
use App\Services\Billing\BillingService;
use App\Services\Billing\FlutterwaveProvider;
use App\Services\Billing\PaystackProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(private readonly BillingService $billing) {}

    public function index(): View
    {
        $workspace = auth()->user()->workspace;

        // Share first brand so the app layout nav can render ($currentBrand is set by ResolveBrand
        // middleware on brand-scoped routes — billing is workspace-level so we share manually)
        $firstBrand = $workspace->brands()->first();
        if ($firstBrand) {
            view()->share('currentBrand', $firstBrand);
        }
        $currency = $this->billing->currencyForWorkspace($workspace);
        $plans = $this->billing->plansForCurrency($currency);
        $sub = $this->billing->currentSubscription($workspace);
        $usage = $this->billing->usageSummary($workspace);
        $provider = $this->billing->provider()->name();
        $settings = [
            'yearly_discount_label' => BillingSetting::get('yearly_discount_label', '2 months free'),
            'flutterwave_public_key' => config('services.flutterwave.public_key', ''),
            'paystack_public_key' => config('services.paystack.public_key', ''),
        ];

        return view('billing.index', compact(
            'workspace', 'currency', 'plans', 'sub', 'usage', 'provider', 'settings'
        ));
    }

    public function initializeCheckout(Request $request): JsonResponse
    {
        $request->validate(['plan_id' => 'required|uuid|exists:billing_plans,id']);

        $workspace = auth()->user()->workspace;

        try {
            $data = $this->billing->initializePayment($workspace, $request->plan_id);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Could not start checkout. Please try again.'], 422);
        }
    }

    public function verifyPayment(Request $request): RedirectResponse
    {
        $reference = $request->get('tx_ref') ?? $request->get('reference', '');
        $provider = $request->get('provider', 'flutterwave') === 'paystack'
            ? app(PaystackProvider::class)
            : app(FlutterwaveProvider::class);

        $result = $provider->verifyPayment($reference);

        if ($result['status'] !== 'success') {
            return redirect()->route('billing')->with('error', 'Payment could not be verified. Please contact support.');
        }

        $result['provider'] = $provider->name();
        $this->billing->handlePaymentSuccess($result);

        return redirect()->route('billing')->with('success', 'Payment successful — your plan has been upgraded!');
    }
}
