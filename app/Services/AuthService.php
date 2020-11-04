<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService extends Service
{
    /**
     * Keys or fields that should be present on
     * the data.
     * @var array
     */
    protected $keys = ['email', 'password'];

    public function __construct($data = null)
    {
        parent::__construct($data);
        $this->model = new User();
        $this->model = $this->model->with('roles');
    }

    public function authenticate()
    {
        $user = $this->model->where('email', $this->email)
            ->first();

        if (!$user) {
            $this->statusCode = 400;
            $this->message = 'Email does not exist.';
            return false;
        }
        if (!Hash::check($this->password, $user->password)) {
            $this->statusCode = 401;
            $this->message = 'Password is incorrect.';
            return false;
        }
        $this->model = $user;
        return true;
    }

    /**
     * Authenticate a user according to role.
     * @param string $role
     * @return static
     */
    public function withRole($role)
    {
        $this->model = $this->model->role($role);
        return $this;
    }
}
