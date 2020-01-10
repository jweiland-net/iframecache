<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        // Configure frontend plugin
        $pluginContent = trim('
tt_content.iframecache =< lib.contentElement
tt_content.iframecache {
    templateName = Iframecache
    20 = USER
    20 {
        userFunc = JWeiland\\Iframecache\\Controller\\IframeController->mainAction
    }
}');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'iframecache',
            'setup',
            '
# Setting Iframecache plugin TypoScript
' . $pluginContent,
            'defaultContentRendering'
        );

        // Register SVG Icon Identifier
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $svgIcons = [
            'ext-iframecache-wizard-icon' => 'plugin_wizard.svg',
        ];
        foreach ($svgIcons as $identifier => $fileName) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:iframecache/Resources/Public/Icons/' . $fileName]
            );
        }

        // add iframecache plugin to new element wizard
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:iframecache/Configuration/TSconfig/ContentElementWizard.txt">'
        );
    }
);
