<?php

namespace App\Charts;

use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class TopStatesChart
{
    protected $chart;
    protected $states;

    public function __construct($states)
    {
        $this->chart = new LarapexChart();
        $this->states = $states;
    }

    public function build()
    {

      $data = $this->states->take(6)->get();


      $xaxis = [];
      $yaxis = [];

      foreach($data as $state) {
        $xaxis[] = $state->name;
        $yaxis[] = $state->orders_count;
      }



      $chart = $this->chart->barChart()
            //->setTitle('Top 3 scorers of the team.')
            //->setSubtitle('Season 2021.')
            ->setHeight(200)
            ->addData(__('Orders count'),$yaxis)
            ->setLabels($xaxis)
            ->setHorizontal(true)
            ->setColors(['#fd7e14']);



         return $chart;
    }
}
