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
 * HTML Analyzer
 */
class HtmlAnalyzer extends AbstractAnalyzer
{
    /**
     * @var array
     */
    protected $templateParts = [];

    public function analyze(string $uri = ''): string
    {
        $this->sourceUri = $uri ?: $this->sourceUri;
        $content = $this->getHtmlSource();
        if (empty($content)) {
            return $this->sourceUri;
        }

        $this->templateParts = $this->htmlParser->splitTags('img,script,link', $content);
        return $this->updateIFrameContent();
    }

    protected function updateIFrameContent(): string
    {
        $this->updateTagParts($this->getScriptParts(), 'src', 'js');
        $this->updateTagParts($this->getLinkParts(), 'href', 'css');
        $this->updateTagParts($this->getImgParts(), 'src', 'img');

        return $this->writeFileToTempDir(
            rtrim($this->cacheFolder, '/') . '/' . 'index.html',
            implode($this->templateParts)
        );
    }

    protected function updateTagParts(array $tagParts, string $attribute, string $subFolder)
    {
        foreach ($tagParts as $key => $tagPart) {
            $remoteSourcePath = $this->getAttributeFromTagPart($tagPart, $attribute);
            $localSourcePath = $this->downloadUriToTempDir(
                $this->buildDownloadUri($remoteSourcePath),
                $subFolder,
                $this->getTagNameFromTagPart($tagPart)
            );
            $this->updatePartInTemplate(
                $key,
                str_replace($remoteSourcePath, $localSourcePath, $tagPart)
            );
        }
    }

    protected function getTagNameFromTagPart(string $tagPart): string
    {
        $parts = GeneralUtility::trimExplode(' ', $tagPart, true, 2);
        return ltrim($parts[0], '<');
    }

    public function getScriptParts()
    {
        $scriptParts = $this->getPartsFromTemplateStartingWithTag('script');

        // Remove all script parts which are inline JavaScript only
        foreach ($scriptParts as $key => $scriptPart) {
            if ($scriptPart === '<script>') {
                unset($scriptParts[$key]);
            }
        }

        return $scriptParts;
    }

    public function getLinkParts()
    {
        $linkParts = $this->getPartsFromTemplateStartingWithTag('link');

        // As a link can also be a canonical, we have to remove the non stylesheet parts
        foreach ($linkParts as $key => $linkPart) {
            if ($this->getAttributeFromTagPart($linkPart, 'rel') !== 'stylesheet') {
                unset($linkParts[$key]);
            }
        }

        return $linkParts;
    }

    public function getImgParts()
    {
        return $this->getPartsFromTemplateStartingWithTag('img');
    }

    protected function getPartsFromTemplateStartingWithTag(string $tagName): array
    {
        $tagName = '<' . $tagName;
        $parts = [];
        foreach ($this->templateParts as $key => $templatePart) {
            if (strpos($templatePart, $tagName) === 0) {
                $parts[$key] = $templatePart;
            }
        }
        return $parts;
    }

    public function updatePartInTemplate(int $key, $content)
    {
        $this->templateParts[$key] = $content;
    }

    public function getAttributeFromTagPart(string $tagPart, string $attribute): string
    {
        $value = '';
        list($attributes, $metaData) = $this->htmlParser->get_tag_attributes($tagPart);
        if (array_key_exists($attribute, $attributes)) {
            $value = $attributes[$attribute];
        }
        return $value;
    }

    protected function getHtmlSource(): string
    {
        if (GeneralUtility::isValidUrl($this->sourceUri)) {
            return GeneralUtility::getUrl($this->sourceUri);
        }
        return '';
    }
}
