<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GamePackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GamePackageController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.packages-index', [
            'games' => Game::query()
                ->with(['packages' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('name')])
                ->withCount('packages')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'packageTotal' => GamePackage::query()->count(),
            'requiredFieldLabels' => $this->requiredFieldLabels(),
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function createAny(): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.package-form', [
            'game' => null,
            'games' => Game::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'package' => new GamePackage([
                'status' => GamePackage::STATUS_DRAFT,
                'currency' => 'THB',
                'required_fields' => ['uid'],
            ]),
            'requiredFieldLabels' => $this->requiredFieldLabels(),
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.packages.store'),
            'method' => 'POST',
            'title' => 'เพิ่มแพ็กเกจ',
        ]);
    }

    public function create(Game $game): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.package-form', [
            'game' => $game,
            'package' => new GamePackage([
                'status' => GamePackage::STATUS_DRAFT,
                'currency' => 'THB',
                'required_fields' => ['uid'],
            ]),
            'requiredFieldLabels' => $this->requiredFieldLabels(),
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.products.packages.store', $game),
            'method' => 'POST',
            'title' => 'เพิ่มแพ็กเกจ',
        ]);
    }

    public function store(Request $request, Game $game): RedirectResponse
    {
        $this->ensureAdminAccess();

        $game->packages()->create($this->validatedPackageData($request));

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'เพิ่มแพ็กเกจเรียบร้อยแล้ว');
    }

    public function storeAny(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        $game = Game::query()->findOrFail($request->validate([
            'game_id' => ['required', Rule::exists('games', 'id')],
        ])['game_id']);

        $game->packages()->create($this->validatedPackageData($request));

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'เพิ่มแพ็กเกจเรียบร้อยแล้ว');
    }

    public function edit(GamePackage $package): View
    {
        $this->ensureAdminAccess();

        return view('admin.products.package-form', [
            'game' => $package->game,
            'package' => $package,
            'requiredFieldLabels' => $this->requiredFieldLabels(),
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.packages.update', $package),
            'method' => 'PUT',
            'title' => 'แก้ไขแพ็กเกจ',
        ]);
    }

    public function update(Request $request, GamePackage $package): RedirectResponse
    {
        $this->ensureAdminAccess();

        $package->update($this->validatedPackageData($request, $package));

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'บันทึกข้อมูลแพ็กเกจเรียบร้อยแล้ว');
    }

    public function destroy(GamePackage $package): RedirectResponse
    {
        $this->ensureAdminAccess();

        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'ลบแพ็กเกจเรียบร้อยแล้ว');
    }

    private function validatedPackageData(Request $request, ?GamePackage $package = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'sku' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('game_packages', 'sku')->ignore($package),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'required_fields' => ['nullable', 'array'],
            'required_fields.*' => [Rule::in(array_keys($this->requiredFieldLabels()))],
            'status' => ['required', Rule::in(array_keys($this->statusLabels()))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $data['currency'] = strtoupper($data['currency']);
        $data['required_fields'] = array_values($data['required_fields'] ?? []);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function requiredFieldLabels(): array
    {
        return [
            'uid' => 'UID / Player ID',
            'server_id' => 'Server ID',
            'character_name' => 'ชื่อตัวละคร',
            'login_email' => 'อีเมลบัญชีเกม',
            'login_password' => 'รหัสผ่านบัญชีเกม',
        ];
    }

    private function statusLabels(): array
    {
        return [
            GamePackage::STATUS_ACTIVE => 'เปิดขาย',
            GamePackage::STATUS_DRAFT => 'แบบร่าง',
            GamePackage::STATUS_INACTIVE => 'ปิดขาย',
        ];
    }
}
