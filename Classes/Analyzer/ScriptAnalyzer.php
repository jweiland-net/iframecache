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
        if ($uriParts['path'] == '/js/wall-fluid.js') {
            // deactivate Cookie Support
            /*$content = str_replace(
                'cookieSupport:o.hasCookieSupport()',
                'cookieSupport:false',
                $content
            );
            $content = str_replace(
                'key:"hasCookieSupport",value:function(){try{return!!_.isString(document.cookie)&&(!!navigator.cookieEnabled&&(document.cookie="cookieSupport=1",document.cookie.indexOf("cookieSupport")>=0))}catch(e){return!1}}',
                'key:"hasCookieSupport",value:function(){return false}',
                $content
            );*/

            // Solve window.history.replaceState Bug
            /*$content = str_replace(
                'if(window.history.pushState)try{var t=this.urlHelper.getWallUrl();e?window.history.replaceState({detail:null},null,t):window.history.state&&window.history.state.detail&&window.history.pushState({detail:null},null,t)}catch(e){console.error(e)}',
                '',
                $content
            );*/
        } elseif ($uriParts['path'] == '/js/wall-fluid-libs.js') {
            // Deactivate binary support to get valid JSON
            $content = str_replace(
                'p(o,c),o.prototype.supportsBinary=!0',
                'p(o,c),o.prototype.supportsBinary=!1',
                $content
            );
        }

        return $content;
    }
}
