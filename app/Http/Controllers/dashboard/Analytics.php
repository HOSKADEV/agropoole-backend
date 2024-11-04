<?php

namespace App\Http\Controllers\dashboard;

use Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Charts\OrderStatusChart;
use App\Charts\OrderMonthlyChart;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Analytics extends Controller
{

  public function index()
  {
    $user = auth()->user();

    $data = [];

    if (in_array($user->role_is(), ['broker', 'store'])) {
      $data['all_times_inbox_count'] = $user->soldOrders()->count();
      $data['all_times_outbox_count'] = $user->boughtOrders()->count();

      $data['this_month_inbox_count'] = $user->soldOrders()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
      $data['this_month_outbox_count'] = $user->boughtOrders()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

      $data['today_inbox_count'] = $user->soldOrders()
        ->whereDate('created_at', now())
        ->count();
      $data['today_outbox_count'] = $user->boughtOrders()
        ->whereDate('created_at', now())
        ->count();

      $data['pending_inbox_count'] = $user->pendingSoldOrders()->union($user->pendingDeliveredOrders())->count();
      $data['pending_outbox_count'] = $user->pendingBoughtOrders()->count();

      $data['all_times_stock_count'] = $user->stocks()->count();
      $data['insufficient_stock_count'] = $user->stocks()->whereColumn('quantity', '<=', 'min_quantity')->count();
      $data['this_month_stock_count'] = $user->stocks()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

      $data['today_stock_count'] = $user->stocks()
        ->whereDate('created_at', now())
        ->count();
    }

    if ($user->role_is('provider')) {
      $data['all_times_inbox_count'] = $user->soldOrders()->count();

      $data['this_month_inbox_count'] = $user->soldOrders()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

      $data['today_inbox_count'] = $user->soldOrders()
        ->whereDate('created_at', now())
        ->count();

      $data['pending_inbox_count'] = $user->pendingSoldOrders()->union($user->pendingDeliveredOrders())->count();

      $data['all_times_stock_count'] = $user->stocks()->count();
      $data['insufficient_stock_count'] = $user->stocks()->whereColumn('quantity', '<=', 'min_quantity')->count();
      $data['this_month_stock_count'] = $user->stocks()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

      $data['today_stock_count'] = $user->stocks()
        ->whereDate('created_at', now())
        ->count();
    }

    if ($user->role_is('driver')) {
      $data['all_times_inbox_count'] = $user->deliveredOrders()->count();

      $data['this_month_inbox_count'] = $user->deliveredOrders()
        ->whereYear('deliveries.created_at', now()->year)
        ->whereMonth('deliveries.created_at', now()->month)
        ->count();

      $data['today_inbox_count'] = $user->deliveredOrders()
        ->whereDate('deliveries.created_at', now())
        ->count();

      $data['pending_inbox_count'] = $user->pendingDeliveredOrders()->count();
    }




    return view('content.dashboard.dashboards-analytics')
      ->with($data)
    ;
  }

  public function stats()
  {
    $user = auth()->user();

    $data = [];

    if (in_array($user->role_is(), ['broker', 'store'])) {
        $data['inbox_status_chart'] = (new OrderStatusChart($user->soldOrders()))->build();
        $data['outbox_status_chart'] = (new OrderStatusChart($user->boughtOrders()))->build();
        $data['inbox_monthly_chart'] = (new OrderMonthlyChart($user->soldOrders(), __('New incoming orders'), 'created_at'))->build();
        $data['outbox_monthly_chart'] = (new OrderMonthlyChart($user->boughtOrders(), __('New outgoing orders'), 'created_at'))->build();
    }

    if ($user->role_is('provider')) {
        $data['inbox_status_chart'] = (new OrderStatusChart($user->soldOrders()))->build();
        $data['inbox_monthly_chart'] = (new OrderMonthlyChart($user->soldOrders(), __('New incoming orders'), 'created_at'))->build();
    }

    if ($user->role_is('driver')) {
        $data['inbox_status_chart'] = (new OrderStatusChart($user->deliveredOrders()))->build();
        $data['inbox_monthly_chart'] = (new OrderMonthlyChart($user->deliveredOrders(), __('New incoming orders'), 'deliveries.created_at'))->build();
    }

    return view('content.dashboard.stats')->with($data);
  }

  /*  public function index()
  {
    //dd(Auth::id());
    $last_year = Carbon::now()->subYear()->lastOfMonth();
    $last_month = Carbon::now()->subMonth()->lastOfMonth();
    $last_week = Carbon::now()->subDays(7);

    $paid_card = Invoice::whereDate('created_at', '>' ,$last_year->toDateString())->where('payment_method',1)->sum('total_amount');
    $paid_cash = Invoice::whereDate('created_at', '>' ,$last_year->toDateString())->where('payment_method',2)->sum('total_amount');
    $total_paid = $paid_cash + $paid_card;

    $orders_this_month = Order::whereDate('created_at', '>' , $last_month->toDateString())->count();
    $orders_this_week = Order::whereDate('created_at', '>' , $last_week->toDateString())->count();
    $users_this_week = User::whereDate('created_at', '>' , $last_week->toDateString())->count();

    $pending = Order::whereDate('created_at', '>' , $last_month->toDateString())->where('status',1)->count();
    $accepted = Order::whereDate('created_at', '>' , $last_month->toDateString())->where('status',2)->count();
    $canceled = Order::whereDate('created_at', '>' , $last_month->toDateString())->where('status',3)->count();
    $ongoing = Order::whereDate('created_at', '>' , $last_month->toDateString())->where('status',4)->count();
    $delivered = Order::whereDate('created_at', '>' , $last_month->toDateString())->where('status',5)->count();

    return view('content.dashboard.dashboards-analytics')
    ->with('paid_card',number_format($paid_card))
    ->with('paid_cash',number_format($paid_cash))
    ->with('total_paid',number_format($total_paid))
    ->with('orders_this_month',$orders_this_month)
    ->with('orders_this_week',$orders_this_week)
    ->with('users_this_week',$users_this_week)
    ->with('pending',$pending)
    ->with('accepted',$accepted)
    ->with('canceled',$canceled)
    ->with('ongoing',$ongoing)
    ->with('delivered',$delivered)
    ;
  }

  public function stats(){
    $last_year = Carbon::now()->subYear()->lastOfMonth();
    $last_month = Carbon::now()->subMonth()->lastOfMonth();
    $last_week = Carbon::now()->subDays(7);

    $orders_this_month = Order::whereDate('created_at', '>' , $last_month->toDateString())->count();
    $orders_this_week = Order::whereDate('created_at', '>' , $last_week->toDateString())->count();
    $users_this_week = User::whereDate('created_at', '>' , $last_week->toDateString())->count();
    $payments_this_month = Invoice::whereDate('created_at', '>' , $last_year->toDateString())
    ->groupBy(DB::raw('MONTH(created_at)'))->select(DB::raw('MONTH(created_at) as month'),
    DB::raw('SUM(CASE WHEN is_paid = 1 THEN total_amount ELSE 0 END) as paid'),
    DB::raw('SUM(CASE WHEN is_paid = 2 THEN total_amount ELSE 0 END) as unpaid'))
    ->get();

    $months = $payments_this_month->pluck('month')->toArray();
    $paid = $payments_this_month->pluck('paid')->toArray();
    $unpaid = $payments_this_month->pluck('unpaid')->toArray();
    $total = array_sum($paid) + array_sum($unpaid);
    $paid_percentage = array_sum($paid) * 100 / (max(1,$total));

    $months = array_map(function($value){
      $month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return $month_names[$value - 1];
    },$months);

    $unpaid = array_map(function($value){
      return - $value;
    },$unpaid);

    $days = [];
    $orders_array = [];
    $users_array = [];

    for($i=1 ; $i<8 ;$i++){
      $date = $last_week->copy()->addDays($i);
      array_push($days,$date->shortEnglishDayOfWeek);
      array_push($orders_array,Order::whereDate('created_at', $date->toDateString())->count());
      array_push($users_array,User::whereDate('created_at', $date->toDateString())->count());
    }

    $orders_status = [];

    for($i=1 ; $i<6 ;$i++){
      $count = Order::whereDate('created_at', '>' , $last_month->toDateString())->where('status',$i)->count();
      array_push($orders_status,$count);
    }

    $status_names = [__('Pending'), __('Accepted'), __('Canceled'), __('Ongoing'), __('Delivered')];
    $payment_status = [__('Paid'), __('Unpaid')];

    return response()->json([
      'status' => 1,
      'months' => $months,
      'paid' => $paid,
      'unpaid' => $unpaid,
      'payment_status' => $payment_status,
      'paid_percentage' => intval($paid_percentage),
      'orders_status' => $orders_status,
      'status_names' => $status_names,
      'days' => $days,
      'orders_array' => $orders_array,
      'users_array' => $users_array,
      'orders_this_month' => $orders_this_month,
      'orders_this_week' => $orders_this_week,
      'users_this_week' => $users_this_week,
    ]);


  } */
}
