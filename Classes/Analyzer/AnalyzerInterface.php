<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/iframecache.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Iframecache\Analyzer;

/**
 * Interface for Analyzers
 */
interface AnalyzerInterface
{
    /**
     * @param string $cacheFolder
     */
    public function setCacheFolder(string $cacheFolder);

    /**
     * @param string $sourceUri
     */
    public function setSourceUri(string $sourceUri);

    /**
     * @param int $contentElementUid
     */
    public function setContentElementUid(int $contentElementUid);

    /**
     * @param string $content The full content of the referenced file
     * @param string $uri The URIO of the referenced file
     * @param string $tagName Something like link, script or img
     * @return string
     */
    public function analyze(string $content, string $uri, string $tagName): string;
}
