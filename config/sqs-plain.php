<?php

/**
 * List of plain SQS queues and their corresponding handling classes
 */
return [
    'handlers' => [
        'file-extract-handler' => 'App\Jobs\FileExtractHandler',
    ],

    'default-handler' => 'App\Jobs\FileExtractHandler',
];