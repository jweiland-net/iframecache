<?php
declare(strict_types = 1);
namespace JWeiland\Iframecache\Controller;

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

use JWeiland\Maps2\Domain\Model\Position;
use JWeiland\Maps2\Service\GeoCodeService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Our main controller to analyze the iframe's content
 */
class IframeController
{
    /**
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * Main Action
     *
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function mainAction(string $content = '', array $conf = []): string
    {
        return 'Yeah';
    }
}
