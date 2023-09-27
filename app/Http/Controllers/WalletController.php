<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Http\Requests\WalletRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function transfer(WalletRequest $request):JsonResponse
    {
        $transaction = [
            'user' => $request->input('user'),
            'amount' => $request->input('amount'),
            'type' => $request->input('type'),
            'status' => $request->input('status')
        ];
        $wallet = new Wallet();
        $response  = $wallet->transfer($transaction);
        return response()->json($response,$response['code']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
