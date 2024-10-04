<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Ad;
use App\Models\AdType;
use Illuminate\Http\Request;
use App\Http\Resources\AdResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AdCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaginatedAdCollection;

class AdController extends Controller
{

  public function index()
  {
    return view('content.ads.list');
  }

  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string',
      'image' => 'sometimes|mimetypes:image/*',
      'url' => 'required|string',
      'types' => 'sometimes|array'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 0,
        'message' => $validator->errors()->first()
      ]);
    }
    try {

      DB::beginTransaction();

      $ad = Ad::create($request->except('image'));

      if ($request->has('types')) {

        $types = $request->types;

        array_walk($types, function (&$item, $key) use ($ad) {
          $item = [
            'ad_id' => $ad->id,
            'type' => $item
          ];
        });

        AdType::insert($types);

      }

      if ($request->hasFile('image')) {
        //$path = $request->image->store('/uploads/ads/images','upload');

        $file = $request->image;
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $filename = 'ads/' . $ad->id . '/' . md5(time() . $name) . '.' . $extension;

        $url = $this->firestore($file->get(), $filename);

        $ad->image = $url;
        $ad->save();
      }

      DB::commit();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new AdResource($ad)
      ]);

    } catch (Exception $e) {
      DB::rollback();
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }
  }

  public function update(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'ad_id' => 'required',
      'name' => 'sometimes|string',
      'image' => 'sometimes|mimetypes:image/*',
      'url' => 'sometimes|string',
      'types' => 'sometimes|array'
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      DB::beginTransaction();

      $ad = Ad::findOrFail($request->ad_id);

      $ad->update($request->only('name', 'ad_id', 'types'));

      if ($request->has('types')) {
        $ad->types()->delete();

        $types = $request->types;

        array_walk($types, function (&$item, $key) use ($ad) {
          $item = [
            'ad_id' => $ad->id,
            'type' => $item
          ];
        });

        AdType::insert($types);

      }

      if ($request->hasFile('image')) {
        //$path = $request->image->store('/uploads/ads/images','upload');

        $file = $request->image;
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $filename = 'ads/' . $ad->id . '/' . md5(time() . $name) . '.' . $extension;

        $url = $this->firestore($file->get(), $filename);

        $ad->image = $url;
        $ad->save();
      }

      DB::commit();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new AdResource($ad)
      ]);

    } catch (Exception $e) {
      DB::rollback();
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }

  public function delete(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'ad_id' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      $ad = Ad::findOrFail($request->ad_id);

      $ad->delete();

      return response()->json([
        'status' => 1,
        'message' => 'success',
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }

  public function restore(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'ad_id' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      $ad = Ad::withTrashed()->findOrFail($request->ad_id);

      $ad->restore();

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new AdResource($ad)
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }

  public function get(Request $request)
  {  //paginated
    $validator = Validator::make($request->all(), [
      'search' => 'sometimes|string',
    ]);

    if ($validator->fails()) {
      return response()->json(
        [
          'status' => 0,
          'message' => $validator->errors()->first()
        ]
      );
    }

    try {

      $user = $this->get_user_from_token($request->bearerToken());


      $ads = Ad::join('ad_types', function ($join) use ($user) {
        $join->on('ads.id', '=', 'ad_types.ad_id');
        $join->where('ad_types.type', '=', $user?->role ?? 4);
      })
        ->select('ads.*')
        ->orderBy('ads.created_at', 'DESC');

      if ($request->has('search')) {

        $ads = $ads->where('ads.name', 'like', '%' . $request->search . '%');
      }

      $ads = $ads->paginate(5);

      //return($ads);

      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new PaginatedAdCollection($ads)
      ]);

    } catch (Exception $e) {
      return response()->json(
        [
          'status' => 0,
          'message' => $e->getMessage()
        ]
      );
    }

  }
}
