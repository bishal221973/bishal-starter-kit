<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Verify Your Email</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f7f9;
    font-family: Arial, Helvetica, sans-serif;
    color: #1f2937;
">

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f4f7f9; padding: 40px 15px;">

        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="
                        max-width: 600px;
                        background-color: #ffffff;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
                    ">

                    <!-- Header -->
                    <tr>
                        <td align="center"
                            style="
                                background-color: #348797;
                                padding: 30px 20px;
                            ">

                            <h1 style="
                                margin: 0;
                                color: #ffffff;
                                font-size: 26px;
                                font-weight: 700;
                            ">
                                Verify Your Email
                            </h1>

                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 35px;">

                            <h2 style="
                                margin: 0 0 15px;
                                font-size: 22px;
                                color: #111827;
                            ">
                                Hello {{ $user->name }},
                            </h2>

                            <p style="
                                margin: 0 0 18px;
                                font-size: 15px;
                                line-height: 1.7;
                                color: #4b5563;
                            ">
                                Welcome! We're happy to have you with us.
                                Please verify your email address to complete
                                your account setup.
                            </p>

                            <!-- Button -->
                            <table cellpadding="0" cellspacing="0" border="0"
                                style="margin: 30px auto;">

                                <tr>
                                    <td align="center"
                                        style="
                                            background-color: #348797;
                                            border-radius: 7px;
                                        ">

                                        <a href="{{ $verificationUrl }}"
                                            style="
                                                display: inline-block;
                                                padding: 14px 30px;
                                                font-size: 15px;
                                                font-weight: 600;
                                                color: #ffffff;
                                                text-decoration: none;
                                            ">
                                            Verify Email Address
                                        </a>

                                    </td>
                                </tr>

                            </table>

                            <!-- Expiration -->
                            <div style="
                                margin: 25px 0;
                                padding: 15px;
                                background-color: #f0f7f8;
                                border-left: 4px solid #348797;
                                border-radius: 5px;
                            ">

                                <p style="
                                    margin: 0;
                                    font-size: 14px;
                                    line-height: 1.6;
                                    color: #374151;
                                ">
                                    <strong>Note:</strong>
                                    This verification link will expire in
                                    <strong>60 minutes</strong>.
                                </p>

                            </div>

                            <!-- Fallback URL -->
                            <p style="
                                margin: 25px 0 8px;
                                font-size: 13px;
                                color: #6b7280;
                            ">
                                If the button above doesn't work, copy and paste
                                the following link into your browser:
                            </p>

                            <p style="
                                margin: 0;
                                padding: 12px;
                                background-color: #f9fafb;
                                border-radius: 6px;
                                word-break: break-all;
                                font-size: 12px;
                            ">

                                <a href="{{ $verificationUrl }}"
                                    style="
                                        color: #348797;
                                        text-decoration: none;
                                    ">
                                    {{ $verificationUrl }}
                                </a>

                            </p>

                            <p style="
                                margin: 25px 0 0;
                                font-size: 14px;
                                line-height: 1.6;
                                color: #6b7280;
                            ">
                                If you did not request this email, you can
                                safely ignore it. Your account will remain
                                unchanged.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="
                                padding: 22px 30px;
                                background-color: #f9fafb;
                                border-top: 1px solid #e5e7eb;
                            ">

                            <p style="
                                margin: 0 0 6px;
                                font-size: 13px;
                                color: #6b7280;
                            ">
                                Thank you for choosing us.
                            </p>

                            <p style="
                                margin: 0;
                                font-size: 12px;
                                color: #9ca3af;
                            ">
                                &copy; {{ date('Y') }} {{ config('app.name') }}.
                                All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>

</html>