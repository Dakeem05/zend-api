<?php

namespace App\Services\Api\V1;

use App\Mail\RecieveCrypto;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class TransactionService 
{
    public function index (string $address) 
    {
        $user = User::where('address', $address)->first();

        if ($user == null) {
            return (object) [
                'success' => false,
                'message' => 'User does not exist'
            ];
        }
        
        $transactions = Transaction::where('user_id', $user->id)->paginate();

        return (object) [
            'success' => true,
            'message' => $transactions
        ];
    }

    public function create (object $data, string $address)
    {
        $user = User::where('address', $address)->first();

        if ($user == null) {
            return (object) [
                'success' => false,
                'message' => 'User does not exist'
            ];
        }

        $code = $this->generateCode();

        if (! $code) {
            return (object) [
                'success' => false,
                'message' => 'Something went wrong while generating code'
            ];
        }

        $link = env('APP_FRONTEND_LINK').'/'.$code;

        if ($data->method == "email") {
            Mail::to($data->recipient)->send(new RecieveCrypto($user->address, $data->amount, $link, $data->token));
        } else {
            $message = "You have received $data->amount $data->token from address $user->address.\n";
            $message .= "Please click the link below to claim your crypto:\n";
            $message .= $link;
            try {
  
                $account_sid = getenv("TWILIO_SID");
                $auth_token = getenv("TWILIO_TOKEN");
                $twilio_number = getenv("TWILIO_FROM");
      
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($data->recipient, [
                    'from' => $twilio_number, 
                    'body' => $message]);
         
            } catch (Exception $e) {
                return (object) [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $data->amount,
            'method' => $data->method,
            'recipient' => $data->recipient,
            'link' => $data->link,
            'code' => $code,
            'token' => $data->token
        ]);

        return (object) [
            'success' => true,
            'message' => $transaction
        ];
    }

    public function show (string $address, string $code) 
    {
        $user = User::where('address', $address)->first();

        if ($user == null) {
            return (object) [
                'success' => false,
                'message' => 'User does not exist'
            ];
        }
        
        $transaction = Transaction::where('user_id', $user->id)->where('code', $code)->first();
        
        if ($transaction == null) {
            return (object) [
                'success' => false,
                'message' => 'Transaction does not exist'
            ];
        }
        
        return (object) [
            'success' => true,
            'message' => $transaction
        ];
    }

    public function request (string $code) 
    {        
        $transaction = Transaction::where('code', $code)->first();
        
        if ($transaction == null) {
            return (object) [
                'success' => false,
                'message' => 'Transaction does not exist'
            ];
        }
        
        return (object) [
            'success' => true,
            'message' => $transaction
        ];
    }

    public function action (string $code) 
    {        
        $transaction = Transaction::where('code', $code)->first();
        
        if ($transaction == null) {
            return (object) [
                'success' => false,
                'message' => 'Transaction does not exist'
            ];
        }
        
        if ($transaction->status == 'claimed') {
            return (object) [
                'success' => false,
                'message' => 'Transaction has been claimed'
            ];
        }

        $transaction->status = 'claimed';
        $transaction->save();

        return (object) [
            'success' => true,
            'message' => "Transaction claimed succesfully"
        ];
    }

    private function generateCode($string_length = 20, $recursion_limit = 10)
    {
        if ($recursion_limit <= 0) {
            // We don't expect this to generate a code 10 times and all the codes are taken or for something to go wrong.
            // If such happens which is rare but not impossible, break out of the recursive loop.
            return null;
        }

        $randomString = Str::random($string_length);
        $code = 'zend-'.Str::lower($randomString);

        if (! $this->checkIfCodeExists($code)) {
            return $code;
        } else {
            return $this->generateCode($string_length, $recursion_limit - 1);
        }
    }

    private function checkIfCodeExists(string $code)
    {
        return Transaction::where('code', $code)->exists();
    }  
}