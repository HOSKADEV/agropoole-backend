<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Resources\StateCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedStateCollection;

class StateController extends Controller
{
  public function get(Request $request){  //paginated
    $validator = Validator::make($request->all(), [
      'search' => 'sometimes|string',

    ]);

    if ($validator->fails()){
      return response()->json([
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try{

    $states = State::orderBy('id','ASC');




    if($request->has('search')){

      $states = $states->where('name', 'like', '%' . $request->search . '%');
    }

    if($request->has('all')){
      $states = $states->get();
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new StateCollection($states)
      ]);
    }

    $states = $states->paginate(10);

    return response()->json([
      'status' => 1,
      'message' => 'success',
      'data' => new PaginatedStateCollection($states)
    ]);

  }catch(Exception $e){
    return response()->json([
      'status' => 0,
      'message' => $e->getMessage()
    ]
  );
  }

  }
}
