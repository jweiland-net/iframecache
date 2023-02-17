<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/iframecache.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Iframecache\ViewHelpers;

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

    public function initializeArguments(): void
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
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
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
