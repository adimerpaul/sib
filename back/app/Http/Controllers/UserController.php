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
                'email' => 'required',
                'password' => 'required'
            ]);
            $user= User::where('email', $request->email)->with('roles')->first();
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $token =  $user->createToken('web')->plainTextToken;
                return response()->json(['token' => $token,"user" => $user], 200);
            } else {
                $user = User::where('rni', $request->email)->where('ci', $request->password)->with('roles')->first();
                if ($user) {
                    $token =  $user->createToken('web')->plainTextToken;
                    return response()->json(['token' => $token,"user" => $user], 200);
                }else {
                    return response()->json(['message' => 'Usuario o contraseña incorrectos'], 401);
                }
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
    public function index(){
        return User::with('roles')->where('id','!=',1)->get();
    }
    public function show($type,Request $request){
        $user = User::where('id', $type)->with('roles')->first();
        if ($user) {
            return $user;
        }
        else if ($type == 'usuario') {
            return User::with('roles')->where('id','!=',1)->where('type',$type)->get();
        }else {
            if (isset($request->filter)){
                return User::with('roles')->where('id','!=',1)->where('type',$type)->where('name','like','%'.$request->filter.'%')->paginate(20);
            }
            return User::with('roles')->where('id','!=',1)->where('type',$type)->paginate(20);
        }
    }
    public function store(Request $request){
        if ($request->name == '' || $request->name == null) {
            $request->merge(['name' => $request->paterno.' '.$request->materno.' '.$request->nombres]);
        }
        error_log(json_encode($request->all()));
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);
        $request->merge(['password' => bcrypt($request->password)]);
        $user=User::create($request->all());
        $user->assignRole($request->roles);
        return $user;
    }
    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);
        $user=User::find($id);
        $user->update($request->all());
        $user->syncRoles($request->roles);
        return $user;
    }
    public function userResetPassword(Request $request, $id){
        $this->validate($request, [
            'password' => 'required'
        ]);
        $user=User::find($id);
        $user->update(['password' => bcrypt($request->password)]);
        return $user;
    }
    public function destroy($id){return User::destroy($id);}
}
