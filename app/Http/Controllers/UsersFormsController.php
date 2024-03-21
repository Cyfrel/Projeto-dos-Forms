<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersFormsController extends Controller
{
    public function create()
    {   
        dd("create");
        //return view('forms.create');
    }

    public function show(Request $request)
    {   
        try {
            $id = $request->query('id');
            $user = Users::findOrFail($id);
        
            return response()->json($user);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }
    }
}
