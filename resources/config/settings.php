<?php

return [
    'data' => [
        'cookie_tracking'  => true,
        'max_failed_attempts' => 5,
        'attempts_decay_minutes'  => 14440,
    ],
    'rules' => [
        'max_failed_attempts' => 'required|integer',
        'attempts_decay_minutes' => 'required|integer'
    ],
];
