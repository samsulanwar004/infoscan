<?php
namespace App\Services\Chart\Contracts;

interface ChartInterface
{
    public function weekly();
    public function daily();
    public function monthly();
    public function yearly();
}
