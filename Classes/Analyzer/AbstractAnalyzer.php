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

use TYPO3\CMS\Core\Html\HtmlParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Abstract template analyzer
 */
abstract class AbstractAnalyzer
{
    /**
     * @var string
     */
    protected $cacheFolder = '';

    /**
     * @var string
     */
    protected $sourceUri = '';

    /**
     * @var HtmlParser
     */
    protected $htmlParser;

    public function __construct(HtmlParser $htmlParser = null)
    {
        $this->htmlParser = $htmlParser ?? GeneralUtility::makeInstance(HtmlParser::class);
    }

    public function setCacheFolder(string $cacheFolder)
    {
        $this->cacheFolder = $cacheFolder;
    }

    protected function getCacheFolder(): string
    {
        return PATH_site . sprintf(
            'typo3temp/assets/iframecache/%s/%s/%s/%s/',
            date('Y'),
            date('m'),
            date('d'),
            date('Hi')
        );
    }

    protected function downloadUriToTempDir(string $uri, string $subFolder, string $tagName = ''): string
    {
        $folder = rtrim($this->cacheFolder, '/') . '/' . rtrim($subFolder, '/') . '/';
        $content = GeneralUtility::getUrl($uri);
        $path = $folder . md5($content) . '.' . $this->getFileExtension($uri);
        return $this->writeFileToTempDir(
            $path,
            $this->postProcessContent($content, $uri, $tagName)
        );
    }

    protected function writeFileToTempDir(string $path, string $content): string
    {
        GeneralUtility::writeFileToTypo3tempDir($path, $content);
        return '/' . current(GeneralUtility::removePrefixPathFromList([$path], PATH_site));
    }

    /**
     * @param string $content
     * @param string $uri
     * @param string $tagName
     * @return string
     */
    protected function postProcessContent(string $content, string $uri, string $tagName): string
    {
        $className = sprintf(
            'JWeiland\\Iframecache\\Analyzer\\%sAnalyzer',
            ucfirst(strtolower($tagName))
        );
        if (class_exists($className)) {
            $object = GeneralUtility::makeInstance($className);
            if ($object instanceof AnalyzerInterface) {
                $object->setSourceUri($this->sourceUri);
                $object->setCacheFolder($this->cacheFolder);
                $content = $object->analyze($content, $uri, $tagName);
            }
        }

        return $content;
    }

    /**
     * In case of compressed files we may have js.gzip as file extension. Various tools return
     * "gzip" only, instead of full extension
     *
     * @param string $uri
     * @return string
     */
    protected function getFileExtension(string $uri): string
    {
        $uriParts = GeneralUtility::split_fileref(
            $this->removeQueryPartFromUri($uri)
        );
        if (array_key_exists('file', $uriParts)) {
            $fileParts = GeneralUtility::trimExplode('.', $uriParts['file'], true, 2);
            if (count($fileParts) > 1) {
                return $fileParts[1];
            }
        }
        return '';
    }

    /**
     * @param string $uri
     * @throws \Exception
     */
    public function setSourceUri(string $uri)
    {
        $uriParts = parse_url($uri);

        if (!is_array($uriParts)) {
            throw new \Exception('Source URI is not a valid URI.');
        }

        if (!array_key_exists('scheme', $uriParts) || empty($uriParts['scheme'])) {
            throw new \Exception('Source URI must start with a scheme like http://');
        }

        if (!array_key_exists('host', $uriParts) || empty($uriParts['host'])) {
            throw new \Exception('Source URI must consists of a host name like jweiland.net');
        }

        $this->sourceUri = $uri;
    }

    /**
     * For better cache update, TYPO3 appends a cache identifier to files.
     * Example: /typo3temp/compressed/merged-d2664b26?1564182671
     * This method removes the ? and everything behind
     *
     * @param string $uri
     * @return mixed|string
     */
    protected function removeQueryPartFromUri(string $uri)
    {
        $uriParts = GeneralUtility::trimExplode('?', $uri);
        if (count($uriParts) > 1) {
            return $uriParts[0];
        } else {
            return $uri;
        }
    }

    protected function buildDownloadUri(string $uri): string
    {
        $uriParts = parse_url($uri);
        if (
            array_key_exists('scheme', $uriParts)
            && !empty($uriParts['scheme'])
            && array_key_exists('host', $uriParts)
            && !empty($uriParts['host'])
        ) {
            // We have a full URI. Nothing to build
            return $uri;
        }

        // build Download URI
        $sourceUriParts = parse_url($this->sourceUri);
        $uri = $sourceUriParts['scheme'] . '://';
        $uri .= $sourceUriParts['host'];
        if (array_key_exists('port', $sourceUriParts) && !empty($sourceUriParts['port'])) {
            $uri .= ':' . $sourceUriParts['port'];
        }

        // if string begins with / we can not use path from source URI, because it's root
        if (
            array_key_exists('path', $uriParts)
            && !empty($uriParts['path'])
            && StringUtility::beginsWith($uriParts['path'], '/')
        ) {
            $uri .= $uriParts['path'];
        } else {
            // path begins without slash which indicates that we have to use path from Source URI
            $uri .= $sourceUriParts['path'] ?: '/';
        }

        if (array_key_exists('query', $uriParts) && !empty($uriParts['query'])) {
            $uri .= '?' . $uriParts['query'];
        }
        if (array_key_exists('fragment', $uriParts) && !empty($uriParts['fragment'])) {
            $uri .= '#' . $uriParts['fragment'];
        }

        return $uri;
    }
}
