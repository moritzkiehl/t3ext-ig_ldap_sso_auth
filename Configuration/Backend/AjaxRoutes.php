<?php

use Causal\IgLdapSsoAuth\Controller\ModuleController;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * Definitions for routes provided by EXT:ig_ldap_sso_auth
 */
return [
    'ldap_form_update' => [
        'path' => '/ldap/form/update',
        'target' => ModuleController::class . '::ajaxUpdateForm'
    ],
    'ldap_search' => [
        'path' => '/ldap/search',
        'target' => ModuleController::class . '::ajaxSearch'
    ],
    'ldap_users_import' => [
        'path' => '/ldap/users/import',
        'target' => ModuleController::class . '::ajaxUsersImport'
    ],
    'ldap_groups_import' => [
        'path' => '/ldap/groups/import',
        'target' => ModuleController::class . '::ajaxGroupsImport'
    ],
];
