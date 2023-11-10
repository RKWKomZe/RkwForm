<?php
declare(strict_types = 1);

return [
    \RKW\RkwForm\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
    \RKW\RkwForm\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    \RKW\RkwForm\Domain\Model\BstForm::class => [
        'tableName' => 'tx_rkwform_domain_model_standardform',
    ],
    \RKW\RkwForm\Domain\Model\GemCommunityForm::class => [
        'tableName' => 'tx_rkwform_domain_model_standardform',
    ],
];
