Mail Configuration

The Bishal Starter Kit provides organization-specific mail configuration through the mail_settings table.

Mail settings allow the application to configure SMTP and other mail transport options without requiring mail credentials to be hard-coded into application code.

Mail Settings Table

Mail configuration is stored in:

mail_settings

The table contains:

id
organization_id
mailer
host
port
username
password
encryption
from_address
from_name
created_at
updated_at
Organization Relationship

Mail settings are associated with an organization through:

$table->foreignId('organization_id')
    ->nullable()
    ->constrained();

This allows different organizations to have different mail configurations.

Example:

Organization A
    ↓
mail_settings
    ↓
SMTP configuration A

Organization B
    ↓
mail_settings
    ↓
SMTP configuration B
Mailer

The mailer field specifies the mail transport.

The default value is:

smtp

Example:

'mailer' => 'smtp'

The starter kit is primarily designed around SMTP configuration.

SMTP Host

The SMTP server hostname is stored in:

host

Examples:

smtp.gmail.com
smtp.office365.com
mail.example.com

Example:

'host' => 'smtp.gmail.com'
SMTP Port

The SMTP port is stored in:

port

The default value is:

587

Common SMTP ports include:

Port	Typical Usage
25	SMTP
465	SMTP over SSL
587	SMTP submission with TLS

Example:

'port' => 587
Username

The SMTP username is stored in:

username

Usually this is the email address used for SMTP authentication.

Example:

support@example.com
Password

The SMTP password is stored in:

password

The database migration uses:

$table->text('password')->nullable();

This allows credentials longer than a standard VARCHAR field.

Important: SMTP passwords are sensitive credentials and should not be exposed in API responses, logs, frontend code, or administration pages unnecessarily.

Encryption

The encryption field specifies SMTP encryption.

Example values:

tls
ssl
null

The migration comment indicates:

// tls, ssl, null

Example:

'encryption' => 'tls'

For port 587, TLS is commonly used.

From Address

The sender email address is stored in:

from_address

Example:

support@example.com

Example:

'from_address' => 'support@example.com'

This address is used as the default sender for outgoing emails.

From Name

The sender display name is stored in:

from_name

Example:

Bishal Starter Kit

When an email is received, the sender may appear as:

Bishal Starter Kit <support@example.com>
Example Mail Configuration

A typical SMTP configuration could look like:

Mailer: smtp
Host: smtp.example.com
Port: 587
Username: support@example.com
Password: ********
Encryption: tls
From Address: support@example.com
From Name: Bishal Starter Kit
Database Migration

The mail settings table is created with:

Schema::create('mail_settings', function (Blueprint $table) {
    $table->id();

    $table->foreignId('organization_id')
        ->nullable()
        ->constrained();

    $table->string('mailer')->default('smtp');
    $table->string('host')->nullable();
    $table->unsignedInteger('port')->default(587);

    $table->string('username')->nullable();
    $table->text('password')->nullable();

    $table->string('encryption')->nullable();

    $table->string('from_address')->nullable();
    $table->string('from_name')->nullable();

    $table->timestamps();
});
Mail Settings Model

A typical model can be:

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
    ];

    protected $hidden = [
        'password',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}

The password should be hidden when the model is converted to an array or JSON response.

Organization Relationship

The Organization model can define:

public function mailSetting()
{
    return $this->hasOne(MailSetting::class);
}

Then:

$organization->mailSetting;

can be used to retrieve the organization's mail configuration.

Saving Mail Settings

Example:

MailSetting::updateOrCreate(
    [
        'organization_id' => $organization->id,
    ],
    [
        'mailer' => 'smtp',
        'host' => $request->host,
        'port' => $request->port,
        'username' => $request->username,
        'password' => $request->password,
        'encryption' => $request->encryption,
        'from_address' => $request->from_address,
        'from_name' => $request->from_name,
    ]
);

Using updateOrCreate() ensures that an organization has one current mail configuration.

Mail Settings Validation

A typical validation rule is:

$request->validate([
    'mailer' => [
        'required',
        'string',
        'max:255',
    ],

    'host' => [
        'nullable',
        'string',
        'max:255',
    ],

    'port' => [
        'required',
        'integer',
        'between:1,65535',
    ],

    'username' => [
        'nullable',
        'string',
        'max:255',
    ],

    'password' => [
        'nullable',
        'string',
    ],

    'encryption' => [
        'nullable',
        'in:tls,ssl',
    ],

    'from_address' => [
        'nullable',
        'email',
        'max:255',
    ],

    'from_name' => [
        'nullable',
        'string',
        'max:255',
    ],
]);
Configuring Laravel Mail Dynamically

If the application stores mail settings in the database, the Laravel mail configuration can be changed at runtime.

Example:

$mailSetting = $organization->mailSetting;

