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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ManuallyCheckWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manually-check-withdrawals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually check for withdrawals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = Transaction::where('type', 'withdrawal')->where('status', 'pending')->get();

        if ($transactions !== null){
            foreach ($transactions as $key => $transaction) {
                $response = Http::withHeaders([
                    "Authorization"=> 'Bearer '.env('FLW_SECRET_KEY'),
                    "Cache-Control" => 'no-cache',
                ])->get(env('FLW_PAYMENT_URL').'/transfers/'.$transaction->tnx_id);
                $res = json_decode($response->getBody());
    
                if ($res->status === "success") {
                    if ($res->data->status === "SUCCESSFUL") {
                        $transaction->update([
                            'status' => 'confirmed',
                            'flw_status' => $res->data->status,
                            'sent_mail' => true
                        ]);
                        $wallet = Wallet::where('user_id', $transaction->user_id)->with('user')->first();
                        $name = strtoupper($wallet->user->name !== null ? $wallet->user->name : $wallet->user->username);
                        Notification::Notify($transaction->user_id, "Your requested withdrawal of ₦".$transaction->amount. ' has been paid.');
                        Mail::to($wallet->user->email)->send(new UserWithdrawRequestPayment($name, $transaction->amount, $wallet->main_balance));
                        $admins = User::where('role', 'admin')->get();
                        foreach ($admins as $key => $admin) {
                            Mail::to($admin->email)->send(new AdminWithdrawRequestPayment($name, $transaction->amount));
                            Notification::Notify($admin->id, "$name's request withdrawal of ₦$transaction->amount has been paid.");
                        }
                        $this->info('Notifications sent successfully.');
                    } else if ($res->data->status === "FAILED" ){
                        $transaction->update([
                            'status' => 'declined',
                            'flw_status' => $res->data->status,
                            'sent_mail' => true
                        ]);
                        $this->updateWalletBalance($transaction->user_id, $transaction->amount, 'credit');
                        $wallet = Wallet::where('user_id', $transaction->user_id)->with('user')->first();
                        $name = strtoupper($wallet->user->name !== null ? $wallet->user->name : $wallet->user->username);
                        Notification::Notify($transaction->user_id, "Your requested withdrawal of ₦".$transaction->amount.' has failed.');
                        Mail::to($wallet->user->email)->send(new UserWithdrawFailed($name, $transaction->amount, $wallet->main_balance));
                        $admins = User::where('role', 'admin')->get();
                        foreach ($admins as $key => $admin) {
                            Mail::to($admin->email)->send(new AdminWithdrawFailed($name, $transaction->amount));
                            Notification::Notify($admin->id, "$name's request withdrawal of ₦$transaction->amount has failed.");
                        }
                        $this->info('Notifications sent successfully.');
                    }
                } 
                $this->info('Nothing to notifify.');
            }
        }
    }

    private function updateWalletBalance(int $user_id, float $amount, string $type)
    {
        $wallet = Wallet::where('user_id', $user_id)->first();

        if ($wallet) {
            $opening_balance = $wallet->main_balance;
            $closing_balance = $type === 'credit' ? $opening_balance + $amount : $opening_balance - $amount;

            return $wallet->update([
                'main_balance' => $closing_balance,
            ]);
        }

        return false;
    }
}
