<?php

namespace App\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class OrderDailyChart
{
  protected $chart;
  protected $orders;
  protected $label;
  protected $column;
  public function __construct($orders, $label, $column)
  {
    $this->chart = new LarapexChart();
    $this->orders = $orders;
    $this->label = $label;
    $this->column = $column;
  }

  public function build()
  {

    $data = $this->orders->whereDate($this->column, '>=', Carbon::now()->subDays(6))
      ->groupBy(DB::raw("DATE({$this->column})"))
      ->select(DB::raw("DATE({$this->column}) as date"), DB::raw('COUNT(orders.id) AS orders'))
      ->get()->pluck('orders', 'date')->all();

    $xaxis = [];
    $dates = [];
    $yaxis = [];
    for ($date = Carbon::now()->subDays(6); $date <= Carbon::now(); $date->addDay()) {
      $dates[] = $date->format('Y-m-d');
      $xaxis[] = $date->dayName;
      $yaxis[] = $data[$date->format('Y-m-d')] ?? 0;
    }

    //dd($yaxis);

    return $this->chart->areaChart()
      ->addData($this->label, $yaxis)
      ->setXAxis($xaxis)
      //->setSparkline()
      ->setStroke(width: 4, curve: 'smooth', /* colors:['#04EA8B'] */)
      ->setHeight(200)
      ->setDataLabels(true)
      //->setFontFamily('Readex Pro')
      //->setFontColor(Session::get('theme') == 'dark' ? '#FFFFFF' : '#000000')
      //->setColors(['#04EA8B'])
    ;
  }
}
