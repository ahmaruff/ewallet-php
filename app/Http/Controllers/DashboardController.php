<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected LogService $logService
    ) {}
    

    public function index(Request $request)
    {
        $this->logService->request($request)->task('get_dashboard');

        $user = Auth::user();

        $wallet = $user->wallet;

        $balance = 0;
        if($wallet) {
            $balance = $wallet->balance;
            $recentTransactions = Transaction::where('wallet_id',$wallet->id)->orderBy('created_at', 'desc')->limit(5)->get();
        }


        $data = [
            'balance' => (float) $balance,
            'recent_transactions' => $recentTransactions ?? [],
        ];

        // $this->logService->status('success')
        //     ->code(Response::HTTP_OK)
        //     ->level('info')
        //     ->message('get dashboard success')
        //     ->response($data)
        //     ->save();
        
        return Inertia::render('Dashboard', $data);
    }
}
