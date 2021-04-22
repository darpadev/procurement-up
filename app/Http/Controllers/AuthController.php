<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
    
    public function postLogin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect(route('home'));
        }

        return redirect(route('login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect(route('login'));
    }

    public function register()
    {
        $units = \App\Models\Unit::select('id', 'name')->orderBy('name', 'ASC')->get();
        return view('auth.register', [
            'units' => $units,
        ]);
    }

    public function storeAccount(Request $request)
    {
        $this->validate($request, [
            'password' => 'min:8|required_with:password_confirmation|same:password_confirmation'
        ]);
        
        $name = $request->name;
        $email = $request->email;
        $unit = $request->unit;
        $password = bcrypt($request->password);
        
        $user = new User;

        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->unit = $unit;
        $user->role = 1;

        $user->save();

        return redirect(route('login'));
    }
}
