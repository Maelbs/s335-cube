<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation mot de passe Cube Bikes</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7; }
        .btn-reset { background-color: #0071e3; color: #ffffff; padding: 14px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; text-transform: uppercase; letter-spacing: 1px; }
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; }
            .fluid { width: 100% !important; height: auto !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 0;">
                
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="email-container" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

                    <tr>
                        <td class="mobile-padding" style="padding: 40px 50px;">
                            <h1 style="margin: 0 0 20px; font-size: 24px; font-weight: 700; color: #333333; text-align: center; text-transform: uppercase; letter-spacing: 1px;">
                                Mot de passe oublié ?
                            </h1>
                            <p style="margin: 0 0 30px; font-size: 16px; line-height: 24px; color: #666666; text-align: center;">
                                Bonjour,<br>
                                Une demande de réinitialisation de mot de passe a été effectuée pour votre compte <strong>Cube Bikes France</strong>.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 30px;">
                                        <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" class="btn-reset">
                                            Réinitialiser mon mot de passe
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 0; font-size: 14px; color: #999999; text-align: center;">
                                <em>Ce lien est valable pendant <strong>60 minutes</strong>.</em>
                            </p>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px; border-top: 1px solid #eeeeee;">
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;">
                                        <p style="font-size: 13px; color: #aaaaaa; margin: 0;">
                                            Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #1a1a1a; padding: 30px; text-align: center;">
                            <p style="color: #ffffff; font-size: 14px; margin: 0 0 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                                BE ONE WITH YOUR BIKE
                            </p>
                            <p style="color: #888888; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} Cube Bikes France. Tous droits réservés.<br>
                                <a href="#" style="color: #888888; text-decoration: underline;">Mentions légales</a> | <a href="#" style="color: #888888; text-decoration: underline;">Support</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>