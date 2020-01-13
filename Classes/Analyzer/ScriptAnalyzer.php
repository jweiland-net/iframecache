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
 * SCRIPT Analyzer
 */
class ScriptAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * @param string $content The full content of the JS file
     * @param string $uri
     * @param string $tagName
     * @return string
     */
    public function analyze(string $content, string $uri, string $tagName): string
    {
        // Only process files references over link-Tag
        if ($tagName !== 'script') {
            return $content;
        }

        $content = $this->modifyWallsFluid($content, $uri);

        return $content;
    }

    protected function modifyWallsFluid(string $content, string $uri)
    {
        $uriParts = parse_url($uri);
        if ($uriParts['path'] !== '/js/wall-fluid.js') {
            return $content;
        }

        // deactivate Cookie Support
        $content = str_replace(
            'cookieSupport:o.hasCookieSupport()',
            'cookieSupport:false',
            $content
        );

        // deactivate Cookie Support
        $content = str_replace(
            'key:"hasCookieSupport",value:function(){try{return!!_.isString(document.cookie)&&(!!navigator.cookieEnabled&&(document.cookie="cookieSupport=1",document.cookie.indexOf("cookieSupport")>=0))}catch(e){return!1}}',
            'key:"hasCookieSupport",value:function(){return false}',
            $content
        );

        // Solve window.hostory.replaceState Bug
        return str_replace(
            'if(window.history.pushState)try{var t=this.urlHelper.getWallUrl();e?window.history.replaceState({detail:null},null,t):window.history.state&&window.history.state.detail&&window.history.pushState({detail:null},null,t)}catch(e){console.error(e)}',
            '',
            $content
        );
    }
}
