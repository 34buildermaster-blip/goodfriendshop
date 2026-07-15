<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    use EnsuresAdminAccess;

    public function edit(): View
    {
        $this->ensureAdminAccess();
        SiteSetting::seedDefaults();

        return view('admin.site-settings.edit', [
            'settings' => SiteSetting::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();
        SiteSetting::seedDefaults();

        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:3000'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            SiteSetting::query()
                ->where('key', $key)
                ->update(['value' => $value]);
        }

        return redirect()
            ->route('admin.site-settings.edit')
            ->with('status', 'บันทึกตั้งค่าทั่วไปเรียบร้อยแล้ว');
    }
}
