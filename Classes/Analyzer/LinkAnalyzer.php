<?php

declare(strict_types = 1);

/*
 * This file is part of the package jweiland/iframecache.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Iframecache\Analyzer;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * CSS Analyzer
 */
class LinkAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * @param string $content The full content of the CSS file
     * @param string $uri
     * @param string $tagName
     * @return string
     */
    public function analyze(string $content, string $uri, string $tagName): string
    {
        // Only process files references over link-Tag
        if ($tagName !== 'link') {
            return $content;
        }

        $cssFileParts = pathinfo($uri);
        $cssFileDirectory = rtrim($cssFileParts['dirname'], '/') . '/';

        return preg_replace_callback(
            '/url\((?<quote>["\']?)(?<file>.*?)["\']?\)/',
            function ($match) use ($cssFileDirectory) {
                $localFile = $this->downloadAndReplaceUri($cssFileDirectory, $match['file']);
                return sprintf(
                    'url(%s%s%s)',
                    $match['quote'],
                    $localFile,
                    $match['quote']
                );
            },
            $content
        );
    }

    protected function downloadAndReplaceUri(string $cssFileDirectory, string $uriInCssFile)
    {
        if (StringUtility::beginsWith($uriInCssFile, 'data:')) {
            // This is not a file. Keep untouched.
            return $uriInCssFile;
        }

        $uriParts = parse_url($uriInCssFile);
        if (
            array_key_exists('scheme', $uriParts)
            && !empty($uriParts['scheme'])
            && array_key_exists('host', $uriParts)
            && !empty($uriParts['host'])
        ) {
            // we have a full url starting withh something like http://
            $resolvedFileToDownload = $uriInCssFile;
        } elseif (StringUtility::beginsWith($uriInCssFile, '/')) {
            // It's a file calculated from root. So add scheme and host of cssFileDirectory
            $cssFileUriParts = parse_url($cssFileDirectory);
            $resolvedFileToDownload = $cssFileUriParts['scheme'] . '://';
            $resolvedFileToDownload .= $cssFileUriParts['host'];
            if (!empty($cssFileUriParts['port'])) {
                $resolvedFileToDownload .= ':' . $cssFileUriParts['port'];
            }
        } else {
            $resolvedFileToDownload = GeneralUtility::resolveBackPath($cssFileDirectory . $uriInCssFile);
        }
        return $this->downloadUriToTempDir($resolvedFileToDownload, 'assets');
    }
}
