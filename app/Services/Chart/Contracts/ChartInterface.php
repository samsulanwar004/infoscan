<?php
namespace App/Services/Chart/Contract;

class ChartInterface {
    public function weekly();
    public function daily();
    public function monthly();
    public function yearly();
}
