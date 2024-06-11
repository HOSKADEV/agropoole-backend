<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Resources\CityCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedCityCollection;

class CityController extends Controller
{
  public function get(Request $request){  //paginated
    $validator = Validator::make($request->all(), [
      'state_id' => 'sometimes|exists:states,id',
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

    $cities = City::orderBy('id','ASC');


    if($request->has('state_id')){

      $cities = $cities->where('state_id',$request->state_id);
    }

    if($request->has('search')){

      $cities = $cities->where('name', 'like', '%' . $request->search . '%');
    }

    if($request->has('all')){
      $cities = $cities->get();
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new CityCollection($cities)
      ]);
    }

    $cities = $cities->paginate(10);

    return response()->json([
      'status' => 1,
      'message' => 'success',
      'data' => new PaginatedCityCollection($cities)
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
