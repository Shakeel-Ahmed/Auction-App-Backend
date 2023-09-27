<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemAddRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Bid;
use Carbon\Carbon;

class ItemController extends Controller
{
    private string $formatDate;

    public function __construct()
    {
        $this->formatDate = 'j M Y g:i A';
    }

    public function list(Request $request)
    {
        // Get query parameters from the request
        $page = $request->input('page', 1); // Default to page 1
        $perPage = $request->input('per_page', 10); // Default to 10 items per page

        // Build the query to fetch items with the highest bid and bidder
        $itemsQuery = DB::table('items')
            ->select('items.*', DB::raw('MAX(bids.amount) as highest'))
            ->leftJoin('bids', 'items.item', '=', 'bids.item')
            ->where('items.status', 'active')
            ->where('items.publish', 1)
            ->where('items.expiry', '>', now()) // Items whose expiry has not yet occurred
            ->groupBy('items.item')
            ->orderBy('created_at', 'desc');

        // Paginate the results using query strings
        $items = $itemsQuery->paginate($perPage, ['*'], 'page', $page);

        $items->getCollection()->transform(function ($item) {
            $item->expiry = Carbon::parse($item->expiry)->format($this->formatDate);
            $item->created_at = Carbon::parse($item->created_at)->format($this->formatDate);
            $item->updated_at = Carbon::parse($item->updated_at)->format($this->formatDate);
            return $item;
        });

        return response()->json($items);
    }

    public function create(ItemAddRequest $request):JsonResponse
    {
        try {

            $item = new Item();

            $item->user =  $request->input('user');
            $item->name =  $request->input('name');
            $item->description = $request->input('description');
            $item->publish =  $request->input('publish');
            $item->expiry =  $request->input('expiry');
            $item->status =  $request->input('status');


            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'auction item successfully created',
                'data' => [
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'status' => $request->input('status')
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
    public function show($id)
    {
        // Fetch the item with the given ID, along with its highest bid
        $item = Item::find($id);

        // Check if the item exists
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'unable to create new user',
                'error' => ['code' => 404]
            ], 404);

        }

        // Find the highest bid for the item
        $highest = Bid::where('item', $id)->max('amount');

        $inputDate = Carbon::createFromFormat('Y-m-d H:i:s', $item->expiry);
        $formattedDate = $inputDate->format($this->formatDate);


        // Prepare the response data
        $responseData = [
            'id' => $item->item,
            'name' => $item->name,
            'description' => $item->description,
            'publish' => $item->publish,
            'expiry' => $formattedDate,
            'status' => $item->status,
            'highest' => $highest ?? 0, // Include the highest bid in the response
        ];

        return response()->json($responseData);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
    }
}
