<?php
declare(strict_types = 1);
namespace JWeiland\Iframecache\DataProcessing;

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

use JWeiland\Iframecache\Analyzer\HtmlAnalyzer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * DataProcessor to update content of iFrame to a local source
 */
class UpdateIframeSourceProcessor implements DataProcessorInterface
{
    /**
     * @var HtmlAnalyzer
     */
    protected $htmlAnalyzer;

    public function __construct(HtmlAnalyzer $htmlAnalyzer = null)
    {
        $this->htmlAnalyzer = $htmlAnalyzer ?? GeneralUtility::makeInstance(HtmlAnalyzer::class);
    }

    /**
     * Process data of a record to resolve File objects to the view
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     * @throws \Exception
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $this->updateProcessedData($processedData);

        // set the files into a variable, default "files"
        if ($processedData['conf']['mode'] === 'iframe') {
            $processedData['iFrameSource'] = $this->htmlAnalyzer->analyze($processedData['conf']['iFrameSource']);
        } else {
            $processedData['jsAttributes'] = $this->getJsAttributes($processedData['conf']);
            $processedData['jsScript'] = $this->htmlAnalyzer->downloadUriToTempDir(
                $this->getJsScript($processedData['conf']['jsScript']),
                'js',
                'script'
            );
        }

        return $processedData;
    }

    protected function getJsScript(string $jsScript): string
    {
        if (StringUtility::beginsWith('EXT:', $jsScript)) {
            $jsScript = GeneralUtility::getFileAbsFileName($jsScript);
        }
        return $jsScript;
    }

    protected function updateProcessedData(array &$processedData)
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $conf = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['pi_flexform'] ?? []
        );
        $processedData['conf'] = $conf;
    }

    protected function getJsAttributes(array $conf): array
    {
        $jsAttributes = [];
        if (
            $conf['mode'] === 'script'
            && array_key_exists('jsAttributes', $conf)
            && !empty($conf['jsAttributes'])
        ) {
            $rows = GeneralUtility::trimExplode(chr(10), $conf['jsAttributes'], true);
            foreach ($rows as $row) {
                $parts = GeneralUtility::trimExplode('|*|', $row, true);
                if (count($parts) === 1) {
                    $jsAttributes[$parts[0]] = $parts[0];
                } elseif (count($parts) === 2) {
                    $jsAttributes[$parts[0]] = $parts[1];
                } elseif (count($parts) === 3 && $parts[2] === '1') {
                    $jsAttributes[$parts[0]] = $this->htmlAnalyzer->analyze($parts['1']);
                }
            }
        }
        return $jsAttributes;
    }
}
