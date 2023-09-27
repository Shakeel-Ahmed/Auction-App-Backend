<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use App\Http\Requests\UserLoginRequest;
use App\Models\Wallet;
use http\Client\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(UserLoginRequest $request): JsonResponse
    {
        if (auth()->attempt(['email' => request('email'), 'password' => request('password')]))
        {
            $user = User::where('email', request('email'))->first();
            return response()->json([
                'success' => true,
                'message' => 'successfully authenticated',
                'data' => [
                    'name' => $user->name,
                    'token'=> $user->remember_token,
                    'user' => $user->id,
                ]
            ], 201);
        } else return response()->json([
            'success' => false,
            'message' => 'un-authenticated user',
            'data' => false
        ], 404);
    }
    public function register(UserRegistrationRequest $request): JsonResponse
    {
        try {
            $token = Str::random(60);
            $user = new User();
            $user->name = request('name');
            $user->email = request('email');
            $user->password = request('password');
            $user->remember_token = $token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'user successfully added into database',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'remember_token' => $user->remember_token,
                ]
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'unable to create new user',
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'info' =>  $e->errorInfo,
                ]
            ], 401);
        }
    }

    public function show($id):JsonResponse {

        $user = User::find($id);

        $userItems = Item::where('user', $id)->get();
        $biddedItems = Item::whereIn('item', function ($query) use ($id) {
            $query->select('item')->from('bids')->where('bidder', $id)->distinct();
        })->get();

        $userItems = $userItems->map(function ($item) {
            return $this->transformItem($item);
        });

        $biddedItems = $biddedItems->map(function ($item) {
            return $this->transformItem($item);
        });

        return response()->json([
            'success' => true,
            'message' => 'user data retrieved',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'balance' => Wallet::balance($id),
                'user_bids' => $biddedItems,
                'user_items' => $userItems,
            ],
        ]);
    }

    private function transformItem($item):array {
        return [
            'id' => $item->item,
            'name' => $item->name,
            'description' => $item->description,
            'publish' => $item->publish,
            'expiry' => $item->expiry,
            'status' => $item->status,
        ];
    }

}
