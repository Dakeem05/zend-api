<?php

namespace App\Console\Commands\Api\V1;

use App\Mail\AdminWithdrawFailed;
use App\Mail\AdminWithdrawRequestPayment;
use App\Mail\UserWithdrawFailed;
use App\Mail\UserWithdrawRequestPayment;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-withdrawals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users that have withdrawn.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = Transaction::where('type', 'withdrawal')->where('status', '!=', 'pending')->where('sent_mail', false)->get();

        if ($transactions !== null) {
            foreach ($transactions as $key => $transaction) {
                if ($transaction->status === 'confirmed') {
                    $wallet = Wallet::where('user_id', $transaction->user_id)->with('user')->first();
                    $name = strtoupper($wallet->user->name !== null ? $wallet->user->name : $wallet->user->username);
                    Notification::Notify($transaction->user_id, "Your requested withdrawal of ₦".$transaction->amount. ' has been paid.');
                    Mail::to($wallet->user->email)->send(new UserWithdrawRequestPayment($name, $transaction->amount, $wallet->main_balance));
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $key => $admin) {
                        Mail::to($admin->email)->send(new AdminWithdrawRequestPayment($name, $transaction->amount));
                        Notification::Notify($admin->id, "$name's request withdrawal of ₦$transaction->amount has been paid.");
                    }
                    $transaction->update([
                        'sent_mail' => true
                    ]);
                    $this->info('Notifications sent successfully.');
                } 
                $wallet = Wallet::where('user_id', $transaction->user_id)->with('user')->first();
                $name = strtoupper($wallet->user->name !== null ? $wallet->user->name : $wallet->user->username);
                Notification::Notify($transaction->user_id, "Your requested withdrawal of ₦".$transaction->amount.' has failed.');
                Mail::to($wallet->user->email)->send(new UserWithdrawFailed($name, $transaction->amount, $wallet->main_balance));
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $key => $admin) {
                    Mail::to($admin->email)->send(new AdminWithdrawFailed($name, $transaction->amount));
                    Notification::Notify($admin->id, "$name's request withdrawal of ₦$transaction->amount has failed.");
                }
                $transaction->update([
                    'sent_mail' => true
                ]);
                $this->info('Notifications sent successfully.');
            }
        }
    }
}
