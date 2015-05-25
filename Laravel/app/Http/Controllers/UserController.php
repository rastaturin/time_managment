<?php namespace App\Http\Controllers;

use App\Exceptions\AccessDenied;
use App\Exceptions\BadRequest;
use App\Http\MyInput;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        if (User::$logged->isManager()) {
            return $this->response(User::all());
        } else {
            throw new AccessDenied('Only manager can access other users');
        }
	}

    /**
     * Store a newly created resource in storage.
     * @return Response
     * @throws BadRequest
     */
	public function store()
	{
        $data = MyInput::all();
        if (empty($data['email'])) {
            throw new BadRequest('Empty email');
        }
        if (empty($data['password'])) {
            throw new BadRequest('Empty password');
        }
        if (isset($data['role'])) {
            unset($data['role']);
        }

        $user = new User($data);
        if (User::byEmail($user->email)) {
            throw new BadRequest(sprintf('Email %s already used', $user->email));
        }
        $user->save();
        return $this->response($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $userId
     * @return Response
     * @throws AccessDenied
     */
	public function show($userId)
	{
        $user = $this->getAvailableUser($userId);
        return response()->json($user);
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     * @throws AccessDenied
     * @throws BadRequest
     */
	public function update($id)
	{
        $user = $this->getAvailableUser($id);
        $data = MyInput::all();

        if (isset($data['role'])) {
            // Only manager (and not herself) can change roles.
            if (!User::$logged->isManager() || $user->id == User::$logged->id) {
                unset($data['role']);
            }
            // Only admin can set to admins
            elseif (!User::$logged->isAdmin() && $data['role'] == User::ADMIN) {
                throw new AccessDenied('Only admin can set other users as admins');
            }
        }

        $user->update($data);
        return $this->response($user);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $userId
	 * @return Response
	 */
	public function destroy($userId)
	{
        $user = $this->getAvailableUser($userId);
        if (User::$logged->id == $user->id) {
            throw new AccessDenied("You can't delete yourself");
        }
        $user->delete();
	}

    public function login()
    {
        $data = MyInput::all();
        if (Auth::attempt($data)) {
            $user = Auth::user();
            $user->api_token = md5(uniqid());
            $user->save();
            $user->hours;
            $user->timerows;
            return $this->response(['token' => $user->api_token, 'user' => $user]);
        } else {
            throw new AccessDenied('Incorrect email or password');
        }
    }


    /**
     * Check access for managing this user
     * @param $userId
     * @return User
     * @throws AccessDenied
     */
    protected function getAvailableUser($userId)
    {
        $user = User::find($userId);
        if ($user && ($user->id == User::$logged->id || User::$logged->isManager())) {
            return $user;
        } else {
            throw new AccessDenied('Only manager can access other users');
        }
    }
}
