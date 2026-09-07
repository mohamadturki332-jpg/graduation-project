<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رد على تذكرتك</title>
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
                            <h1 style="margin:0 0 12px; font-size:20px; color:#0f172a;">لديك رد جديد على تذكرتك</h1>
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.7; color:#334155;">
                                قام <strong>{{ $agentName }}</strong> من فريق الدعم الفني بالرد على تذكرتك رقم <strong>#{{ $ticket->id }}</strong> ({{ $ticket->title }}).
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border-right:3px solid #0f172a; border-radius:6px; margin:0 0 16px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0; font-size:15px; line-height:1.8; color:#0f172a; white-space:pre-line;">{{ $replyBody }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:13px; line-height:1.7; color:#64748b;">
                                يرجى الاحتفاظ برقم التذكرة <strong>#{{ $ticket->id }}</strong> للمتابعة.
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
