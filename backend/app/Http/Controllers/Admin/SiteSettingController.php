<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    use EnsuresAdminAccess;

    public function edit(): View
    {
        $this->ensureAdminAccess();
        SiteSetting::seedDefaults();

        return view('admin.site-settings.edit', [
            'settings' => SiteSetting::query()
                ->whereIn('key', array_keys(SiteSetting::DEFAULTS))
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();
        SiteSetting::seedDefaults();

        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:3000'],
            'logo_file' => [
                'nullable',
                'image',
                'mimes:webp,png,jpg,jpeg',
                'max:2048',
                'dimensions:min_width=120,min_height=40,max_width=2000,max_height=1000',
            ],
        ]);

        foreach ($data['settings'] as $key => $value) {
            SiteSetting::query()
                ->where('key', $key)
                ->update(['value' => $value]);
        }

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $filename = 'logo-'.Str::random(8).'.'.$file->extension();
            $path = $file->storeAs('site', $filename, 'public');

            SiteSetting::query()
                ->where('key', 'logo_path')
                ->update(['value' => Storage::url($path)]);
        }

        return redirect()
            ->route('admin.site-settings.edit')
            ->with('status', 'บันทึกตั้งค่าทั่วไปเรียบร้อยแล้ว');
    }
}
