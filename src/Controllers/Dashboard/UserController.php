<?php

namespace ZapsterStudios\Ally\Controllers\Dashboard;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(User::paginate(30));
    }

    /**
     * Display a single user.
     *
     * @param  integer  $id
     * @return \Response
     */
    public function show($id)
    {
        return response()->json(User::with('teams')->findOrFail($id));
    }

    /**
     * Search for a list of users.
     *
     * @param  Request  $request
     * @return Response
     */
    public function search(Request $request)
    {
        $search = $request->search;

        return response()->json(User::where('id', $search)
            ->orWhere('country', $search)
            ->orWhere('name', 'LIKE', '%'.$search.'%')
            ->orWhere('email', 'LIKE', '%'.$search.'%')
            ->paginate(30)
        );
    }
}
