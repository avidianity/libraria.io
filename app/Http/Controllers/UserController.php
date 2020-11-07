<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            'role:Admin'
        ])->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::with('authors.books.category')
            ->with('authors.books.photo')
            ->with('roles')
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $this->rules();
        $roles['email'][] = Rule::unique('users', 'email');
        $data = $request->validate($rules);

        $data['password'] = Hash::make($data['password']);

        $user = new User($data);
        $user->assignRole($data['role']);
        $user->save();
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::with('authors.books.category')
            ->with('authors.books.photo')
            ->with('roles')
            ->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = $this->rules('nullable');
        $roles['email'][] = Rule::unique('users', 'email')
            ->ignoreModel($user);
        $data = $request->validate($rules);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        $user->fill($data);
        $user->save();
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return new Response([
                'message' => 'You cannot delete your own account.',
                'errors' => [],
            ], 403);
        }
        $user->delete();
        return new Response('', 204);
    }

    protected function rules($mode = 'required')
    {
        return [
            'name' => [$mode, 'string', 'max:255'],
            'email' => [
                $mode, 'email',
                'max:255',
            ],
            'password' => [$mode, 'string', 'min:6', 'max:255'],
            'role' => [$mode, Rule::in([User::ADMIN, User::NORMAL])]
        ];
    }
}
