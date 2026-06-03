<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = DB::table('users')->where('email','=' , $email)->where('password','=', $password)->get();
    //print_r(count($user));
        if (count($user)) {

          //  $request->session()->put('user', $request->email);
           // $request->session()->put('priv', $user[0]->privilege);
    //dd($request->session()->get('user'));
            return view('welcome');
            //->with(['user' => $request->session()->get('user')])->with('priv', $request->session()->get('priv'))->with('success', 'Login successful. Welcome, ' . $user[0]->name . ' !');
        } else {
            return redirect()->back()->with(['success' => 'Invalid email or password']);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
