<?php

namespace App\Charts;

use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class OrderStatusChart
{
  protected $chart;
  protected $orders;
  protected $filter;

  public function __construct($orders, $filter = null)
  {
    $this->chart = new LarapexChart();
    $this->orders = $orders;
    $this->filter = $filter;
  }

  public function build()
  {

    $data = $this->orders;

    if ($this->filter) {
      $data = $data->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
    }

    $data = $data->groupBy('status')->select('status', DB::raw('COUNT(orders.id) as orders'))
      ->get()->pluck('orders', 'status')->toArray();

    $xaxis = [];
    $yaxis = [];

    $colors = [
      'pending' => '#004B80',
      'accepted' => '#007bff',
      'canceled' => '#ff3e1d',
      'confirmed' => '#fd7e14',
      'shipped' => '#ffab00',
      'ongoing' => "#03c3ec",
      'delivered' => '#20c997',
      'received' => '#71dd37'
    ];

    $markers = [];

    foreach ($data as $key => $value) {
      $xaxis[] = __($key);
      $yaxis[] = $value;
      $markers[] = $colors[$key];
    }



    $chart = $this->chart->donutChart()
      //->setTitle('Top 3 scorers of the team.')
      //->setSubtitle('Season 2021.')
      ->setHeight(200)
      ->addData(empty($yaxis) ? [1] : $yaxis)
      ->setLabels(empty($xaxis) ? [__('No data')] : $xaxis);



    return empty($xaxis) ? $chart->setColors(['#8592a3']) : $chart->setColors($markers);
  }
}
