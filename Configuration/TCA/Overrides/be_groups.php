<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3') || defined('TYPO3') || die();

$tempColumns = [
    'tx_igldapssoauth_dn' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang_db.xlf:be_groups.tx_igldapssoauth_dn',
        'config' => [
            'type' => 'input',
            'size' => 30,
        ]
    ],
];

ExtensionManagementUtility::addTCAcolumns('be_groups', $tempColumns);
ExtensionManagementUtility::addToAllTCAtypes('be_groups', 'tx_igldapssoauth_dn');
