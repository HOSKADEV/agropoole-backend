<?php

namespace App\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class OrderMonthlyChart
{
  protected $chart;
  protected $orders;
  protected $label;
  public function __construct($orders, $label)
  {
    $this->chart = new LarapexChart();
    $this->orders = $orders;
    $this->label = $label;
  }

  public function build()
  {

    $db_data = $this->orders->whereDate('created_at', '>=', Carbon::now()->subMonths(6)->firstOfMonth())
      ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
      ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) AS month'), DB::raw('COUNT(id) AS orders'))
      ->get()->toArray();

    $data = [];

    foreach ($db_data as $item) {
      $data[Carbon::createFromDate($item['year'] . '-' . $item['month'] . '-1')->format('Y-m-d')] = $item['orders'];
    }

    //dd($data);

    $xaxis = [];
    $dates = [];
    $yaxis = [];
    for ($date = Carbon::now()->subMonths(5)->firstOfMonth(); $date <= Carbon::now(); $date->addMonth()) {
      $dates[] = $date->format('Y-m-d');
      $xaxis[] = $date->monthName;
      $yaxis[] = $data[$date->format('Y-m-d')] ?? 0;
    }

    //dd($yaxis);

    return $this->chart->areaChart()
      ->addData($this->label, $yaxis)
      ->setXAxis($xaxis)
      //->setSparkline()
      ->setStroke(width: 4, curve: 'smooth', /* colors:['#04EA8B'] */)
      ->setHeight(250)
      ->setDataLabels(true)
      //->setFontFamily('Readex Pro')
      //->setFontColor(Session::get('theme') == 'dark' ? '#FFFFFF' : '#000000')
      //->setColors(['#04EA8B'])
    ;
  }
}
