<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt — Brandara</title>
</head>
<body style="margin:0;padding:0;background:#F8FAFC;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;padding:40px 20px;">
    <tr><td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #E2E8F0;">

            {{-- Header --}}
            <tr>
                <td style="background:linear-gradient(135deg,#7C3AED,#4338CA);padding:32px;text-align:center;">
                    <p style="color:rgba(255,255,255,0.7);font-size:12px;text-transform:uppercase;letter-spacing:2px;margin:0 0 8px;">Brandara</p>
                    <p style="color:#fff;font-size:24px;font-weight:800;margin:0;">Payment confirmed ✓</p>
                </td>
            </tr>

            {{-- Body --}}
            <tr>
                <td style="padding:32px;">
                    <p style="font-size:15px;color:#374151;margin:0 0 24px;">Hi {{ $workspaceName }},</p>
                    <p style="font-size:15px;color:#374151;margin:0 0 24px;">
                        Your <strong>{{ $planLabel }} plan</strong> is now active. Here's your receipt.
                    </p>

                    {{-- Receipt box --}}
                    <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;overflow:hidden;margin-bottom:24px;">
                        @foreach([
                            ['Plan', $planLabel],
                            ['Billing', $intervalLabel],
                            ['Amount', $formattedAmount],
                            ['Next billing date', $nextBillingDate],
                        ] as $row)
                        <tr>
                            <td style="padding:12px 16px;font-size:13px;color:#94A3B8;border-bottom:1px solid #E2E8F0;width:40%;">{{ $row[0] }}</td>
                            <td style="padding:12px 16px;font-size:13px;color:#0F172A;font-weight:600;border-bottom:1px solid #E2E8F0;">{{ $row[1] }}</td>
                        </tr>
                        @endforeach
                    </table>

                    <p style="font-size:14px;color:#64748B;line-height:1.6;margin:0 0 24px;">
                        You can manage your subscription, download invoices, or cancel anytime from your billing page.
                    </p>

                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <a href="{{ url('/billing') }}" style="display:inline-block;background:#7C3AED;color:#fff;font-size:14px;font-weight:600;padding:12px 24px;border-radius:8px;text-decoration:none;">
                                    Go to my workspace →
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            {{-- Footer --}}
            <tr>
                <td style="padding:20px 32px;border-top:1px solid #F1F5F9;text-align:center;">
                    <p style="font-size:12px;color:#94A3B8;margin:0;">
                        Brandara · Built for African founders and agencies<br>
                        Questions? Reply to this email and we'll help.
                    </p>
                </td>
            </tr>

        </table>
    </td></tr>
</table>
</body>
</html>
