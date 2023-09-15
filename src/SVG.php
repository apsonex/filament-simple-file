<?php

namespace Apsonex\FilamentImage;

use Illuminate\Support\Str;
use enshrined\svgSanitize\Sanitizer;

class SVG
{
    public static function sanitize($svg, ?string $width = '100%', ?string $height = '100%', ?string $class = null): string
    {
        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);
        $sanitizer->minify(true);
        $html = $sanitizer->sanitize($svg);

        if ($width) {
            $html = preg_replace('/width=".*?"/', 'width="' . $width . '"', $html);
        } else {
            $html = preg_replace('/width=".*?"/', '', $html);
        }

        if ($height) {
            $html = preg_replace('/height=".*?"/', 'height="' . $height . '"', $html);
        } else {
            $html = preg_replace('/height=".*?"/', '', $html);
        }

        if ($class) {
            $html = preg_replace('/class=".*?"/', 'class="' . $class . '"', $html);
        } else {
            $html = preg_replace('/class=".*?"/', '', $html);
        }

        // foreach (['class', 'width', 'height'] as $item) {
        //     $html = preg_replace('/' . $item . '=".*?"/', '', $html);
        // }
        return $html;
    }
}
