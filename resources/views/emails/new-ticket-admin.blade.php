<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New email ticket</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, 'Segoe UI', sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    <tr>
                        <td style="background:#0f172a; padding:20px 28px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:bold;">{{ config('app.name') }}</span>
                            <span style="color:#94a3b8; font-size:13px;"> · Admin notification</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <h1 style="margin:0 0 12px; font-size:20px; color:#0f172a;">A new ticket arrived by email</h1>
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.7; color:#334155;">
                                An incoming email was automatically converted into a support ticket. Details below.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; margin:0 0 20px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 4px; font-size:12px; color:#64748b;">Ticket</p>
                                        <p style="margin:0 0 14px; font-size:18px; font-weight:bold; color:#0f172a;">#{{ $ticket->id }} — {{ $ticket->title }}</p>

                                        <p style="margin:0 0 4px; font-size:12px; color:#64748b;">From</p>
                                        <p style="margin:0 0 14px; font-size:14px; color:#0f172a;">
                                            {{ $senderName ? $senderName.' <'.$senderEmail.'>' : $senderEmail }}
                                        </p>

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%" style="vertical-align:top;">
                                                    <p style="margin:0 0 4px; font-size:12px; color:#64748b;">Category</p>
                                                    <p style="margin:0; font-size:14px; color:#0f172a;">{{ ucwords(str_replace('_', ' ', $ticket->category)) }}</p>
                                                </td>
                                                <td width="50%" style="vertical-align:top;">
                                                    <p style="margin:0 0 4px; font-size:12px; color:#64748b;">Priority</p>
                                                    <p style="margin:0; font-size:14px; color:#0f172a;">{{ ucwords($ticket->priority) }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if (trim((string) $ticket->description) !== '')
                                <p style="margin:0 0 6px; font-size:12px; color:#64748b;">Message</p>
                                <p style="margin:0 0 20px; font-size:14px; line-height:1.7; color:#334155; white-space:pre-line;">{{ \Illuminate\Support\Str::limit($ticket->description, 500) }}</p>
                            @endif

                            <a href="{{ route('tickets.show', $ticket) }}" style="display:inline-block; background:#0f172a; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; padding:12px 24px; border-radius:8px;">Open ticket</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; padding:16px 28px; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">{{ config('app.name') }} · Automated admin notification</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
