<?php

namespace App\Http\Controllers;


use Session;
use App\Models\Ad;
use App\Models\Cart;
use App\Models\User;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Driver;
use App\Models\Family;
use App\Models\Notice;
use App\Models\Product;
use App\Models\Section;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class DatatablesController extends Controller
{
  public function categories(){

    $categories = Category::orderBy('created_at','DESC')->get();

    return datatables()
      ->of($categories)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          return $btn;
      })

      ->addColumn('subcategories', function ($row) {

          return number_format($row->subcategories()->count());

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function subcategories(Request $request){

    $subcategories = Subcategory::orderBy('created_at','DESC');

    if(!empty($request->category)){
      $subcategories->where('category_id',$request->category);
    }

    $subcategories = $subcategories->get();

    return datatables()
      ->of($subcategories)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          return $btn;
      })

      ->addColumn('category', function ($row) {

        return $row->category->name;

    })

      ->addColumn('products', function ($row) {

          return $row->products()->count();

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }


  public function families(){

    $families = Family::orderBy('created_at','DESC')->get();

    return datatables()
      ->of($families)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          if(is_null($row->section())){

            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline add_to_home" title="'.__('Add to Homepage').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bxs-plus-square me-2"></i></a>';

          }else{

            $btn .= '<a class="dropdown-item-inline remove_from_home" title="'.__('Remove from Homepage').'" table_id="'.$row->section()->id.'" href="javascript:void(0);"><i class="bx bxs-x-square me-2"></i></a>';

          }

          return $btn;
      })

      ->addColumn('categories', function ($row) {

          return $row->categories()->count();

      })

      ->addColumn('is_published', function ($row) {

        if(is_null($row->section())){
         return false ;
        }
        return true;

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function offers(){

    $offers = Offer::orderBy('created_at','DESC')->get();

    return datatables()
      ->of($offers)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          if(is_null($row->section())){

            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline add_to_home" title="'.__('Add to Homepage').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bxs-plus-square me-2"></i></a>';

          }else{

            $btn .= '<a class="dropdown-item-inline remove_from_home" title="'.__('Remove from Homepage').'" table_id="'.$row->section()->id.'" href="javascript:void(0);"><i class="bx bxs-x-square me-2"></i></a>';

          }

          return $btn;
      })

      ->addColumn('categories', function ($row) {

          return $row->categories()->count();

      })

      ->addColumn('is_published', function ($row) {

        if(is_null($row->section())){
         return false ;
        }
        return true;

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function products(Request $request){

    $user = $request->user();

    if($user->role_is('provider')){
      $request->merge(['provider' => $user->id]);
    }


    $products = Product::orderBy('created_at','DESC');

    if($request->provider){
      $products = $products->where('user_id', $request->provider);
    }

    if($request->category){

      $products = $products->whereHas('subcategory', function($query) use ($request){
        $query->where('category_id', $request->category);
      });
    }


    if($request->subcategory){
      $products = $products->where('subcategory_id',$request->subcategory);
    }

    if($request->availability){

      $products = $products->where('status',$request->availability == 1?'available':'unavailable');


    }

    if($request->stock){

      /* $products = $request->stock == 1
      ? $products->has('stocks', '>', '0')
      : $products->has('stocks', '=', '0'); */

      $products = $request->stock == 1
      ? $products->whereIn('id', $user->stocks()->pluck('product_id')->toArray())
      : $products->whereNotIn('id', $user->stocks()->pluck('product_id')->toArray());
    }

    $products = $products->get();

    return datatables()
      ->of($products)
      ->addIndexColumn()

      ->addColumn('action', function ($row) use ($user) {
          $btn = '';

          if($user->role_is('provider')){
            $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';
          }

          $btn .= '<a class="dropdown-item-inline add_stock" title="'.__('Add stock').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bxs-package me-2"></i></a>';


          return $btn;
      })

      ->addColumn('name', function ($row) {

          return $row->unit_name;

      })

      ->addColumn('image', function ($row) {

        return $row->image();

    })

    /*   ->addColumn('name_image', function ($row) {

        return [
          0 => $row->image(),
          1 => $row->unit_name
        ];

    }) */


      ->addColumn('in_stock', function ($row) use ($user) {

        if($row->stocks()->where('user_id', $user->id)->count()){
         return true ;
        }
        return false;

      })

      ->addColumn('availability', function ($row) {

        if($row->status == 'unavailable'){
         return false ;
        }
        return true;

      })


      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function stocks(Request $request){

    $user = $request->user();

    $stocks = Stock::where('user_id', $user->id);

    if($request->category){

      $stocks = $stocks->whereHas('product', function($query) use ($request){
        $query->whereHas('subcategory', function($query) use ($request){
          $query->where('category_id', $request->category);
        });
      });
    }


    if($request->subcategory){
      $stocks = $stocks->whereHas('product', function($query) use ($request){
        $query->where('subcategory_id',$request->subcategory);
      });
    }

    if($request->availability){

      $stocks = $stocks->where('status',$request->availability == 1?'available':'unavailable');

    }

    if($request->sufficiency){

      $stocks = $stocks->whereColumn('quantity', $request->sufficiency == 1 ?'>' : '<=', 'min_quantity');

    }

    $stocks = $stocks->orderBy('created_at','DESC')->with('product')->get();

    return datatables()
      ->of($stocks)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          return $btn;
      })

      ->addColumn('name', function ($row) {

          return $row->product->unit_name;

      })

      ->addColumn('image', function ($row) {

        return $row->product->image();

    })

      /* ->addColumn('name_image', function ($row) {

        return [
          0 => $row->product->image(),
          1 => $row->product->unit_name
        ];

    }) */

      ->addColumn('price', function ($row) {

        return number_format($row->price,2,'.',',');

    })

      ->addColumn('quantity', function ($row) {

        return [
          0 => $row->quantity,
          1 => $row->min_quantity
        ];

      })

      ->addColumn('availability', function ($row) {

        if($row->status == 'unavailable'){
         return false ;
        }
        return true;

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function sections(Request $request){

    $sections = Section::orderBy('rank','ASC')->get();

    return datatables()
      ->of($sections)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          if($row->deleteable == 1){
            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Remove').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-x me-2"></i></a>';
          }

          if($row->moveable == 1){
            $btn .= '<a class="dropdown-item-inline switch" title="'.__('Switch').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-refresh me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline insert" title="'.__('Insert').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-redo me-2"></i></a>';

          }

          return $btn;
      })

      ->addColumn('type', function ($row) {

        return $row->type;

    })

      ->addColumn('name', function ($row) {

          return $row->name();

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function orders(Request $request){

$user = auth()->user();

    $orders = match (intval($request->type)) {
      1 => Order::where('buyer_id', $user->id),
      2 => Order::where('seller_id', $user->id),
      3 => Order::whereHas('deliveries', function ($q) use ($user) {
        $q->where('driver_id', $user->id);
        $q->where('deleted_at', null);
      })
    };

    if($request->status){
        $orders = $orders->where('status',$request->status);
    }

    $orders = $orders->orderBy('created_at','DESC')->get();

    return datatables()
      ->of($orders)
      ->addIndexColumn()

      ->addColumn('action', function ($row) use ($request) {
          $btn = '';

          //$btn .= '<a class="dropdown-item-inline note" title="'.__('Note').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-note me-2"></i></a>';

          //$btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          $btn .= '<a class="dropdown-item info" title="'.__('Info').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-group me-2"></i>'.__('Info').'</a>';

          $btn .= '<a class="dropdown-item history" title="'.__('History').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-info-circle me-2"></i>'.__('History').'</a>';

          $btn .= '<a class="dropdown-item" title="'.__('Cart').'" href="'.url('order/'.$row->id.'/items').'"><i class="bx bx-cart me-2"></i>'.__('Cart').'</a>';

          if($row->status == 'pending' && $request->type == 2){

            $btn .= '<a class="dropdown-item accept" title="'.__('Approve').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-check me-2"></i>'.__('Accept').'</a>';

            $btn .= '<a class="dropdown-item refuse" title="'.__('Cancel').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-x me-2"></i>'.__('Cancel').'</a>';

          }

          if($row->status == 'accepted' && $request->type == 1){

            $btn .= '<a class="dropdown-item confirm" title="'.__('Confirm').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-check-double me-2"></i>'.__('Confirm').'</a>';

            $btn .= '<a class="dropdown-item refuse" title="'.__('Cancel').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-x me-2"></i>'.__('Cancel').'</a>';

          }

          if($row->status == 'confirmed' && $request->type == 2){


            $btn .= '<a class="dropdown-item ship" title="'.__('Ship').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-user-plus me-2"></i>'.__('Ship').'</a>';

          }

          if($row->status == 'shipped' && $request->type == 3){


            $btn .= '<a class="dropdown-item ongoing" title="'.__('Ongoing').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-transfer me-2"></i>'.__('Ongoing').'</a>';

          }

          if($row->status == 'ongoing' && $request->type == 3){


            $btn .= '<a class="dropdown-item deliver" title="'.__('Deliver').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-import me-2"></i>'.__('Deliver').'</a>';

          }

          if($row->status == 'delivered' && $request->type == 1){


            $btn .= '<a class="dropdown-item receive" title="'.__('Receive').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-user-check me-2"></i>'.__('Receive').'</a>';

          }






          /* if(!in_array($row->status,['pending','canceled'])){
            if(!is_null($row->invoice)){

                $btn .= '<a class="dropdown-item-inline invoice" title="'.__('Invoice').'" table_id="'.$row->invoice->id.'" href="javascript:void(0);"><i class="bx bx-file me-2"></i></a>';

              if($row->status == 'ongoing' && $row->invoice->is_paid == 'no'){

                $btn .= '<a class="dropdown-item-inline payment" title="'.__('Payment').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-money me-2"></i></a>';

              }

            }
          } */

          //$btn .= '<a class="dropdown-item-inline" title="'.__('Location').'" href="'.$row->address().'" target="_blank" ><i class="bx bx-map me-2"></i></a>';


          return $btn;
      })

      ->addColumn('seller', function ($row) {

          return [
            0 => $row->seller->image(),
            1 => $row->seller->enterprise()
          ];

      })

      ->addColumn('buyer', function ($row) {

        return [
          0 => $row->buyer->image(),
          1 => $row->buyer->enterprise()
        ];

    })

      ->addColumn('phone', function ($row) {

        return $row->phone();

      })

      ->addColumn('status', function ($row) {

        return $row->status;

      })

      ->addColumn('driver', function ($row) {

        if(!is_null($row->delivery)){
          return $row->delivery->driver->fullname();
        }

      })

      /* ->addColumn('purchase_amount', function ($row) {

        if(!is_null($row->invoice)){
          return number_format($row->invoice->purchase_amount,2,'.',',');
        }

      })

      ->addColumn('tax_amount', function ($row) {

        if(!is_null($row->invoice)){
          return number_format($row->invoice->tax_amount,2,'.',',');
        }

      })

      ->addColumn('total_amount', function ($row) {

        if(!is_null($row->invoice)){
          return number_format($row->invoice->total_amount,2,'.',',');
        }

      }) */

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d H:i',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function items(Request $request){

    $cart = Cart::findOrFail($request->cart_id);
    $items = $cart->items()->orderBy('created_at','DESC')->get();

    return datatables()
      ->of($items)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline edit" title="'.__('Edit').'" table_id="'.$row->id.'" quantity="'.$row->quantity.'"href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          return $btn;
      })

      ->addColumn('product', function ($row) {

          return $row->name();

      })


      ->addColumn('price', function ($row) {

        return number_format($row->price(),2,'.',',');

      })

      ->addColumn('type', function ($row) {

        return $row->type;

      })

      ->addColumn('quantity', function ($row) {

        return $row->quantity;

      })

      ->addColumn('discount', function ($row) {

        return $row->discount.'%';

      })

      ->addColumn('amount', function ($row) {

        return number_format($row->amount,2,'.',',');

      })


      ->make(true);
  }

  public function drivers(){

    $drivers = Driver::orderBy('created_at','DESC')->get();

    return datatables()
      ->of($drivers)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

          $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          return $btn;
      })

      ->addColumn('name', function ($row) {

        return $row->fullname();

      })

      ->addColumn('phone', function ($row) {

          return $row->phone();

      })

      ->addColumn('status', function ($row) {

          return $row->status();

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function users(Request $request){

    $users = User::whereNot('role',0)->latest();

    if(!empty($request->type)){
      $users = $users->where('role', $request->type);
    }

    if(!empty($request->status)){
      $users = $users->where('status', $request->status);
    }

    $users = $users->get();

    return datatables()
      ->of($users)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

          if($row->status == 'active'){
            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Block').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-x-circle me-2" style="color:#FF0017"></i></a>';
          }else{
            $btn .= '<a class="dropdown-item-inline restore" title="'.__('Activate').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-check-circle me-2" style="color:#00EF2E"></i></a>';
          }


          $btn .= '<a class="dropdown-item-inline reset_password" title="'.__('Reset password').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-reset me-2"></i></a>';

          if($row->longitude && $row->latitude){
            $btn .= '<a class="dropdown-item-inline" title="'.__('Location').'" href="'.$row->address().'" target="_blank" ><i class="bx bx-map me-2"></i></a>';
          }

          return $btn;
      })

      ->addColumn('name', function ($row) {

        return $row->fullname();

      })

      ->addColumn('enterprise', function ($row) {

        return $row->enterprise_name;

      })

      ->addColumn('phone', function ($row) {

          return $row->phone();

      })

      ->addColumn('email', function ($row) {

        return $row->email;

    })

      ->addColumn('status', function ($row) {

        return $row->status;


      })

      ->addColumn('type', function ($row) {

        return $row->role;

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function notices(){

    $notices = Notice::orderBy('created_at','DESC')->get();

    return datatables()
      ->of($notices)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

            $btn .= '<a class="dropdown-item-inline view" title="'.__('View').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-show me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          return $btn;
      })

      ->addColumn('title', function ($row) {

        if(Session::get('locale') == 'en'){
          return $row->title_en;
        }

        return $row->title_ar;
      })

      ->addColumn('type', function ($row) {

          return $row->type;

      })

      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }

  public function ads(){

    $ads = Ad::orderBy('created_at','DESC')->get();

    return datatables()
      ->of($ads)
      ->addIndexColumn()

      ->addColumn('action', function ($row) {
          $btn = '';

            $btn .= '<a class="dropdown-item-inline update" title="'.__('Edit').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-edit me-2"></i></a>';

            $btn .= '<a class="dropdown-item-inline delete" title="'.__('Delete').'" table_id="'.$row->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i></a>';

          return $btn;
      })

      ->addColumn('name', function ($row) {
        return $row->name;
      })

      ->addColumn('types', function ($row) {
        return $row->types()->pluck('type')->toArray();
      })



      ->addColumn('created_at', function ($row) {

        return date('Y-m-d',strtotime($row->created_at));

      })


      ->make(true);
  }


}
