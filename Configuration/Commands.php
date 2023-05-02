<?php

return [
    'rkw_form:cleanup' => [
        'class' => \RKW\RkwForm\Command\CleanupCommand::class,
        'schedulable' => true,
    ],
    'rkw_form:fileCleanup' => [
        'class' => \RKW\RkwForm\Command\FileCleanupCommand::class,
        'schedulable' => true,
    ],
    'rkw_form:security' => [
        'class' => \RKW\RkwForm\Command\SecurityCommand::class,
        'schedulable' => true,
    ],
];
