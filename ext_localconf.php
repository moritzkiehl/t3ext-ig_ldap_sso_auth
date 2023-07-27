<?php
use TYPO3\CMS\Core\Imaging\IconFactory;
use Causal\IgLdapSsoAuth\Hooks\DatabaseRecordListIconUtility;
use Causal\IgLdapSsoAuth\Hooks\SetupModuleController;
use Causal\IgLdapSsoAuth\Hooks\DataHandler;
use Causal\IgLdapSsoAuth\Task\ImportUsers;
use Causal\IgLdapSsoAuth\Task\ImportUsersAdditionalFields;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Causal\IgLdapSsoAuth\Service\AuthenticationService;
use Causal\IgLdapSsoAuth\Form\Element\LdapSuggestElement;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Causal\IgLdapSsoAuth\Property\TypeConverter\ConfigurationConverter;
defined('TYPO3') || defined('TYPO3') || die();

(static function () {
    // Configuration of authentication service
    $EXT_CONFIG = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['ig_ldap_sso_auth'] ?? [];

    // SSO configuration
    if ($EXT_CONFIG['enableFESSO'] ?? false) {
        $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] = 1;
    }
    if ($EXT_CONFIG['enableBESSO'] ?? false) {
        $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['BE_fetchUserIfNoSession'] = 1;
    }

    // Visually change the record icon for FE/BE users and groups
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][IconFactory::class]['overrideIconOverlay'][] = \Causal\IgLdapSsoAuth\Hooks\IconFactory::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][] = DatabaseRecordListIconUtility::class;

    // Service configuration
    $subTypesArr = [];
    $subTypes = '';
    if ($EXT_CONFIG['enableFELDAPAuthentication'] ?? false) {
        $subTypesArr[] = 'getUserFE';
        $subTypesArr[] = 'authUserFE';
        $subTypesArr[] = 'getGroupsFE';
    }
    if ($EXT_CONFIG['enableBELDAPAuthentication'] ?? false) {
        $subTypesArr[] = 'getUserBE';
        $subTypesArr[] = 'authUserBE';

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/setup/mod/index.php']['modifyUserDataBeforeSave'][] = SetupModuleController::class . '->preprocessData';
    }
    if (is_array($subTypesArr)) {
        $subTypesArr = array_unique($subTypesArr);
        $subTypes = implode(',', $subTypesArr);
    }

    // Register hook for \TYPO3\CMS\Core\DataHandling\DataHandler
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = DataHandler::class;

    // Register the import users Scheduler task
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][ImportUsers::class] = [
        'extension' => 'ig_ldap_sso_auth',
        'title' => 'LLL:EXT:' . 'ig_ldap_sso_auth' . '/Resources/Private/Language/locallang.xlf:task.import_users.title',
        'description' => 'LLL:EXT:' . 'ig_ldap_sso_auth' . '/Resources/Private/Language/locallang.xlf:task.import_users.description',
        'additionalFields' => ImportUsersAdditionalFields::class
    ];

    ExtensionManagementUtility::addService(
        'ig_ldap_sso_auth',
        'auth' /* sv type */,
        AuthenticationService::class, /* sv key */
        [
            'title' => 'Authentication service',
            'description' => 'Authentication service for LDAP and SSO environment.',

            'subtype' => $subTypes,

            'available' => true,
            'priority' => 80,
            'quality' => 80,

            'os' => '',
            'exec' => '',

            'className' => AuthenticationService::class,
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1_553_520_893] = [
        'nodeName' => 'ldapSuggest',
        'priority' => 40,
        'class' => LdapSuggestElement::class,
    ];

    // Register type converters
    ExtensionUtility::registerTypeConverter(ConfigurationConverter::class);

    // User have save doc new button
    ExtensionManagementUtility::addUserTSConfig('options.saveDocNew.tx_igldapssoauth_config=1');
})();
