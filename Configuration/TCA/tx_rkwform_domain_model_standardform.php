<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform',
        'label' => 'salutation',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'enablecolumns' => [

        ],
        'searchFields' => 'salutation,first_name,last_name,company,email,phone,text',
        'iconfile' => 'EXT:rkw_form/Resources/Public/Icons/tx_rkwform_domain_model_standardform.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'salutation, first_name, last_name, company, email, phone, text',
    ],
    'types' => [
        '1' => ['showitem' => 'salutation, first_name, last_name, company, email, phone, text, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [

        'salutation' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.salutation',
	        'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.salutation.I.99', 99),
                    array('LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.salutation.I.0', 0),
                    array('LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.salutation.I.1', 1)
                ),
                'default' => 99,
                'size' => 1,
                'maxitems' => 1,
			]
	    ],
	    'first_name' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.first_name',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'last_name' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.last_name',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'company' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.company',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'email' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.email',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'phone' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.phone',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
        'street' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'postal' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.postal',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'topic' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.topic',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
	    'text' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.text',
	        'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
			],
	    ],
    ],
];
