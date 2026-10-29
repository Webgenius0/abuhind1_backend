<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Services\EnvWriter;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class IntegrationSettingController extends Controller
{
    public function __construct(protected EnvWriter $envWriter)
    {
    }

    public function index(): View
    {
        $activeTab = session('active_tab', 'apple');

        return view('backend.v2.pages.settings.integration', compact('activeTab'));
    }

    /**
     * Update Google OAuth credentials.
     */
    public function updateGoogle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'GOOGLE_CLIENT_ID'     => 'nullable|string|max:255',
            'GOOGLE_CLIENT_SECRET' => 'nullable|string|max:255',
            'GOOGLE_REDIRECT_URI'  => 'nullable|string|max:255',
        ]);

        try {
            $this->envWriter->update([
                'GOOGLE_CLIENT_ID'     => $data['GOOGLE_CLIENT_ID'] ?? null,
                'GOOGLE_CLIENT_SECRET' => $data['GOOGLE_CLIENT_SECRET'] ?? null,
                'GOOGLE_REDIRECT_URI'  => $data['GOOGLE_REDIRECT_URI'] ?? null,
            ]);

            return back()->with(['t-success' => 'Google settings updated successfully.', 'active_tab' => 'google']);
        } catch (Exception $e) {
            return back()->withInput()->with(['t-error' => 'Failed to update Google settings: ' . $e->getMessage(), 'active_tab' => 'google']);
        }
    }

    /**
     * Update Apple Sign-In credentials.
     */
    public function updateApple(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'APPLE_CLIENT_ID' => 'required|string|max:255',
            'APPLE_TEAM_ID'   => 'required|string|max:255',
            'APPLE_KEY_ID'    => 'required|string|max:255',
            'apple_key_file'  => 'nullable|file|max:2048',
        ]);

        try {
            $values = [
                'APPLE_CLIENT_ID' => $data['APPLE_CLIENT_ID'],
                'APPLE_TEAM_ID'   => $data['APPLE_TEAM_ID'],
                'APPLE_KEY_ID'    => $data['APPLE_KEY_ID'],
            ];

            if ($request->hasFile('apple_key_file')) {
                $file = $request->file('apple_key_file');
                $fileName = $file->getClientOriginalName() ?: ('AuthKey_' . $data['APPLE_KEY_ID'] . '.p8');
                $directory = storage_path('app/private');

                if (! File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $file->move($directory, $fileName);

                $values['APPLE_PRIVATE_KEY'] = 'app/private/' . $fileName;
            }

            $this->envWriter->update($values);

            return back()->with(['t-success' => 'Apple settings updated successfully.', 'active_tab' => 'apple']);
        } catch (Exception $e) {
            return back()->withInput()->with(['t-error' => 'Failed to update Apple settings: ' . $e->getMessage(), 'active_tab' => 'apple']);
        }
    }

    /**
     * Update Google Gemini AI credentials.
     */
    public function updateGemini(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'GEMINI_API_KEY'  => 'nullable|string|max:500',
            'GEMINI_MODEL'    => 'nullable|string|max:255',
            'GEMINI_API_BASE' => 'nullable|string|max:500',
        ]);

        try {
            $this->envWriter->update([
                'GEMINI_API_KEY'  => $data['GEMINI_API_KEY'] ?? null,
                'GEMINI_MODEL'    => $data['GEMINI_MODEL'] ?? null,
                'GEMINI_API_BASE' => $data['GEMINI_API_BASE'] ?? null,
            ]);

            return back()->with(['t-success' => 'Gemini AI settings updated successfully.', 'active_tab' => 'gemini']);
        } catch (Exception $e) {
            return back()->withInput()->with(['t-error' => 'Failed to update Gemini settings: ' . $e->getMessage(), 'active_tab' => 'gemini']);
        }
    }
}
