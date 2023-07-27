<?php
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use Causal\IgLdapSsoAuth\Utility\CompatUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Causal\IgLdapSsoAuth\Controller\ModuleController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3') || defined('TYPO3') || die();

(static function() {
    // Register additional sprite icons
    /** @var IconRegistry $iconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon('extensions-ig_ldap_sso_auth-overlay-ldap-record',
        BitmapIconProvider::class,
        [
            'source' => 'EXT:' . 'ig_ldap_sso_auth' . '/Resources/Public/Icons/overlay-ldap-record.png',
        ]
    );
    unset($iconRegistry);

    // Hopefully CompatUtility::getTypo3Mode() will never be null in TYPO3 v12
    $typo3Mode = CompatUtility::getTypo3Mode() ?? TYPO3_MODE;
    if ($typo3Mode === 'BE') {
        // Add BE module on top of system main module
        ExtensionUtility::registerModule(
            'ig_ldap_sso_auth',
            'system',
            'txigldapssoauthM1',
            'top',
            [
                ModuleController::class => implode(',', [
                    'index',
                    'status',
                    'search',
                    'importFrontendUsers', 'importBackendUsers',
                    'importFrontendUserGroups', 'importBackendUserGroups',
                ]),
            ], [
                'access' => 'admin',
                'icon' => 'EXT:' . 'ig_ldap_sso_auth' . '/Resources/Public/Icons/module-ldap.png',
                'labels' => 'LLL:EXT:' . 'ig_ldap_sso_auth' . '/Resources/Private/Language/locallang.xlf'
            ]
        );
    }

    // Initialize "context sensitive help" (csh)
    ExtensionManagementUtility::addLLrefForTCAdescr('tx_igldapssoauth_config', 'EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang_csh_db.xlf');
})();
