<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GameProductController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.index', [
            'games' => Game::query()
                ->withCount('packages')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.game-form', [
            'game' => new Game(['status' => Game::STATUS_DRAFT]),
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.products.store'),
            'method' => 'POST',
            'title' => 'เพิ่มเกมใหม่',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        Game::create($this->validatedGameData($request));

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'เพิ่มเกมเรียบร้อยแล้ว');
    }

    public function edit(Game $game): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.game-form', [
            'game' => $game,
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.products.update', $game),
            'method' => 'PUT',
            'title' => 'แก้ไขเกม',
        ]);
    }

    public function update(Request $request, Game $game): RedirectResponse
    {
        $this->ensureAdminAccess();

        $game->update($this->validatedGameData($request, $game));

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'บันทึกข้อมูลเกมเรียบร้อยแล้ว');
    }

    public function destroy(Game $game): RedirectResponse
    {
        $this->ensureAdminAccess();

        $game->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'ลบเกมเรียบร้อยแล้ว');
    }

    private function validatedGameData(Request $request, ?Game $game = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:180',
                Rule::unique('games', 'slug')->ignore($game),
            ],
            'publisher' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'image_file' => [
                'nullable',
                'image',
                'mimes:webp,png,jpg,jpeg',
                'max:2048',
                'dimensions:min_width=400,min_height=400,max_width=2000,max_height=2000,ratio=1/1',
            ],
            'status' => ['required', Rule::in(array_keys($this->statusLabels()))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $extension = $file->extension();
            $filename = $data['slug'].'-'.Str::random(8).'.'.$extension;

            $data['image_path'] = $file->storeAs('games', $filename, 'public');
        }

        unset($data['image_file']);

        return $data;
    }

    private function statusLabels(): array
    {
        return [
            Game::STATUS_ACTIVE => 'เปิดขาย',
            Game::STATUS_DRAFT => 'แบบร่าง',
            Game::STATUS_INACTIVE => 'ปิดขาย',
        ];
    }
}
