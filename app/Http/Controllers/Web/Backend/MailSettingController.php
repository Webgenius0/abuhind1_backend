<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Services\EnvWriter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MailSettingController extends Controller
{
    private const ENV_MAP = [
        'mail_mailer'     => 'MAIL_MAILER',
        'mail_host'       => 'MAIL_HOST',
        'mail_port'       => 'MAIL_PORT',
        'mail_username'   => 'MAIL_USERNAME',
        'mail_password'   => 'MAIL_PASSWORD',
        'mail_encryption' => 'MAIL_ENCRYPTION',
        'mail_address'    => 'MAIL_FROM_ADDRESS',
    ];

    public function __construct(protected EnvWriter $envWriter)
    {
    }

    public function index(): View
    {
        return view('backend.v2.pages.settings.mail');
    }

    /**
     * Persist the SMTP settings to .env and apply them at runtime.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mail_mailer'     => 'required|string|in:smtp,sendmail,mailgun,postmark,log,ses',
            'mail_host'       => 'required|string|max:255',
            'mail_port'       => 'required|string|max:10',
            'mail_username'   => 'nullable|email',
            'mail_password'   => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_address'    => 'required|email',
        ]);

        try {
            $values = [];

            foreach (self::ENV_MAP as $field => $envKey) {
                $value = $data[$field] ?? null;

                if ($field === 'mail_encryption' && $value === 'null') {
                    $value = '';
                }

                $values[$envKey] = $value;
            }

            $this->envWriter->update($values);

            config([
                'mail.default'                  => $data['mail_mailer'],
                'mail.mailers.smtp.host'        => $data['mail_host'],
                'mail.mailers.smtp.port'        => $data['mail_port'],
                'mail.mailers.smtp.username'    => $data['mail_username'],
                'mail.mailers.smtp.password'    => $data['mail_password'],
                'mail.mailers.smtp.encryption'  => $data['mail_encryption'] === 'null' ? null : $data['mail_encryption'],
                'mail.from.address'             => $data['mail_address'],
            ]);

            return back()->with('t-success', 'Mail settings updated successfully.');
        } catch (Exception $e) {
            Log::error(self::class . ':update', ['error' => $e->getMessage()]);

            return back()->withInput()->with('t-error', 'Failed to update mail settings. Please try again.');
        }
    }

    /**
     * Send a test email using the current configuration.
     */
    public function test(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $recipient = $request->input('email');

            Mail::raw('This is a test email from ' . config('app.name') . '. Your SMTP configuration is working correctly!', function ($message) use ($recipient) {
                $message->to($recipient)->subject('Test Email - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $recipient . '.',
            ]);
        } catch (Exception $e) {
            Log::error(self::class . ':test', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }
}
