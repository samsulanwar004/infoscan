<?php

$queues = [
    ['connection' => 'sqs', 'name' => 'pointProcess'],
    ['connection' => 'sqs', 'name' => 'memberLog'],
    ['connection' => 'sqs', 'name' => 'crowdsourceLog'],
    ['connection' => 'sqs', 'name' => 'transactionProcess'],
];


function runCommand($connection, $name)
{
    $command = 'sudo /usr/bin/php /var/www/infoscan_file_process/artisan queue:listen '.$connection.' --queue=\''.$name.'\' --tries=0 --sleep=3 > /dev/null & echo $!';
    $number = exec($command);
    file_put_contents('/tmp/'.$name.'.pid', $number);
}

foreach ($queues as $queue) {
    $file = '/tmp/' . $queue['name'] . '.pid';
    if (file_exists($file)) {
        $pid = file_get_contents($file);
        $result = exec('ps | grep ' . $pid);
        if ($result == '') {
            runCommand($queue['connection'], $queue['name']);
        }
    } else {
        runCommand($queue['connection'], $queue['name']);
    }
}
