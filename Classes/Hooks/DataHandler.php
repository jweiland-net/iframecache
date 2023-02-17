<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/iframecache.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Iframecache\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook for DataHandler
 */
class DataHandler
{
    /**
     * Flushes the cache if a tt_content record was edited.
     *
     * @param array $params
     */
    public function clearCachePostProc(array $params)
    {
        if (
            (isset($params['table']) && $params['table'] === 'tt_content')
            || (isset($params['cacheCmd']) && in_array($params['cacheCmd'], ['all', 'pages', 'system'], true))
        ) {
            GeneralUtility::flushDirectory(PATH_site . 'typo3temp/assets/iframecache', true);
        }
    }
}
