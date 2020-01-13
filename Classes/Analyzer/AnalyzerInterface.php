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
