<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\ContentPost;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\PremiumApp;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use EnsuresAdminAccess;

    public function __invoke(): View
    {
        $this->ensureAdminAccess();

        return view('admin.dashboard', [
            'metrics' => [
                ['label' => 'เกมทั้งหมด', 'value' => Game::count(), 'note' => 'จัดการได้แล้ว', 'tone' => 'green'],
                ['label' => 'แพ็กเกจทั้งหมด', 'value' => GamePackage::count(), 'note' => 'ผูกกับเกม', 'tone' => 'cyan'],
                ['label' => 'แอพพรีเมียม', 'value' => PremiumApp::count(), 'note' => 'สินค้าแยกจากเกม', 'tone' => 'teal'],
                ['label' => 'ข่าว/กิจกรรม', 'value' => ContentPost::count(), 'note' => 'รอส่งขึ้นหน้าบ้าน', 'tone' => 'lime'],
                ['label' => 'สมาชิกทั้งหมด', 'value' => User::where('role', User::ROLE_CUSTOMER)->count(), 'note' => 'บัญชีลูกค้า', 'tone' => 'lime'],
            ],
            'recentGames' => Game::query()
                ->withCount('packages')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
