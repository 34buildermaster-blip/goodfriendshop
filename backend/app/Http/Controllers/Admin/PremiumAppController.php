<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\PremiumApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PremiumAppController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureAdminAccess();

        return view('admin.premium-apps.index', [
            'apps' => PremiumApp::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(20),
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.premium-apps.form', [
            'app' => new PremiumApp([
                'status' => PremiumApp::STATUS_DRAFT,
                'currency' => 'THB',
                'delivery_type' => PremiumApp::DELIVERY_MANUAL_SERVICE,
                'stock_status' => PremiumApp::STOCK_IN_STOCK,
            ]),
            'statusLabels' => $this->statusLabels(),
            'deliveryTypeLabels' => PremiumApp::deliveryTypeLabels(),
            'stockStatusLabels' => PremiumApp::stockStatusLabels(),
            'customerFieldLabels' => PremiumApp::customerFieldLabels(),
            'action' => route('admin.premium-apps.store'),
            'method' => 'POST',
            'title' => 'เพิ่มแอพพรีเมียม',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        PremiumApp::create($this->validatedAppData($request));

        return redirect()
            ->route('admin.premium-apps.index')
            ->with('status', 'เพิ่มแอพพรีเมียมเรียบร้อยแล้ว');
    }

    public function edit(PremiumApp $premiumApp): View
    {
        $this->ensureAdminAccess();

        return view('admin.premium-apps.form', [
            'app' => $premiumApp,
            'statusLabels' => $this->statusLabels(),
            'deliveryTypeLabels' => PremiumApp::deliveryTypeLabels(),
            'stockStatusLabels' => PremiumApp::stockStatusLabels(),
            'customerFieldLabels' => PremiumApp::customerFieldLabels(),
            'action' => route('admin.premium-apps.update', $premiumApp),
            'method' => 'PUT',
            'title' => 'แก้ไขแอพพรีเมียม',
        ]);
    }

    public function update(Request $request, PremiumApp $premiumApp): RedirectResponse
    {
        $this->ensureAdminAccess();

        $premiumApp->update($this->validatedAppData($request, $premiumApp));

        return redirect()
            ->route('admin.premium-apps.index')
            ->with('status', 'บันทึกข้อมูลแอพพรีเมียมเรียบร้อยแล้ว');
    }

    public function destroy(PremiumApp $premiumApp): RedirectResponse
    {
        $this->ensureAdminAccess();

        $premiumApp->delete();

        return redirect()
            ->route('admin.premium-apps.index')
            ->with('status', 'ลบแอพพรีเมียมเรียบร้อยแล้ว');
    }

    private function validatedAppData(Request $request, ?PremiumApp $app = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:180',
                Rule::unique('premium_apps', 'slug')->ignore($app),
            ],
            'provider' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'image_file' => [
                'nullable',
                'image',
                'mimes:webp,png,jpg,jpeg',
                'max:2048',
                'dimensions:min_width=400,min_height=400,max_width=2000,max_height=2000,ratio=1/1',
            ],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'delivery_type' => ['required', Rule::in(array_keys(PremiumApp::deliveryTypeLabels()))],
            'customer_required_fields' => ['nullable', 'array'],
            'customer_required_fields.*' => ['string', Rule::in(array_keys(PremiumApp::customerFieldLabels()))],
            'warranty_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'stock_status' => ['required', Rule::in(array_keys(PremiumApp::stockStatusLabels()))],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'supplier_contact' => ['nullable', 'string', 'max:255'],
            'fulfillment_note' => ['nullable', 'string', 'max:5000'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(array_keys($this->statusLabels()))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['currency'] = strtoupper($data['currency']);
        $data['customer_required_fields'] = array_values($data['customer_required_fields'] ?? []);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = $data['slug'].'-'.Str::random(8).'.'.$file->extension();

            $data['image_path'] = $file->storeAs('premium-apps', $filename, 'public');
        }

        unset($data['image_file']);

        return $data;
    }

    private function statusLabels(): array
    {
        return [
            PremiumApp::STATUS_ACTIVE => 'เปิดขาย',
            PremiumApp::STATUS_DRAFT => 'แบบร่าง',
            PremiumApp::STATUS_INACTIVE => 'ปิดขาย',
        ];
    }
}
