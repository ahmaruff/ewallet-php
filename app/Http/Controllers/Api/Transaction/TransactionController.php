<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Commands\ResponseJsonCommand;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LogService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function __construct(
        protected LogService $logService,
        protected WalletService $walletService,
    ) {}

    public function deposit(Request $request)
    {
        $this->logService->request($request)->task('deposit')->start();

        $rules = [
            'amount' => ['required', 'numeric'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $user = Auth::user();

        if($user->wallet) {
            $wallet = $user->wallet; 
        } else {
            $wallet = $this->walletService->createWalletForUser($user);
        }

        try {
            if($wallet) {
                $trx = $this->walletService->depositFunds($wallet, $validated['amount']);
                $wallet->refresh();
                $data = [
                    'transaction' => $trx,
                    'user_id' => $user,
                    'balance' => (float) $wallet->balance
                ];

                $this->logService->status('success')
                    ->code(Response::HTTP_CREATED)
                    ->level('info')
                    ->message('funds deposited')
                    ->response($data)
                    ->save();

                return ResponseJsonCommand::responseSuccess(Response::HTTP_CREATED, $data, 'funds deposited');
            }    
            
            $this->logService->status('error')
                ->code(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->level('error')
                ->message('Wallet not found')
                ->save();
                
            return ResponseJsonCommand::responseError(Response::HTTP_INTERNAL_SERVER_ERROR, 'Wallet not found', $validated);
        } catch (\Throwable $th) {
            throw $th;
        }        
    }

    public function withdraw(Request $request)
    {
        $this->logService->request($request)->task('withdraw')->start();
        
        $rules = [
            'amount' => ['required', 'numeric'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $user = Auth::user();

        if($user->wallet) {
            $wallet = $user->wallet; 
        } else {
            $wallet = $this->walletService->createWalletForUser($user);
        }

        try {
            if($wallet) {
                $trx = $this->walletService->withdrawFunds($wallet, $validated['amount']);
                $wallet->refresh();
                $data = [
                    'transaction' => $trx,
                    'user_id' => $user,
                    'balance' => (float) $wallet->balance
                ];

                $this->logService->status('success')
                    ->code(Response::HTTP_CREATED)
                    ->level('info')
                    ->message('funds withdrawed')
                    ->response($data)
                    ->save();

                return ResponseJsonCommand::responseSuccess(Response::HTTP_CREATED, $data, 'funds withdrawed');
            }    
            
            $this->logService->status('error')
                ->code(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->level('error')
                ->message('Wallet not found')
                ->save();
                
            return ResponseJsonCommand::responseError(Response::HTTP_INTERNAL_SERVER_ERROR, 'Wallet not found', $validated);
        } catch (\Throwable $th) {
            throw $th;
        }        
    }
}
