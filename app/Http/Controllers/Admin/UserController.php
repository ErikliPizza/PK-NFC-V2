<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $users = User::where('email', 'LIKE', "%$search%")->paginate(10);
        } else {
            $users = User::paginate(10);
        }

        return view('admin/pages/users/index', compact('users', 'search'));
    }

    public function show(User $user)
    {
        return view('admin.pages.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', $user->email . ' deleted successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', 'Password updated successfully.');
    }

    public function togglePremium(User $user)
    {
        $user->premium = !$user->premium;
        $user->save();

        return redirect()->back()->with('info', 'Premium status updated.');
    }
}
