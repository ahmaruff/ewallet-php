<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $wallet = $user->wallet;

        $balance = 0;
        if($wallet) {
            $balance = $wallet->balance;
        }

        $data = [
            'balance' => $balance
        ];

        return Inertia::render('Dashboard', $data);
    }
}
