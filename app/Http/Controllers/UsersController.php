<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $you = auth()->user();
        $users = User::all();
//        dd(session());
        return view('dashboard.admin.usersList', compact('users', 'you'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('dashboard.admin.userShow', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::orderBy('sequence')->get();
        return view('dashboard.admin.userCreateForm')->with([
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:5|max:64|unique:users',
            'full_name' => 'required|min:5|max:64',
            'email' => 'required|email|min:1|max:128|unique:users',
            'password' => 'required|min:5',
        ]);

        $roles = Role::findById($request->role_id);
//        dd($roles);
        $param = $request->input();
        $param['password'] = Hash::make($param['password']);
//        $param['menuroles'] = implode(',', array_filter($request->all($roles->map(function ($item) {return $item->name;})->toArray())));
        $param['menuroles'] = $roles->name;
        $param['created_by'] = auth()->id();
        User::create($param);

        return redirect('/users')->with(['message' => 'User berhasil diupdate']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::orderBy('sequence')->get();
        return view('dashboard.admin.userEditForm', compact(['user', 'roles']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|min:1|max:64',
//            'password' => 'min:5',
        ]);
        $role = Role::findById($request->role_id);
        $user = User::find($id);
        if ($request->has('password') && strlen($request->password) > 0)
            $user->password = Hash::make($request->input('password'));
        $user->role_id = $request->input('role_id');
//        $user->menuroles = implode(',', array_filter($request->all($roles->map(function ($item) {return $item->name;})->toArray())));
        $user->menuroles = $role->name . ($role->name != 'Frontdesk' ? ',Frontdesk' : '');
        $user->save();
        $user->syncRoles($role->name, 'Frontdesk');

        $request->session()->flash('message', 'Successfully updated user');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return redirect()->route('users.index');
    }
}
