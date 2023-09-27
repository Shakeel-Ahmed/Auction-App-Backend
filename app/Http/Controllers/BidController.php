<?php

namespace App\Http\Controllers;

use App\Http\Requests\BidRequest;
use App\Models\Bid;
use App\Models\Item;
//use http\Env\Response;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidController extends Controller
{
    public function register(BidRequest $request):JsonResponse
    {
        try {
            $balance = Wallet::balance($request->input('bidder'));
            $highest = Bid::where('item', $request->input('item'))->orderBy('amount','desc')->first();
            $userBid = Bid::where('item', $request->input('item'))->where('bidder', $request->input('bidder'))->orderBy('amount','desc')->first();

             if($highest) $highestAmount = $highest->amount;
             else $highestAmount = 0;

            $bidAmount = $request->input('amount');
            if($userBid) $reqBalance = $bidAmount - $userBid->amount;
            else $reqBalance = $bidAmount;

            if ($bidAmount <= $highestAmount || $balance < $reqBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'in-sufficient funds or low bid',
                    'data' => [
                        'bid' => $bidAmount,
                        'existing' => $highestAmount,
                        'balance' => $balance
                    ]
                ], 422);
            }
            // Create a new Bid instance with the validated data
            $bid = new Bid();
            $bid->bidder = $request->input('bidder');
            $bid->item = $request->input('item');
            $bid->amount =  $request->input('amount');
            $bid->status = $request->input('status');
            $bid->save();

            $transaction = [
                'user' => $request->input('bidder'),
                'amount' => $reqBalance,
                'type' => 'debit',
                'status' => 'bid on item #'.$request->input('item')
            ];

            $wallet = new Wallet();
            $response  = $wallet->transfer($transaction);

            if ($response['code'] === 201) return response()->json([
                'success' => true,
                'message' => 'bid raised to $'.$bid->amount,
                'data' => [
                    'amount' =>  $bid->amount,
                    'bidder' => $bid->bidder,
                    'item' => $bid->item,
                ]
            ], 201);
            else return response()->json($response);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'failed to create bid for this item',
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'info' =>  $e->errorInfo,
                ]
            ], 500);
        }
    }

    public function show(Bid $bid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bid $bid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bid $bid)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bid $bid)
    {
        //
    }
}
