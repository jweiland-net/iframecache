<?php
declare(strict_types = 1);
namespace JWeiland\Iframecache\Service;

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
 * Controller
 */
class WallsService
{
    /**
     * It's really hard to interpret binary support.
     * Keep this value disabled to switch over to base64
     *
     * @var bool
     */
    protected $useBinarySupport = false;

    /**
     * Header of first request.
     * Needed to extract cookie
     *
     * @var array
     */
    protected $header = [];

    public function getWalls(): array
    {
        return $this->getData(
            $this->getSessionId()
        );
    }

    protected function getSessionId(): string
    {
        $errors = [];
        $result = GeneralUtility::getUrl(
            sprintf(
                'https://walls.io/socket.io/?wallId=74741&client=wallsio-frontend&initialCheckins=8&cookieSupport=1&network=&EIO=3&transport=polling&t=%s&b64=%d',
                $this->getFormattedTimestamp(),
                (int)!$this->useBinarySupport
            ),
            1,
            null,
            $errors
        );

        // Explode 2 \r\n to separate header from body
        list($header, $body) = GeneralUtility::trimExplode(chr(13) . chr(10) . chr(13) . chr(10), $result);
        foreach (GeneralUtility::trimExplode(chr(10), $header) as $headerData) {
            $headerParts = GeneralUtility::trimExplode(':', $headerData, true, 2);
            $this->header[$headerParts[0]] = $headerParts[1];
        }

        if (is_string($body)) {
            $data = $this->getDataFromResult($body, 2);
            if (array_key_exists('sid', $data)) {
                return $data['sid'];
            }
        }
        return '';
    }

    protected function getData(string $sessionId): array
    {
        $errors = [];
        $result = GeneralUtility::getUrl(
            sprintf(
                'https://walls.io/socket.io/?wallId=74741&client=wallsio-frontend&initialCheckins=8&cookieSupport=0&network=&EIO=3&transport=polling&t=%s&sid=%s&b64=%d',
                $this->getFormattedTimestamp(),
                $sessionId,
                (int)!$this->useBinarySupport
            ),
            0,
            $this->getHeaders(),
            $errors
        );

        if (is_string($result)) {
            $data = $this->getDataFromResult($result, 3);
            if (!empty($data)) {
                return $data;
            }
        }

        return [
            'sessionId' => $sessionId,
            'errors' => $errors
        ];
    }

    protected function getHeaders()
    {
        return [
            'accept' => '*/*',
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' =>'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7,so;q=0.6',
            'cache-control' => 'no-cache',
            'dnt' => '1',
            'pragma' => 'no-cache',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36',
            'cookie' => $this->getCookie()
        ];
    }

    protected function getCookie()
    {
        $cookies = [];
        if (array_key_exists('Set-Cookie', $this->header)) {
            foreach (GeneralUtility::trimExplode(',', $this->header['Set-Cookie']) as $cookie) {
                $cookieParts = GeneralUtility::trimExplode(';', $cookie);
                $cookies[] = current($cookieParts);
            }
        }
        return implode(';', $cookies);
    }

    protected function getDataFromResult(string $result, int $explodeParts): array
    {
        $parts = GeneralUtility::trimExplode(':', $result, true, $explodeParts);
        $jsonString = preg_replace('/^\d+/', '', $parts[$explodeParts - 1]);
        $data = json_decode($jsonString, true);
        return $data ?: [];
    }

    /**
     * Walls.io works with a special Timestamp format
     * I have adapted the JS part into PHP
     *
     * function r(t) {
     *   var e = "";
     *   do e = s[t % a] + e, t = Math.floor(t / a); while (t > 0);
     *   return e
     * }
     *
     * @return string
     */
    protected function getFormattedTimestamp(): string
    {
        $chars = range(0, 9);
        array_push($chars, ...range('A', 'Z'));
        array_push($chars, ...range('a', 'z'));
        array_push($chars, ...['-', '_']);
        $amountOfChars = count($chars);
        $timestamp = date('U') * 1000; // we need Microseconds
        $formattedTimestamp = '';
        do {
            $formattedTimestamp = $chars[$timestamp % $amountOfChars] . $formattedTimestamp;
            $timestamp = floor($timestamp / $amountOfChars);
        } while ($timestamp > 0);
        return $formattedTimestamp;
    }
}
