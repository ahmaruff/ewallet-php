<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected LogService $logService,
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('roles');
        $isAdmin = $user->hasRole('admin');

        $userCount = User::count();
        $totalTransaction = Transaction::sum('amount');
        $recentTransactions = Transaction::orderBy('created_at', 'desc')->limit(5)->get();
        $averageTransaction = Transaction::avg('amount');

        $data = [
            'user_count' => $userCount,
            'total_transaction' => $totalTransaction,
            'recent_transaction' => $recentTransactions,
            'averageTransaction' => $averageTransaction,
            'is_admin' => $isAdmin,
            'user' => $user,
        ];

        return Inertia::render('admin/Dashboard', $data);
    }
}
