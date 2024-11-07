<?php

namespace App\Charts;

use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class UserStatusChart
{
  protected $chart;
  protected $users;
  protected $filter;
protected $column;
  public function __construct($users, $filter = null, $column='created_at')
  {
    $this->chart = new LarapexChart();
    $this->users = $users;
    $this->filter = $filter;
    $this->column = $column;
  }

  public function build()
  {

    $data = $this->users->whereNot('role',0);

    if ($this->filter) {
      $data = $data->whereYear($this->column, now()->year)->whereMonth($this->column, now()->month);
    }

    $data = $data->groupBy('role')->select('role', DB::raw('COUNT(users.id) as users'))
      ->get()->pluck('users', 'role')->toArray();
//dd($data);
    $xaxis = [];
    $yaxis = [];

    $colors = [
      1 => '#696cff',
      2 => '#007bff',
      3 => "#03c3ec",
      4 => '#20c997',
      5 => '#71dd37'
    ];

    $roles = [
      1 => 'provider',
      2 => 'broker',
      3 => "store",
      4 => 'client',
      5 => 'driver'
    ];

    $markers = [];

    foreach ($data as $key => $value) {
      $xaxis[] = __($roles[$key]);
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
