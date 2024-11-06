<?php

namespace App\Charts;

use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class TopProductsChart
{
    protected $chart;
    protected $user;
    protected $filter;

    public function __construct($user, $filter=null)
    {
        $this->chart = new LarapexChart();
        $this->user = $user;
        $this->filter = $filter;
    }

    public function build()
    {

      $data = $this->user->topProducts(empty($this->filter) ? null : now())->with('product')->take(6)->get();


      $xaxis = [];
      $yaxis = [];

      foreach($data as $product) {
        $xaxis[] = $product->unit_name;
        $yaxis[] = $product->items_count;
      }



      $chart = $this->chart->barChart()
            //->setTitle('Top 3 scorers of the team.')
            //->setSubtitle('Season 2021.')
            ->setHeight(200)
            ->addData(__('Orders count'),$yaxis)
            ->setLabels($xaxis)
            ->setHorizontal(true)
            ->setColors(['#20c997']);



         return $chart;
    }
}
