<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\NotifyInactiveUser;

class UsersNotLoggedInForMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:users-not-logged-in-for-month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send an email notification to users who didn’t log in from the
    past month, and tell them that we miss you';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = Carbon::now()->subMonth(1);
        $inactive_user = User::where('last_login', '<', $limit)->get();

        foreach ($inactive_user as $user) {
            $user->notify(new NotifyInactiveUser());
        }
    }
}
