<?php

namespace App\Console\Commands;

use App\Models\ResidentListing;
use Illuminate\Console\Command;

class ExpireResidentListings extends Command
{
    protected $signature = 'resident-listings:expire';
    protected $description = 'Mark expired resident listings and update their ad_status';

    public function handle(): int
    {
        $count = ResidentListing::where('ad_status', 'active')
            ->where('ad_expires_at', '<=', now())
            ->whereNotNull('ad_expires_at')
            ->update(['ad_status' => 'expired']);

        $this->info("Expired {$count} resident listing(s).");
        return self::SUCCESS;
    }
}
