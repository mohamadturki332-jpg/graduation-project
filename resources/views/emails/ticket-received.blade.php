<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم استلام طلبك</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, 'Segoe UI', sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    <tr>
                        <td style="background:#0f172a; padding:20px 28px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:bold;">{{ config('app.name') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <h1 style="margin:0 0 12px; font-size:20px; color:#0f172a;">تم استلام طلبك</h1>
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.7; color:#334155;">
                                شكرًا لتواصلك مع الدعم الفني. تم تحويل رسالتك إلى تذكرة وسيقوم أحد موظفي الدعم بمراجعتها في أقرب وقت.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; margin:0 0 16px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0 0 6px; font-size:13px; color:#64748b;">رقم التذكرة</p>
                                        <p style="margin:0 0 14px; font-size:18px; font-weight:bold; color:#0f172a;">#{{ $ticket->id }}</p>
                                        <p style="margin:0 0 6px; font-size:13px; color:#64748b;">الموضوع</p>
                                        <p style="margin:0; font-size:15px; color:#0f172a;">{{ $ticket->title }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:13px; line-height:1.7; color:#64748b;">
                                يرجى الاحتفاظ برقم التذكرة للمتابعة. هذه رسالة تلقائية — لا حاجة للرد عليها.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; padding:16px 28px; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">{{ config('app.name') }} · نظام الدعم الفني</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
