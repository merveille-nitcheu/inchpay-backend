<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\GoogleMessage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a userlist.
     */
    public function index()
    {
        return UserResource::collection(User::orderBy('nom')->get());
    }

    /**
     * Create an user account
     */
    public function store(UserRequest $request)
    {


        try {
            $user = User::firstOrCreate([
                'nom' => $request->nom,
                'email' => $request->email,
                'username' => $request->username,
                'tel' => $request->tel,
                'password' => Hash::make($request->password),

            ]);
            $codeverif = Str::random(10);
            Mail::to($request->email)
            ->send(new GoogleMessage($request->nom,$codeverif));
        // ->queue(new GoogleMessage($request->all()));

            return response()->json([
                "message" => "Utilisateur enregistré avec succès",
                "Email" => "Email envyé avec succes",
                "data" => new UserResource($user),
                "verifcode" => $codeverif
            ], 200);

        } catch (\Throwable $th) {
            dd($th);
        }
    }


    /**
     * Create an Admin account
     */
    public function storeAdmin(UserRequest $request)
    {
        try {
            $user = User::firstOrCreate([
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'Isadmin' => true,

            ]);
           
            return response()->json([
                "message" => "Utilisateur enregistré avec succès",
                "data" => new UserResource($user)
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $user = User::where('slug', $slug)->first();

        try {
            return response()->json([
                "message" => "utilisateur visualisé avec succès",
                "data" => new UserResource($user),
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
             
        $user = User::where('slug', $slug)->first();
        try {
            $user->update([
                'nom' => $request->nom,
                'email' => $request->email,
                'username' => $request->username,
                'tel' => $request->tel,
                'photo' => $request->photo ? $request->file('photo')->storeAs('images/profil', $user->id . '.' . $request->photo->extension(), 'public') : null,
                'password' => $request->password ? Hash::make($request->password) : $user->password,

            ]);
            return response()->json([
                "message" => "Informations modifiées",
                "data" => $user
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $user = User::where('slug', $slug)->first();
        $user->delete();
        return response()->json([
            "message" => "Utilisateur supprimé"
        ], 200);
    }

    public function login(AuthRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            // Générer un jeton d'API pour l'utilisateur authentifié
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24);

            // Retourner le cookie à l'utilisateur
            return response([
                'message' => 'l\'utilisateur est connecté',
                'token' => $token,
                'user' =>$user
            ])->withCookie($cookie);
        }

        // Retourner une erreur si les informations d'identification sont incorrectes
        return response()->json(['message' => 'mauvais identifiants'], 401);
    }

    //deconnecter un utilisateur
    public function logout()
    {
        $cookie = Cookie::forget('jwt');
        // $request->user()->token()->delete();
        return response([
            'message' => 'utilisateur déconnecté'
        ])->withCookie($cookie);
    }
}
