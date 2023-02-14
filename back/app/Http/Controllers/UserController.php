<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            $user= User::where('email', $request->email)->with('roles')->first();
            if ($user) {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $token =  $user->createToken('web')->plainTextToken;
                    return response()->json(['token' => $token,"user" => $user], 200);
                } else {
                    return response()->json(['message' => 'Contraseña incorrecta'], 401);
                }
            } else {
                return response()->json(['message' => 'Usuario no encontrado'], 401);
            }
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
    public function me(Request $request)
    {
        $user = User::where('id', $request->user()->id)->with('roles')->first();
        return $user;
    }
}