config([
    'mail.default' => $mailSetting->mailer,

    'mail.mailers.smtp.host' => $mailSetting->host,
    'mail.mailers.smtp.port' => $mailSetting->port,
    'mail.mailers.smtp.username' => $mailSetting->username,
    'mail.mailers.smtp.password' => $mailSetting->password,
    'mail.mailers.smtp.encryption' => $mailSetting->encryption,

    'mail.from.address' => $mailSetting->from_address,
    'mail.from.name' => $mailSetting->from_name,
]);

This allows the application to use organization-specific mail settings.

Sending an Email

Once the mail configuration has been loaded, normal Laravel mail functionality can be used.

For example:

use Illuminate\Support\Facades\Mail;

Mail::to($user->email)
    ->send(new WelcomeMail($user));

The mail configuration determines which SMTP server sends the message.

Organization-Specific Mail

For a multi-organization application, the current organization should be determined before loading its mail configuration.

Conceptually:

Authenticated User
        ↓
Current Organization
        ↓
Mail Settings
        ↓
Configure Laravel Mail
        ↓
Send Email

This allows:

Organization A
support@organization-a.com

Organization B
hello@organization-b.com

to use different sender addresses and SMTP servers.

Gmail SMTP Example

A Gmail SMTP configuration commonly looks like:

Mailer: smtp
Host: smtp.gmail.com
Port: 587
Encryption: tls
Username: your-email@gmail.com
Password: application-password

For Gmail accounts using two-factor authentication, an App Password may be required instead of the normal account password.

Microsoft 365 SMTP Example

A Microsoft 365 configuration commonly uses:

Mailer: smtp
Host: smtp.office365.com
Port: 587
Encryption: tls
Username: your-email@example.com
Password: ********

The exact SMTP authentication requirements depend on the Microsoft 365 tenant configuration.

Testing Mail Configuration

A mail configuration page should provide a way to send a test email.

Example controller logic:

use Illuminate\Support\Facades\Mail;

public function test(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    // Load the organization's mail settings first.

    Mail::raw(
        'This is a test email from Bishal Starter Kit.',
        function ($message) use ($request) {
            $message
                ->to($request->email)
                ->subject('Mail Configuration Test');
        }
    );

    return back()->with(
        'success',
        'Test email sent successfully.'
    );
}

The actual implementation should load the current organization's mail settings before sending.

Mail Configuration UI

A mail settings page can contain:

Mail Configuration

Mailer
[ SMTP ]

SMTP Host
[ smtp.example.com ]

SMTP Port
[ 587 ]

Username
[ support@example.com ]

Password
[ ******** ]

Encryption
[ TLS ]

From Address
[ support@example.com ]

From Name
[ Bishal Starter Kit ]

[ Save Settings ]
[ Send Test Email ]
Password Security

The SMTP password is sensitive.

Do not return it directly:

return response()->json($mailSetting);

unless the password has been hidden from serialization.

Use:

protected $hidden = [
    'password',
];

When updating the settings UI, it is also preferable to leave the existing password unchanged when the password field is empty.

Example:

if ($request->filled('password')) {
    $data['password'] = $request->password;
}
Mail Settings and .env

Laravel normally stores mail configuration in .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME=...

The Bishal Starter Kit additionally provides the mail_settings database structure for organization-specific configuration.

Therefore, database-based mail configuration should be loaded at the appropriate point in the application lifecycle before sending mail.

Troubleshooting
Connection Refused

Check:

SMTP host
SMTP port
Firewall
Server network
SMTP provider
Authentication Failed

Check:

SMTP username
SMTP password
SMTP authentication requirements
App password requirements
TLS/SSL Error

Check that the encryption matches the SMTP provider.

For example:

Port 587 → tls
Port 465 → ssl

The provider's SMTP documentation should be treated as the source of truth.

Emails Not Arriving

Check:

SMTP credentials
From address
Mail provider logs
Spam folder
DNS records
SPF
DKIM
DMARC

For production applications, proper email authentication records are important for deliverability.

Mail Configuration Fields
Field	Type	Default	Description
organization_id	Foreign key	null	Organization owning the settings
mailer	String	smtp	Mail transport
host	String	null	SMTP server
port	Integer	587	SMTP port
username	String	null	SMTP username
password	Text	null	SMTP password
encryption	String	null	tls, ssl, or no encryption
from_address	String	null	Default sender email
from_name	String	null	Default sender name
Summary

The Bishal Starter Kit provides database-backed mail configuration using:

mail_settings

The configuration supports:

SMTP
Custom SMTP host
Custom port
Username/password
TLS/SSL encryption
Organization-specific mail settings
Custom sender address
Custom sender name
Test email functionality

The overall flow is:

Organization
      ↓
Mail Settings
      ↓
SMTP Configuration
      ↓
Laravel Mail
      ↓
Email

This allows each organization to configure its own outgoing email settings while continuing to use Laravel's standard mail system.