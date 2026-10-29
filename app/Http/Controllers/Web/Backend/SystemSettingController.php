<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    public function index(): View
    {
        $setting = SystemSetting::first();

        return view('backend.v2.pages.settings.system', compact('setting'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'title'               => 'nullable|string|max:255',
            'system_name'         => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:50',
            'address'             => 'nullable|string|max:500',
            'copyright_text'      => 'nullable|string|max:500',
            'timezone'            => 'nullable|string|max:100',
            'maintenance_mode'    => 'nullable|boolean',
            'maintenance_message' => 'nullable|string',
            'description'         => 'nullable|string',
            'logo'                => 'nullable|image|mimes:png,jpg,jpeg,gif,svg,webp|max:4096',
            'favicon'             => 'nullable|image|mimes:png,jpg,jpeg,gif,svg,ico,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $setting = SystemSetting::first() ?? new SystemSetting();

            $setting->title               = $request->input('title');
            $setting->system_name         = $request->input('system_name');
            $setting->email               = $request->input('email');
            $setting->phone               = $request->input('phone');
            $setting->address             = $request->input('address');
            $setting->copyright_text      = $request->input('copyright_text');
            $setting->timezone            = $request->input('timezone');
            $setting->maintenance_mode    = $request->boolean('maintenance_mode');
            $setting->maintenance_message = $request->input('maintenance_message');
            $setting->description         = $request->input('description');

            if ($request->hasFile('logo')) {
                $setting->logo = uploadImage($request->file('logo'), 'logos');
            }

            if ($request->hasFile('favicon')) {
                $setting->favicon = uploadImage($request->file('favicon'), 'favicons');
            }

            $setting->save();

            return back()->with('t-success', 'System settings updated successfully.');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update system settings.');
        }
    }
}
