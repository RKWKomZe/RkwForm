<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard',
        'label' => 'salutation',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'salutation,first_name,last_name,company,email,phone,text',
        'iconfile' => 'EXT:rkw_form/Resources/Public/Icons/tx_rkwform_domain_model_standard.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, salutation, first_name, last_name, company, email, phone, text',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, salutation, first_name, last_name, company, email, phone, text, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_rkwform_domain_model_standard',
                'foreign_table_where' => 'AND tx_rkwform_domain_model_standard.pid=###CURRENT_PID### AND tx_rkwform_domain_model_standard.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ]
            ],
        ],
        'salutation' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.salutation',
	        'config' => [
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.salutation.I.99', 99),
                    array('LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.salutation.I.0', 0),
                    array('LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.salutation.I.1', 1)
                ),
                'default' => 99,
                'size' => 1,
                'maxitems' => 1,
			]
	    ],
	    'first_name' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.first_name',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'last_name' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.last_name',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'company' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.company',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'email' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.email',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'phone' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.phone',
	        'config' => [
			    'type' => 'input',
			    'size' => 30,
			    'eval' => 'trim'
			],
	    ],
	    'text' => [
	        'exclude' => true,
	        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standard.text',
	        'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
			],
	    ],
    ],
];
