<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TransactionCreationRequest;
use App\Services\Api\V1\TransactionService;
use App\Traits\Api\V1\ApiResponseTrait;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private TransactionService $transaction_service)
    {
    }

    public function index (string $address) 
    {
        $request = $this->transaction_service->index($address);

        if ($request->success == false) {
            return $this->errorResponse($request->message);
        } else {
            return $this->successResponse([
                "transactions" => $request->message
            ], "Transactions retrieved successfully");
        }
    }
    
    public function create (string $address, TransactionCreationRequest $request) 
    {
        $request = $this->transaction_service->create((Object) $request->validated(), $address);
        
        if ($request->success == false) {
            return $this->errorResponse($request->message);
        } else {
            return $this->successResponse([
                "transaction" => $request->message
            ], "Transaction created");
        }
    }
    
    public function show (string $address, string $uuid) 
    {
        $request = $this->transaction_service->show($address, $uuid);

        if ($request->success == false) {
            return $this->errorResponse($request->message);
        } else {
            return $this->successResponse([
                "transactions" => $request->message
            ], "Transaction retrieved successfully");
        }
    }

    public function request (string $link) 
    {
        $request = $this->transaction_service->request($link);

        if ($request->success == false) {
            return $this->errorResponse($request->message);
        } else {
            return $this->successResponse([
                "transactions" => $request->message
            ], "Transaction retrieved successfully");
        }
    }

    public function action (string $uuid) 
    {
        $request = $this->transaction_service->action($uuid);

        if ($request->success == false) {
            return $this->errorResponse($request->message);
        } else {
            return $this->successResponse($request->message);
        }
    }
}
