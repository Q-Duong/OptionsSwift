<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Options Swift</title>
</head>
<body style="margin: 0; padding: 0; width: 100% !important; background-color: #0b0c0d; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #0b0c0d; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 40px 15px;">
                
                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 500px; background-color: #111315;  border-radius: 20px; border-collapse: collapse; box-shadow: 0 10px 40px rgba(0,0,0,0.6); margin: 0 auto;">
                    <tr>
                        <td align="left" style="padding: 50px 30px;">
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 30px;">
                                        <span style="font-size: 24px; font-weight: 900; color: #00ff66; letter-spacing: 1.5px; font-style: italic;">
                                            OPTIONS <span style="color: #ffffff;">SWIFT</span>
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="left" style="padding-bottom: 20px;">
                                        <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; line-height: 1.3;">
                                            Verify your email address
                                        </h1>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="left" style="padding-bottom: 15px; color: #a0aab2; font-size: 15px; line-height: 1.6;">
                                        Hi <strong style="color: #ffffff;">{{ $user->name }}</strong>,
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding-bottom: 35px; color: #a0aab2; font-size: 15px; line-height: 1.6;">
                                        Thank you for joining Options Swift! Before you can dive into the real-time options flow scanner and unlock premium signals, we just need to verify that this email address is yours.
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 40px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" bgcolor="#00ff66" style="border-radius: 8px;">
                                                    <a href="{{ $verificationUrl }}" target="_blank" style="display: inline-block; padding: 15px 35px; color: #000000; font-size: 15px; font-weight: 900; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px; border-radius: 8px;">
                                                        Verify Email Address
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="left" style="padding-top: 25px; border-top: 1px solid #1f2226;">
                                        <p style="color: #666e75; font-size: 12px; margin: 0 0 10px 0; line-height: 1.5;">
                                            This link will expire in 60 minutes. If you did not create an account with us, please ignore this email.
                                        </p>
                                        <p style="color: #444a50; font-size: 12px; font-weight: bold; margin: 15px 0 0 0; line-height: 1.5;">
                                            © {{ date('Y') }} Options Swift. All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
                
            </td>
        </tr>
    </table>
</body>
</html>