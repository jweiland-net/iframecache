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
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * DataProcessor to update content of iFrame to a local source
 */
class UpdateIframeSourceProcessor implements DataProcessorInterface
{
    /**
     * Process data of a record to resolve File objects to the view
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $this->updateProcessedData($processedData);

        $templateAnalyzer = GeneralUtility::makeInstance(HtmlAnalyzer::class);;

        // set the files into a variable, default "files"
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'src');
        $processedData[$targetVariableName] = $templateAnalyzer->analyze($processedData['conf']['src']);

        return $processedData;
    }

    protected function updateProcessedData(array &$processedData)
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $processedData['conf'] = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['pi_flexform'] ?? []
        );
    }
}
