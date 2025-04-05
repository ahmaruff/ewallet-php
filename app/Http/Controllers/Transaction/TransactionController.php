<?php

namespace App\Http\Controllers\Transaction;

use App\Commands\ModelFilterQueryCommand;
use App\Commands\PaginationInfoCommand;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function __construct(
        protected LogService $logService,
    ) {}

    public function index(Request $request)
    {
        $startDate = $request->query('start_date', null);
        $endDate = $request->query('end_date', null);
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        if(!$startDate) {
            $startDate = Carbon::now()->subMonth()->startOfDay()->toDateString();
        }

        if(!$endDate) {
            $endDate = Carbon::now()->endOfDay()->toDateString();
        }

        $user = Auth::user();
        $wallet = $user->wallet;

        if($wallet) {
            $query = Transaction::query()
                ->where('wallet_id', $wallet->id);
            
            $query = ModelFilterQueryCommand::filterByDate($query, 'created_at', $startDate, $endDate);
            $trx = ModelFilterQueryCommand::paginate($query, $perPage, $page);

            $data = [
                'transactions' => $trx->getCollection(),
                'pagination' => PaginationInfoCommand::execute($trx, $perPage),
            ];
            return Inertia::render('Transaction', $data);
        }

        $data = [
            'transactions' => null,
            'pagination' => null,
        ];

        return Inertia::render('Transaction', $data);
    }
}
