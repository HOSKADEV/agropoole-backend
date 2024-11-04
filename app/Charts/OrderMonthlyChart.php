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

    $db_data = $this->orders->whereDate($this->column, '>=', Carbon::now()->subMonths(6)->firstOfMonth())
      ->groupBy(DB::raw("YEAR({$this->column})"), DB::raw("MONTH({$this->column})"))
      ->select(DB::raw("YEAR({$this->column}) as year"), DB::raw("MONTH({$this->column}) AS month"), DB::raw('COUNT(orders.id) AS orders'))
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
      ->setHeight(200)
      ->setDataLabels(true)
      //->setFontFamily('Readex Pro')
      //->setFontColor(Session::get('theme') == 'dark' ? '#FFFFFF' : '#000000')
      //->setColors(['#04EA8B'])
    ;
  }
}
