<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\Order;
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
            ->with(['user', 'game', 'package'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
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
        ]);
    }

    public function edit(Order $order): View
    {
        $this->ensureAdminAccess();

        return view('admin.orders.edit', [
            'order' => $order->load(['user', 'game', 'package']),
            'statusLabels' => Order::statusLabels(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->ensureAdminAccess();

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::statusLabels()))],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $order->update($data);

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'อัปเดตออเดอร์เรียบร้อยแล้ว');
    }
}
