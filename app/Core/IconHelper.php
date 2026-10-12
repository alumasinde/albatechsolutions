<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/** Helper for rendering inline SVG icons from sprite */
final class IconHelper
{
    /**
     * Render an SVG icon from the sprite
     * @param string $name Icon name (without 'icon-' prefix)
     * @param string $class Additional CSS classes
     * @param bool $ariaHidden Whether to hide from screen readers
     * @return string SVG HTML
     */
    public static function render(string $name, string $class = '', bool $ariaHidden = true): string
    {
        $ariaAttr = $ariaHidden ? ' aria-hidden="true"' : '';
        $classAttr = $class !== '' ? ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' : '';
        return '<svg' . $classAttr . $ariaAttr . '><use href="/assets/images/icons.svg#icon-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"></use></svg>';
    }
}
