<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Item;
use App\Models\Bid;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConcludeAuction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:conclude';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    // Step 1: Get items with expired auction date and publish = 1
        $expiredItems = Item::where('expiry', '<=', now())
            ->where('publish', '1')
            ->get();

        foreach ($expiredItems as $item) {
            // Step 2: Find the user with the highest bid amount for this item
            $highestBidder = Bid::where('item', $item->item)
                ->orderByDesc('amount')
                ->first();

            if ($highestBidder) {
                $itemId = $item->item;
                $bidderId = $highestBidder->bidder;

                // Step 3: Update the item's publish column to "2"
                $item->update(['publish' => '2']);

                // Step 4: Update the bid table status to "winner"
                Bid::where('item', $itemId)->where('bidder', $bidderId)
                    ->update(['status' => 'winner']);

                // Step 5: Get all users who bid on this item except the bid winner
                $otherBidders = Bid::where('item', $itemId)
                    ->where('bidder', '!=', $bidderId)
                    ->get();

                // Step 6: Get the max bid amount of each user
                $maxBidAmounts = $otherBidders->groupBy('bidder')
                    ->map(function ($bids) {
                        return $bids->max('amount');
                    });

                // Step 7: Refunding to non-winner bidders accounts
                foreach ($otherBidders as $bid) {
                    $maxBidAmount = $maxBidAmounts[$bid->bidder];

                    $latestCredits = Wallet::where('user', $bid->bidder)
                        ->latest('created_at')
                        ->value('credits');

                    $wallet = new Wallet();
                    $wallet->user = $bid->bidder;
                    $wallet->amount = $maxBidAmount;
                    $wallet->credits = $maxBidAmount + $latestCredits;
                    $wallet->type = 'credit';
                    $wallet->status = "Refund from the bid item {$item->name} #{$item->item}";
                    $wallet->save();
                }
            }
        }

    }
}
