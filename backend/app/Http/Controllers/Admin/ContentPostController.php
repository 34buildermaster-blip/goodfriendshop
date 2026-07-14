<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\ContentPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentPostController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureAdminAccess();

        return view('admin.content-posts.index', [
            'posts' => ContentPost::query()
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->paginate(20),
            'statusLabels' => $this->statusLabels(),
            'typeLabels' => $this->typeLabels(),
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.content-posts.form', [
            'post' => new ContentPost([
                'type' => ContentPost::TYPE_NEWS,
                'status' => ContentPost::STATUS_DRAFT,
            ]),
            'statusLabels' => $this->statusLabels(),
            'typeLabels' => $this->typeLabels(),
            'action' => route('admin.content-posts.store'),
            'method' => 'POST',
            'title' => 'เพิ่มข่าวสาร/กิจกรรม',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        ContentPost::create($this->validatedPostData($request));

        return redirect()
            ->route('admin.content-posts.index')
            ->with('status', 'เพิ่มข่าวสาร/กิจกรรมเรียบร้อยแล้ว');
    }

    public function edit(ContentPost $contentPost): View
    {
        $this->ensureAdminAccess();

        return view('admin.content-posts.form', [
            'post' => $contentPost,
            'statusLabels' => $this->statusLabels(),
            'typeLabels' => $this->typeLabels(),
            'action' => route('admin.content-posts.update', $contentPost),
            'method' => 'PUT',
            'title' => 'แก้ไขข่าวสาร/กิจกรรม',
        ]);
    }

    public function update(Request $request, ContentPost $contentPost): RedirectResponse
    {
        $this->ensureAdminAccess();

        $contentPost->update($this->validatedPostData($request, $contentPost));

        return redirect()
            ->route('admin.content-posts.index')
            ->with('status', 'บันทึกข่าวสาร/กิจกรรมเรียบร้อยแล้ว');
    }

    public function destroy(ContentPost $contentPost): RedirectResponse
    {
        $this->ensureAdminAccess();

        $contentPost->delete();

        return redirect()
            ->route('admin.content-posts.index')
            ->with('status', 'ลบข่าวสาร/กิจกรรมเรียบร้อยแล้ว');
    }

    private function validatedPostData(Request $request, ?ContentPost $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:200',
                Rule::unique('content_posts', 'slug')->ignore($post),
            ],
            'type' => ['required', Rule::in(array_keys($this->typeLabels()))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:20000'],
            'cover_image_path' => ['nullable', 'string', 'max:255'],
            'cover_image_file' => [
                'nullable',
                'image',
                'mimes:webp,png,jpg,jpeg',
                'max:3072',
                'dimensions:min_width=800,min_height=420,max_width=3000,max_height=2000',
            ],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys($this->statusLabels()))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('cover_image_file')) {
            $file = $request->file('cover_image_file');
            $filename = $data['slug'].'-'.Str::random(8).'.'.$file->extension();

            $data['cover_image_path'] = $file->storeAs('content-posts', $filename, 'public');
        }

        unset($data['cover_image_file']);

        return $data;
    }

    private function statusLabels(): array
    {
        return [
            ContentPost::STATUS_PUBLISHED => 'เผยแพร่',
            ContentPost::STATUS_DRAFT => 'แบบร่าง',
            ContentPost::STATUS_ARCHIVED => 'เก็บถาวร',
        ];
    }

    private function typeLabels(): array
    {
        return [
            ContentPost::TYPE_NEWS => 'ข่าวสาร',
            ContentPost::TYPE_ACTIVITY => 'กิจกรรม',
        ];
    }
}
