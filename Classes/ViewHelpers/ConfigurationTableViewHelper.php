<?php
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

namespace Causal\IgLdapSsoAuth\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Render a configuration table
 *
 * @author     Xavier Perseguers <xavier@causal.ch>
 * @package    TYPO3
 * @subpackage ig_ldap_sso_auth
 */
class ConfigurationTableViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('data', 'mixed', 'Data as array or string', true);
        $this->registerArgument('humanKeyNames', 'bool', 'Use human key names', false, false);
    }

    /**
     * Renders a configuration table.
     *
     * @return string
     */
    public function render(): string
    {
        $hasError = false;
        $content = $this->renderTable($this->arguments['data'], $this->arguments['humanKeyNames'], 1, $hasError);
        return $content;
    }

    /**
     * Renders a configuration table.
     *
     * @param array|string $data
     * @return string
     */
    protected function renderTable($data, string $humanKeyNames, int $depth, bool &$hasError): string
    {
        if (!is_array($data)) {
            return htmlspecialchars($data);
        } elseif (empty($data)) {
            return '<em>' . htmlspecialchars($this->translate('module_status.messages.empty')) . '</em>';
        }

        $tableClass = 'table table-striped table-hover';
        $trClass = '';

        $content = [];
        foreach ($data as $key => $value) {
            $hasValueError = false;
            $valueCell = $this->renderValueCell($value, $key, $depth, $hasValueError);
            $class = 'key';
            if ($hasValueError) {
                $hasError = true;
                $class .= ' error';
            }
            if ($humanKeyNames) {
                $key = $this->processKey($key);
            }
            $content[] = sprintf('<tr class="' . $trClass . '"><td class="' . $class . '">%s</td>%s</tr>', htmlspecialchars($key), $valueCell);
        }

        return '<table class="' . $tableClass . '">' . implode(LF, $content) . '</table>';
    }

    /**
     * Renders a configuration value in a table cell.
     *
     * @return string
     */
    protected function renderValueCell(mixed $value, string $key, int $depth, bool &$hasError): string
    {
        if ($key === '__errors') {
            $hasError = true;
        }
        if (is_array($value)) {
            return sprintf('<td>%s</td>', $this->renderTable($value, false, $depth + 1, $hasError));
        }

        /** @var IconFactory $iconFactory */
        static $iconFactory = null;
        if ($iconFactory === null) {
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        }

        $class = 'value-default';

        if (is_bool($value)) {
            if ($value === true) {
                $icon = 'actions-edit-hide';
                $messageId = 'module_status.messages.enabled';
                $class = 'value-enabled';
            } else {
                $icon = 'actions-edit-unhide';
                $messageId = 'module_status.messages.disabled';
                $class = 'value-disabled';
            }
            $value = $iconFactory->getIcon($icon, Icon::SIZE_SMALL)->render();
            $value .=  ' ' . htmlspecialchars($this->translate($messageId));
        } elseif ($depth > 1 && $key === 'status') {
            $label = $value;
            if ($value === 'Success') {
                $icon = 'status-dialog-ok';
                $class = 'value-success';
            } else {
                $icon = 'status-dialog-warning';
                $class = 'value-error';
                $hasError = true;
            }
            $value = $iconFactory->getIcon($icon, Icon::SIZE_SMALL)->render();
            $value .=  ' ' . htmlspecialchars((string) $label);
        } elseif ($value instanceof AbstractEntity) {
            if ($value instanceof BackendUserGroup) {
                $icon = 'status-user-group-backend';
                $table = 'be_groups';
            } else {
                $icon = 'status-user-group-frontend';
                $table = 'fe_groups';
            }
            $options = [
                'title' => 'id=' . $value->getUid(),
            ];
            /** @var \Causal\IgLdapSsoAuth\Hooks\IconFactory $iconFactoryHook */
            static $iconFactoryHook = null;
            if ($iconFactoryHook === null) {
                $iconFactoryHook = GeneralUtility::makeInstance(\Causal\IgLdapSsoAuth\Hooks\IconFactory::class);
            }
            $overlay = $iconFactoryHook->postOverlayPriorityLookup(
                $table,
                ['uid' => $value->getUid()],
                [],
                null
            );
            $value = $iconFactory->getIcon($icon, Icon::SIZE_SMALL, $overlay)->render() . ' ' . htmlspecialchars((string) $value->getTitle());
            $value = str_replace('<img src=', '<img title="' . htmlspecialchars($options['title']) . '" src=', $value);
        } else {
            $value = htmlspecialchars((string) $value);
        }

        return sprintf('<td class="%s">%s</td>', $class, $value);
    }

    /**
     * Returns a meaningful description out of a configuration array key.
     *
     * @return string
     */
    protected function processKey(string $key): string
    {
        return $this->translate('module_status.configuration.' . $key);
    }

    /**
     * Translates a label.
     *
     * @return string|null
     */
    protected function translate(string $id, ?array $arguments = null): ?string
    {
        $value = LocalizationUtility::translate($id, 'ig_ldap_sso_auth', $arguments);
        return $value ?? $id;
    }
}
