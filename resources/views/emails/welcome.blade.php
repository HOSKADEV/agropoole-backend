<!DOCTYPE html>
<html>

<body style="margin:0; padding:0; background-color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color:#ffffff;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
                    style="width:600px; max-width:600px; background-color:#ffffff;">
                    <tr>
                        <td align="center" style="padding:24px 24px 8px;">
                            <img src="{{ asset('assets/img/mail/logo.webp') }}" alt="Agropool Logo" width="120"
                                style="display:block; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <img src="{{ asset('assets/img/mail/header.webp') }}" alt="Agropool" width="600"
                                style="display:block; width:100%; max-width:600px; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="padding:8px 24px 24px; font-family: Arial, sans-serif; font-size:16px; line-height:24px; color:#0b0b0b;">
                            <p style="margin:0 0 12px;">Hello {{ $user->name ?? 'there' }},</p>
                            <p style="margin:0 0 12px;">Welcome to Agropool! We’re glad to have you on board.</p>
                            <p style="margin:0 0 12px;">If you have any questions, just reply to this email.</p>
                            <p style="margin:0;">— The Agropool Team</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <img src="{{ asset('assets/img/mail/footer.webp') }}" alt="" width="600"
                                style="display:block; width:100%; max-width:600px; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
