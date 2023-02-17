<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/iframecache.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Iframecache\Analyzer;

use TYPO3\CMS\Core\Core\Environment;
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
     * @var int
     */
    protected $contentElementUid = 0;

    /**
     * @var string
     */
    protected $sourceUri = '';

    /**
     * @var HtmlParser
     */
    protected $htmlParser;

    public function __construct(int $contentElementUid = 0, HtmlParser $htmlParser = null)
    {
        $this->contentElementUid = $contentElementUid;
        $this->setCacheFolder($this->getCacheFolder());
        $this->htmlParser = $htmlParser ?? GeneralUtility::makeInstance(HtmlParser::class);
    }

    public function setCacheFolder(string $cacheFolder): void
    {
        $this->cacheFolder = $cacheFolder;
    }

    public function setContentElementUid(int $contentElementUid): void
    {
        $this->contentElementUid = $contentElementUid;
        $this->setCacheFolder($this->getCacheFolder());
    }

    protected function getCacheFolder(): string
    {
        $cacheFolder = sprintf(
            'typo3temp/assets/iframecache/%d/',
            $this->contentElementUid
        );

        return Environment::getPublicPath() . '/' . $cacheFolder;
    }

    public function downloadUriToTempDir(string $uri, string $subFolder, string $tagName = ''): string
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

        return '/' . current(GeneralUtility::removePrefixPathFromList([$path], Environment::getPublicPath() . '/'));
    }

    protected function postProcessContent(string $content, string $uri, string $tagName): string
    {
        $className = sprintf(
            'JWeiland\\Iframecache\\Analyzer\\%sAnalyzer',
            ucfirst(strtolower($tagName))
        );

        if (class_exists($className)) {
            $object = GeneralUtility::makeInstance($className);
            if ($object instanceof AnalyzerInterface) {
                $object->setSourceUri($this->sourceUri ?: $uri);
                $object->setContentElementUid($this->contentElementUid);
                $object->setCacheFolder($this->cacheFolder);
                $content = $object->analyze($content, $uri, $tagName);
            }
        }

        return $content;
    }

    /**
     * In case of compressed files we may have js.gzip as file extension. Various tools return
     * "gzip" only, instead of full extension
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

    public function setSourceUri(string $uri): void
    {
        $uriParts = parse_url($uri);

        if (!is_array($uriParts)) {
            throw new \Exception('Source URI is not a valid URI.');
        }

        if (!array_key_exists('scheme', $uriParts) || empty($uriParts['scheme'])) {
            throw new \Exception('Source URI must start with a scheme like https://');
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
     */
    protected function removeQueryPartFromUri(string $uri): string
    {
        $uriParts = GeneralUtility::trimExplode('?', $uri);
        if (count($uriParts) > 1) {
            return $uriParts[0];
        }

        return $uri;
    }

    protected function buildDownloadUri(string $uri): string
    {
        $uriParts = parse_url($uri);
        if (array_key_exists('scheme', $uriParts)
        && !empty($uriParts['scheme'])
        && array_key_exists('host', $uriParts)
        && !empty($uriParts['host'])) {
            // We have a full URI. Nothing to build
            return $uri;
        }

        if (
            array_key_exists('host', $uriParts)
            && !empty($uriParts['host'])
        ) {
            // Something like '//apis.google.com/libraries/jquery...'
            return 'https://' . ltrim($uri, '/');
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
