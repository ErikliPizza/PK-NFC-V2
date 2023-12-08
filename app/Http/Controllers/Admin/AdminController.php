<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        return view('admin/pages/index');
    }

    public function decrypt (Request $request)
    {
        if (strlen($request->identity) === 4) {
            $params = ['slug' => $request->identity];
            $sig = hash_hmac('sha256', http_build_query($params), env('SIGNATURE_KEY'));
            $result = url('/nfc/' . $request->identity . '?signature=' . $sig);
            return redirect()->route('admin.dashboard')->with('key', $result);
        } else {
            return redirect()->route('admin.dashboard')->with('info', 'Enter a valid 4 character identity');
        }

    }
}
