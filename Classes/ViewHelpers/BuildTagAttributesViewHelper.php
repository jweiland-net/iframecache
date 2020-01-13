<?php
declare(strict_types = 1);
namespace JWeiland\Iframecache\ViewHelpers;

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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Build HTML TAG Attributes
 */
class BuildTagAttributesViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the render-result of this ViewHelper
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize all arguments.
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'attributes',
            'array',
            'The Attributes to build',
            false,
            []
        );
    }

    /**
     * Implements a ViewHelper to generate HTML Tag Attributes
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $attributes = [];
        foreach ($renderChildrenClosure() as $attribute => $value) {
            if ($value === '' || $attribute === $value) {
                $attributes[] = $attribute;
            } else {
                $attributes[] = sprintf(
                    '%s="%s"',
                    $attribute,
                    $value
                );
            }
        }
        return implode(' ', $attributes);
    }
}
