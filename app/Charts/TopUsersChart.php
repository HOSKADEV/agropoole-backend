<?php

namespace App\Charts;

use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class TopUsersChart
{
    protected $chart;
    protected $users;
    protected $label;
    protected $horizontal;

    public function __construct($users, $label, $horizontal=true)
    {
        $this->chart = new LarapexChart();
        $this->users = $users;
        $this->label = $label;
        $this->horizontal = $horizontal;
    }

    public function build()
    {

      $data = $this->users->take(6)->get();


      $xaxis = [];
      $yaxis = [];

      foreach($data as $user) {
        $xaxis[] = empty($user->enterprise_name) ? $user->name : $user->enterprise_name;
        $yaxis[] = $user->orders;
      }



      $chart = $this->chart->barChart()
            //->setTitle('Top 3 scorers of the team.')
            //->setSubtitle('Season 2021.')
            ->setHeight(200)
            ->addData(__($this->label),$yaxis)
            ->setLabels($xaxis)
            ->setHorizontal($this->horizontal)
            ->setColors(['#03c3ec']);



         return $chart;
    }
}
