<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();
        return view('user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::all();
        return view('user.create');


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|min:3',
            'email' => 'required|email',
            'role' => 'required',
        ]);


        $defaultPassword = Str::substr($request->email, 0, 3) . Str::substr($request->nama, 0, 3);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($defaultPassword),
        ]);

        return redirect()->back()->with('success', 'Berhasil Menambahkan Akun!');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('user.edit', compact('user'));
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->back()->with('deleted', 'Berhasil Menghapus Data');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|min:3',
            'email' => 'required|email',
            'role' => 'required',
        ]);
        $user = User::findOrFail($id);

        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('user.akun')->with('success', 'Berhasil Memperbarui Akun!');
    }
    public function loginAuth(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email:dns',
                'password' => 'required|alpha_dash',
            ],
            [
                'email.required' => 'Email harus diisi',
                'email.email' => 'Email tidak valid',
                'password.required' => 'Password harus diisi',
                'password.alpha_dash' => 'Password harus berisi huruf dan karakter tanpa spasi'
            ]
        );

        $user = $request->only(['email', 'password']);
        if (Auth::attempt($user)) {
            return redirect()->route('home.page');
        } else {
            return redirect()->back()->with('failed', 'Proses login gagal, silahkan coba kembali dengan data yang benar!');
        }
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('login')->with('logout', 'Anda telah logout!');
    }
}
