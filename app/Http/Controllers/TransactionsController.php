<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use Http_Request2;
use GuzzleHttp\Client;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Application;
use App\Http\Resources\TransactionsResource;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * 
     */

     public function showtransactionuser($slug)
    {
        $user = User::where('slug', $slug)->first();
        $transactions = [];
        foreach ($user->applications as $application) {
            $serviceTransactions = $application->transactions;
            $transactions = array_merge($transactions, $serviceTransactions->toArray());
        }

        try {
            return response()->json([
                "message" => "Transactions retrieved successfully",
            "data" => $transactions,
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
        }
    }

    public function show($slug)
    {
        $app = Application::where('slug', $slug)->first();
        $transactions = $app->transactions;

        try {
            return response()->json([
                "message" => "Transactions retrieved successfully",
            "data" => $transactions,
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
        }
    }
     

    

     private $subscription = "4ebd9f4c22fd4fc0b658b5ce7017843c";


    public function index()
    {
        
        // return response()->json(['data'=>'test ok index']);
        return TransactionsResource::collection(Transaction::get());
    }

    /**
     * Store a newly created resource in storage.
     */
  

    /**
     * Remove the specified resource from storage.
     */
    public function responseDeposit(Request $request)
    {
        $status = $request->input('status');
        $slug = $request->input('slug');
        $app = Application::where('slug',$slug)->first();
        $user = User::findOrFail($app->user->id);
        
        try {
            $transaction = Transaction::firstOrCreate([
                'montant' => $request->input('montant'),
                'trans_token' => strtoupper(Str::random(10)),
                'tel' => $request->input('tel'),
                'status' => $status,
                'type_trans' => "depot",
                'application_id' => $app->id,
            ]);
        
            // Mettre à jour le solde de l'utilisateur uniquement si le statut est "succes" ou "failed"
            
             
                $user->solde += $request->input('montant');
                $user->save();
              
           
           
        
            // Déterminer le message en fonction du statut
            $message = ($status === "success") ? "transaction réussie" : "transaction échouée";
        
            return response()->json([
                "message" => $message,
                "data" => new TransactionsResource($transaction),
              
            ], 200);
        
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function responseWithDrawal(Request $request)
    {
        $status = $request->input('status');
        $slug = $request->input('slug');
        $type = $request->input('type');
        $app = Application::where('slug',$slug)->first();
        $user = User::findOrFail($app->user->id);
        $type_trans = ($type === "refund") ? "remboursement" : "retrait";
        try {
            $transaction = Transaction::firstOrCreate([
                'montant' => $request->input('montant'),
                'trans_token' => Str::random(10),
                'tel' => $request->input('tel'),
                'status' => $status,
                'type_trans' => $type->trans,
                'application_id' => $app->id,
            ]);

                if ($user->solde >= $request->input('montant')) {
                    $user->solde -= $request->input('montant');
                    $user->save();
                } else {
                    $message = "solde insuffisant";
                }
        
           
        
            // Déterminer le message en fonction du statut
            $message = ($status === "success") ? "transaction réussie" : "transaction échouée";
        
            return response()->json([
                "message" => $message,
                "insufficient_balance_message" => $message, 
                "data" => new TransactionsResource($transaction)
            ], 200);
        
        } catch (\Throwable $th) {
            dd($th);
        }
    }


    private function performCommonLogic($xReferenceId)
    {
        // apiuser
        $client = new Client();
        $response = $client->post('https://sandbox.momodeveloper.mtn.com/v1_0/apiuser', [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Reference-Id' => $xReferenceId->toString(),
                'Ocp-Apim-Subscription-Key' => $this->subscription,
            ],
            'json' => [
                'providerCallbackHost' => 'string'
            ]
        ]);

        // apikey
        $response = $client->post("https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/{$xReferenceId}/apikey", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->subscription,
            ],
            'json' => [
                'X-Reference-Id' => $xReferenceId->toString(),
            ]
        ]);
        $apiKey = json_decode($response->getBody())->apiKey;

        // access_token
        $token = base64_encode($xReferenceId . ':' . $apiKey);
        $response = $client->post('https://sandbox.momodeveloper.mtn.com/collection/token/', [
            'headers' => [
                'Authorization' => 'Basic ' . $token,
                'Ocp-Apim-Subscription-Key' => $this->subscription,
            ]
        ]);
        $access_token = json_decode($response->getBody())->access_token;

        return $access_token;
    }


    public function requesttopay(Request $request1,$slug)
    {
        $xReferenceId = Str::uuid();

        // Perform common logic
        $access_token = $this->performCommonLogic($xReferenceId);

        // requesttopay
        $client = new Client();
        $response = $client->post('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Ocp-Apim-Subscription-Key' => $this->subscription,
                'X-Target-Environment' => 'sandbox',
                'Content-Type' => 'application/json',
                'X-Reference-Id' => $xReferenceId->toString(),
            ],
            'json' => [
                'amount' => $request1->montant,
                'currency' => 'EUR',
                'externalId' => $xReferenceId->toString(),
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $request1->tel,
                ],
                'payerMessage' => 'string',
                'payeeNote' => 'string'
            ]
        ]);

        $responseBody = json_decode($response->getBody());

        if ($response->getStatusCode() == 202) {

            return redirect()->action(
                [TransactionsController::class, 'responseDeposit'], ['status' => 'success',
                'slug' => $slug,
                'tel' =>$request1->tel,
                'montant' =>$request1->montant]
            );
        } else {
            return redirect()->action(
                [TransactionsController::class, 'responseDeposit'], ['status' => 'failed',
                'slug' => $slug,
                'tel' =>$request1->tel,
                'montant' =>$request1->montant]
            );
        }
    }

    public function requesttowithdrawal(Request $request1,$slug)
    {
        $xReferenceId = Str::uuid();

        // Perform common logic
        $access_token = $this->performCommonLogic($xReferenceId);

        // requesttowithdraw
        $client = new Client();
        $response = $client->post('https://sandbox.momodeveloper.mtn.com/collection/v2_0/requesttowithdraw', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Ocp-Apim-Subscription-Key' => $this->subscription,
                'X-Target-Environment' => 'sandbox',
                'Content-Type' => 'application/json',
                'X-Reference-Id' => $xReferenceId->toString(),
            ],
            'json' => [
                "payeeNote" => "string",
                "externalId" => $xReferenceId->toString(),
                "amount" => $request1->montant,
                "currency" => "EUR",
                "payer" => [
                    "partyIdType" => "MSISDN",
                    "partyId" => $request1->tel,
                ],
                "payerMessage" => "string"
            ]
        ]);

        $responseBody = json_decode($response->getBody());
        if ($response->getStatusCode() == 202) {

            return redirect()->action(
                [TransactionsController::class, 'responseWithDrawal'], ['status' => 'success',
                'slug' => $slug,
                'tel' =>$request1->tel,
                'montant' =>$request1->montant,
                'type' =>$request1->type?$request->type:null]
            );
        } else {
            return redirect()->action(
                [TransactionsController::class, 'responseWithDrawal'], ['status' => 'failed',
                'slug' => $slug,
                'tel' =>$request1->tel,
                'montant' =>$request1->montant,
                'type' =>$request1->type?$request->type:null]
            );
        }
    }

    


}
