<?php

namespace App\Charts;

use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class OrderStatusChart
{
    protected $chart;
    protected $orders;

    public function __construct($orders)
    {
        $this->chart = new LarapexChart();
        $this->orders = $orders;
    }

    public function build()
    {

      $data = $this->orders//->whereYear('created_at',now()->year)->whereMonth('created_at',now()->month)
      ->groupBy('status')->select('status', DB::raw('COUNT(id) as orders'))
      ->get()->pluck('orders','status')->toArray();

      $xaxis = [];
      $yaxis = [];

      foreach($data as $key => $value) {
        $xaxis[] = __($key);
        $yaxis[] = $value;
      }



        return $this->chart->donutChart()
            //->setTitle('Top 3 scorers of the team.')
            //->setSubtitle('Season 2021.')
            ->setHeight(250)
            ->addData($yaxis)
            ->setLabels($xaxis);
    }
}
