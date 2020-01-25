<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class UserController extends Controller
{
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * UserController index.
     * @param Request $request
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->getBy($request->all());
        return [
            'users' => array_values( $users )
        ];
    }
}
