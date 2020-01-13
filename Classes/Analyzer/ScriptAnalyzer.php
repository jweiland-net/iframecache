<?php
declare(strict_types = 1);
namespace JWeiland\Iframecache\Analyzer;

/*
 * This file is part of the iframecache project.
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SCRIPT Analyzer
 */
class ScriptAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * @param string $content The full content of the JS file
     * @param string $uri
     * @param string $tagName
     * @return string
     */
    public function analyze(string $content, string $uri, string $tagName): string
    {
        // Only process files references over link-Tag
        if ($tagName !== 'script') {
            return $content;
        }

        return $content;
    }
}
