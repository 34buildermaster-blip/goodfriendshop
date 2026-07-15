<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureAdminAccess();

        return view('admin.hero-slides.index', [
            'slides' => HeroSlide::query()->orderBy('sort_order')->orderBy('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.hero-slides.form', [
            'slide' => new HeroSlide(['is_active' => true, 'cta_url' => '/games']),
            'action' => route('admin.hero-slides.store'),
            'method' => 'POST',
            'title' => 'เพิ่มสไลด์หน้าแรก',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        HeroSlide::create($this->validatedSlideData($request));

        return redirect()->route('admin.hero-slides.index')->with('status', 'เพิ่มสไลด์เรียบร้อยแล้ว');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        $this->ensureAdminAccess();

        return view('admin.hero-slides.form', [
            'slide' => $heroSlide,
            'action' => route('admin.hero-slides.update', $heroSlide),
            'method' => 'PUT',
            'title' => 'แก้ไขสไลด์หน้าแรก',
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $this->ensureAdminAccess();

        $heroSlide->update($this->validatedSlideData($request));

        return redirect()->route('admin.hero-slides.index')->with('status', 'บันทึกสไลด์เรียบร้อยแล้ว');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $this->ensureAdminAccess();

        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('status', 'ลบสไลด์เรียบร้อยแล้ว');
    }

    private function validatedSlideData(Request $request): array
    {
        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:180'],
            'highlight' => ['nullable', 'string', 'max:180'],
            'quote' => ['nullable', 'string', 'max:600'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'image_file' => [
                'nullable',
                'image',
                'mimes:webp,png,jpg,jpeg',
                'max:4096',
                'dimensions:min_width=900,min_height=420,max_width=4000,max_height=2400',
            ],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = Str::slug($data['title']).'-'.Str::random(8).'.'.$file->extension();
            $data['image_path'] = $file->storeAs('hero-slides', $filename, 'public');
        }

        unset($data['image_file']);

        return $data;
    }
}
