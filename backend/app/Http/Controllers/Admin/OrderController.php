<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    use EnsuresAdminAccess;

    public function index(Request $request): View
    {
        $this->ensureAdminAccess();

        $orders = Order::query()
            ->with(['user', 'game', 'package', 'premiumApp'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('type'), function ($query) use ($request) {
                if ($request->string('type')->toString() === 'premium_app') {
                    $query->whereNotNull('premium_app_id');
                }

                if ($request->string('type')->toString() === 'game') {
                    $query->whereNotNull('game_package_id');
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';

                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', $search)
                        ->orWhere('customer_name', 'like', $search)
                        ->orWhere('customer_email', 'like', $search)
                        ->orWhere('customer_phone', 'like', $search)
                        ->orWhere('game_name', 'like', $search)
                        ->orWhere('package_name', 'like', $search)
                        ->orWhere('player_identifier', 'like', $search);
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusLabels' => Order::statusLabels(),
            'paymentStatusLabels' => Order::paymentStatusLabels(),
        ]);
    }

    public function edit(Order $order): View
    {
        $this->ensureAdminAccess();

        return view('admin.orders.edit', [
            'order' => $order->load(['user', 'game', 'package', 'premiumApp']),
            'statusLabels' => Order::statusLabels(),
            'paymentStatusLabels' => Order::paymentStatusLabels(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->ensureAdminAccess();

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::statusLabels()))],
            'payment_method' => ['nullable', 'string', 'max:40'],
            'payment_status' => ['nullable', Rule::in(array_keys(Order::paymentStatusLabels()))],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_note' => ['nullable', 'string', 'max:5000'],
            'paid_at' => ['nullable', 'date'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['payment_status'] = $data['payment_status'] ?? $order->payment_status ?? Order::PAYMENT_UNPAID;

        if ($data['payment_status'] === Order::PAYMENT_PAID && blank($data['paid_at'] ?? null)) {
            $data['paid_at'] = now();
        }

        $order->update($data);

        app(OrderNotificationService::class)->orderUpdated($order->refresh());

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'อัปเดตออเดอร์เรียบร้อยแล้ว');
    }
}
