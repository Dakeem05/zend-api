<?php

namespace App\Console\Commands\APi\V1;

use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NullifyOldOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:nullify-old-otp';

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
        $instance = PasswordResetToken::where('expires_at', '<=', Carbon::now())->delete();
        $this->info('Old OTPs nullified successfully.');
    }
}
