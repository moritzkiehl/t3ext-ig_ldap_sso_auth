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

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Render a conf* View helper which renders the flash messages (if there are any).
 *
 * @author     Xavier Perseguers <xavier@causal.ch>
 * @package    TYPO3
 * @subpackage ig_ldap_sso_auth
 */
class FlashMessagesViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\FlashMessagesViewHelper
{

    /**
     * @var string The message severity class names
     */
    protected static $classes = [
        AbstractMessage::NOTICE => 'notice',
        AbstractMessage::INFO => 'info',
        AbstractMessage::OK => 'success',
        AbstractMessage::WARNING => 'warning',
        AbstractMessage::ERROR => 'danger'
    ];

    /**
     * @var string The message severity icon names
     */
    protected static $icons = [
        AbstractMessage::NOTICE => 'lightbulb-o',
        AbstractMessage::INFO => 'info',
        AbstractMessage::OK => 'check',
        AbstractMessage::WARNING => 'exclamation',
        AbstractMessage::ERROR => 'times'
    ];

    /**
     * Renders FlashMessages and flushes the FlashMessage queue
     * Note: This disables the current page cache in order to prevent FlashMessage output
     * from being cached.
     *
     * @see \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::no_cache
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $as = $arguments['as'];
        $queueIdentifier = $arguments['queueIdentifier'] ?? null;
        $flashMessages = $renderingContext->getControllerContext()
            ->getFlashMessageQueue($queueIdentifier)->getAllMessagesAndFlush();
        if ($flashMessages === null || empty($flashMessages)) {
            return '';
        }

        if ($as === null) {
            $out = [];
            foreach ($flashMessages as $flashMessage) {
                $out[] = static::renderFlashMessage($flashMessage);
            }
            return implode(LF, $out);
        }
        $templateVariableContainer = $renderingContext->getVariableProvider();
        $templateVariableContainer->add($as, $flashMessages);
        $content = $renderChildrenClosure();
        $templateVariableContainer->remove($as);

        return $content;
    }

    /**
     * @return string
     */
    protected static function renderFlashMessage(FlashMessage $flashMessage): string
    {
        $className = 'alert-' . static::$classes[$flashMessage->getSeverity()];
        $iconName = 'fa-' . static::$icons[$flashMessage->getSeverity()];

        $messageTitle = $flashMessage->getTitle();
        $markup = [];
        $markup[] = '<div class="alert ' . $className . '">';
        $markup[] = '    <div class="media">';
        $markup[] = '        <div class="media-left">';
        $markup[] = '            <span class="fa-stack fa-lg">';
        $markup[] = '                <i class="fa fa-circle fa-stack-2x"></i>';
        $markup[] = '                <i class="fa ' . $iconName . ' fa-stack-1x"></i>';
        $markup[] = '            </span>';
        $markup[] = '        </div>';
        $markup[] = '        <div class="media-body">';
        if (!empty($messageTitle)) {
            $markup[] = '            <h4 class="alert-title">' . htmlspecialchars((string) $messageTitle) . '</h4>';
        }
        $markup[] = '            <p class="alert-message">' . $flashMessage->getMessage() . '</p>';
        $markup[] = '        </div>';
        $markup[] = '    </div>';
        $markup[] = '</div>';
        return implode('', $markup);
    }
}
