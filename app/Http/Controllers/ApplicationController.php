<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Support\Facades\Crypt;
use App\Http\Resources\WidgetResource;
use App\Models\Widget;
use App\Models\User;
use Kouyatekarim\Momoapi\Products\Collection;

class ApplicationController extends Controller
{
    // public function tester(){
    //     return response()->json(['test'=>'ok']);
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return ApplicationResource::collection(Application::orderBy('nom')->get());
    }

    public function appuser($slug)

    {   $user = User::where('slug', $slug)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    
        $applications = $user->applications; // Supposons que la relation entre User et Application soit définie dans le modèle User
    
        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$slug)

    {    
        $user = User::where('slug',$slug)->first();
        
        try {
            dd($request->all());
            $app = Application::firstOrCreate([
                'nom' => $request->nom,
                'categories' => $request->categories,
                'produit' => $request->produit,
                'description' => $request->description,
                'logo' => $request->logo ? $request->logo->storeAs('images/App', $request->nom . '.' . $request->logo->extension(), 'public') : null,
                'url' => $request->url,
                'user_id'=>$user->id,

            ]);

            return response()->json([
                "message" => "Application créee avec succès",
                "data" => new ApplicationResource($app)
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $app = Application::where('slug', $slug)->first();

        // return response()->json(['data'=>'test ok transaction']);
        try {
            return response()->json([
                "message" => "Application visualisée avec succès",
                "data" => new ApplicationResource($app),
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $app = Application::where('slug', $slug)->first();

        try {
            $app->update([
                'nom' => $request->nom,
                'categorie' => $request->categorie,
                'produit' => $request->produit,
                'description' => $request->description,
                'logo' => $request->logo ? $request->logo->storeAs('images/App', $request->nom . '.' . $request->logo->extension(), 'public') : null,
                'url' => $request->url,

            ]);

            return response()->json([
                "message" => "Application créee avec succès",
                "data" => new ApplicationResource($app)
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $app = Application::where('slug', $slug)->first();
        $app->delete();
        return response()->json([
            "message" => "Application supprimée avec succes"
        ], 200);
    }


    public function tokengenerate(Request $request,$slug)
    {
        $app = Application::where('slug', $slug)->first();
        $sluguser = $app->user->slug;

        try {

            $app = Application::findOrFail($app->id);
            $widget = Widget::firstOrCreate([
                'nom' =>$request->nom,
                'url_redirection' =>$request->url_redirection,
                'application_id' => $app->id,
                'lien_payement' => Crypt::encryptString($app->slug.$sluguser),
                'status'=>"active",
                // 'token'=>$request->token,
                // $decrypted = Crypt::decryptString($app->token);

            ]);

            return response()->json([
                "message" => "token créee avec succès",
                "data" => new WidgetResource($widget)
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function showwidget(Request $request,$slug)
    {
        $app = Application::where('slug', $slug)->first();
        $sluguser = $app->user->slug;

        try {

            $app = Application::findOrFail($app->id);
            $widget = Widget::firstOrCreate([
                'nom' =>$request->nom,
                'url_redirection' =>$request->url_redirection,
                'application_id' => $app->id,
                'lien_payement' => Crypt::encryptString($app->slug.$sluguser),
                'status'=>"active",
                // 'token'=>$request->token,
                // $decrypted = Crypt::decryptString($app->token);

            ]);

            return response()->json([
                "message" => "token créee avec succès",
                "data" => new WidgetResource($widget)
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
        }
    }


    public function widgetlist()
    {
        return WidgetResource::collection(Widget::all());
    }


    public function veriftoken($generatelink)
    {   
        
        $decrypted_token = Crypt::decryptString($generatelink);
        $slugapp = substr($decrypted_token, 0, 6);
        $sluguser = substr($decrypted_token, 6, 6);
        $user = User::where('slug', $sluguser)->first();
        $app = Application::where('slug',$slugapp)->first();

        if ($user && $app) {
            try {
                return response()->json([
                    "message" => "Show page transaction",
                    "app"=>$app,
                ], 200);
            } catch (\Throwable $th) {
                dd($th);
                //throw $th;
            }
        } else {
            try {
                return response()->json([
                    "message" => "Erreur dans le slug",
                ], 200);
            } catch (\Throwable $th) {
                dd($th);
                //throw $th;
            }
        }
        
      
    }

    public function showwidgetuser($slug)
    {
        $user = User::where('slug', $slug)->first();
        $widgets = [];
        foreach ($user->applications as $application) {
            $serviceWidgets = $application->widgets;
            $widgets = array_merge($widgets, $serviceWidgets->toArray());
        }

        try {
            return response()->json([
                "message" => "Transactions retrieved successfully",
            "data" => $widgets,
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
        }
    }

    // public function depot(Request $request,$slug)
    // {
    //     $app = Application::where('slug', $slug)->first();
    //     $xReferenceId = "49f6b2d9-f7d0-4386-9397-641f3368d5a5"; // à générer sur le site [UUID](https://www.uuidgenerator.net/).
    //     $options = [
    //          'callbackHost' => 'clinic.com', 
    //         // 'callbackUrl' => '', 
    //         //'environment' => '', 
    //         // 'accountHolderIdType' => '', 
    //         'subscriptionKey' => '4ebd9f4c22fd4fc0b658b5ce7017843c', 
    //         'xReferenceId' =>  $xReferenceId, 
         
        
    //     ];
        
         
    //     // Avec collection
    //     $product = Collection::create($options);
        
    //     $product->createApiUser(); 
    //     $apiUser = $product->getApiUser();
    //     echo $apiUser->getProviderCallbackHost(); //clinic.com
    //     echo '</br>';
    //     echo $apiUser->getTargetEnvironment(); //sandbox
    //     echo '</br>';
         
    //     $apiKey = $product->createApiKey();
    //     echo $apiKey->getApiKey();echo"------";
        
        
    //     $options = [
    //         // 'callbackHost' => '', //(optional)
    //         // 'callbackUrl' => '', //(optional) 
    //         //'environment' => 'mtnivorycoast', 
    //         // 'accountHolderIdType' => '', 
    //         'subscriptionKey' => '4ebd9f4c22fd4fc0b658b5ce7017843c', 
    //         'xReferenceId' => $xReferenceId, //
    //         'apiKey' => $apiKey->getApiKey(), //  
           
    //     ];
        
    //     $product = Collection::create($options);
    //     $token = $product->getToken();
    //     echo $token->getAccessToken(); //accessToken
    //     $token->getTokenType(); //tokenType
    //     $token->getExpiresIn(); //expiry in seconds
    //     $options = [
    //         // 'callbackHost' => '', 
    //         // 'callbackUrl' => '', 
    //         //'environment' => '', 
    //         // 'accountHolderIdType' => '', 
    //         'subscriptionKey' => '4ebd9f4c22fd4fc0b658b5ce7017843c', 
    //         'xReferenceId' =>$xReferenceId, 
    //         'apiKey' => $apiKey->getApiKey(), 
    //         'accessToken' => $token 
    //     ];
        
        
    //     $product = Collection::create($options);
        
    //     $externalId ="12345";
    //     $partyId = '652795601'; //numero sans l'indicateur du pays
    //     $amount = $request->prix;
    //     $currency = "EUR";
    //     $product->requestToPay($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '');

    //     // Récupérer le statut de la transaction
    //    $transactionStatus = $product->getTransactionStatus($externalId);
    //    dd($transactionStatus);
    //    if ($transactionStatus ===200){
    //     $transaction = Transaction::firstOrCreate([
    //         'montant' => $request->prix,
    //         'trans_token' => Str::random(6),
    //         'application_id'=>$app->id,

    //     ]);
    //     return response()->json([
    //         "message" => "transaction reussi",
    //         "data" => new TransactionResource($transaction)
    //     ], 200);
    //    }else {
    //     return response()->json([
    //         "message" => "transaction echoué",
           
    //     ], 200);
    //    }
    

        
    // }
}
