<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function() {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'iframecache',
        'Configuration/TypoScript',
        'iFrame Cache Default'
    );
});
