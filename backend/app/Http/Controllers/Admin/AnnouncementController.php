<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureAdminAccess();

        return view('admin.announcements.index', [
            'announcements' => Announcement::query()->orderBy('sort_order')->orderBy('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.announcements.form', [
            'announcement' => new Announcement(['is_active' => true]),
            'action' => route('admin.announcements.store'),
            'method' => 'POST',
            'title' => 'เพิ่มประกาศ',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        Announcement::create($this->validatedAnnouncementData($request));

        return redirect()->route('admin.announcements.index')->with('status', 'เพิ่มประกาศเรียบร้อยแล้ว');
    }

    public function edit(Announcement $announcement): View
    {
        $this->ensureAdminAccess();

        return view('admin.announcements.form', [
            'announcement' => $announcement,
            'action' => route('admin.announcements.update', $announcement),
            'method' => 'PUT',
            'title' => 'แก้ไขประกาศ',
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->ensureAdminAccess();

        $announcement->update($this->validatedAnnouncementData($request));

        return redirect()->route('admin.announcements.index')->with('status', 'บันทึกประกาศเรียบร้อยแล้ว');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->ensureAdminAccess();

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'ลบประกาศเรียบร้อยแล้ว');
    }

    private function validatedAnnouncementData(Request $request): array
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
