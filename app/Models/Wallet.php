<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;


class Wallet extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction';

    public static function balance($user)
    {
        $credit = Wallet::select('credits')->where('user', $user)->orderBy('created_at','desc')->first();
        return $credit->credits ?? 0;
    }
    public function transfer($transaction): array
    {
        try {

            $creditValue = 0;

            $wallet = new Wallet();

            $balance = $wallet->balance($transaction['user']);

            if($transaction['type'] !== 'credit' and $transaction['type'] !== 'debit') return [
                'success' => false,
                'message' => $transaction['type'].' is invalid transaction type',
                'code' => 401
            ];

            if($transaction['type'] === 'credit') {
                $creditValue = $balance + $transaction['amount'];
            } elseif ($transaction['type'] === 'debit' and $balance >=  $transaction['amount']) {
                $creditValue = $balance - $transaction['amount'];
            } elseif($transaction['type'] === 'debit' and $balance < $transaction['amount']) {
                return [
                    'success' => false,
                    'message' => 'insufficient balance',
                    'code' => 401,
                    'data' => [
                        'amount' => $transaction['amount'],
                        'credits' => $balance,
                        'type' => $transaction['type'],
                        'status' => 'declined',
                    ]
                ];
            }

            $wallet->user = $transaction['user'];
            $wallet->amount = $transaction['amount'];
            $wallet->credits = $creditValue;
            $wallet->type = $transaction['type'];
            $wallet->status = $transaction['status'];

            $wallet->save();



            return [
                'success' => true,
                'message' => $wallet->amount.' has been credited in your account',
                'code' => 201,
                'data' => [
                    'amount' => $wallet->amount,
                    'credits' => $wallet->credits,
                    'user' => $wallet->user,
                    'type' => $wallet->type,
                    'status' => $wallet->status,
                ]
            ];
        } catch (QueryException $e) {
            return [
                'success' => false,
                'message' => 'transaction failed',
                'code' => 500,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'info' =>  $e->errorInfo,
                ]
            ];
        }
    }
}
