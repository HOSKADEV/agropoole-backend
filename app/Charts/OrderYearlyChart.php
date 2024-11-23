<?php

namespace App\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class OrderYearlyChart
{
  protected $chart;
  protected $orders;
  protected $year;
  public function __construct($orders, $year)
  {
    $this->chart = new LarapexChart();
    $this->orders = $orders;
    $this->year = $year ? $year : now()->year;
  }

  public function build()
  {

    $db_data = $this->orders->whereYear('orders.created_at', $this->year)->where('status','received')
    ->join('invoices', 'invoices.order_id', 'orders.id')
      ->groupBy(DB::raw(DB::raw("MONTH(orders.created_at)")))
      ->select(DB::raw("MONTH(orders.created_at) AS month"), DB::raw('SUM(invoices.purchase_amount) AS orders'))
      ->get()->pluck('orders','month')->toArray();


    //dd($db_data);

    $xaxis = [];
    $dates = [];
    $yaxis = [];
    for ($date = Carbon::create($this->year,1,1); $date <= Carbon::create($this->year,12,1); $date->addMonth()) {
      $dates[] = $date->format('Y-m-d');
      $xaxis[] = $date->monthName;
      $yaxis[] = $db_data[$date->month] ?? 0;
    }

    //dd($yaxis);

    return $this->chart->barChart()
      ->addData(__('Orders amount'), $yaxis)
      ->setXAxis($xaxis)
      ->setHeight(250)
      ->setColors(['#696cff'])
    ;
  }
}
