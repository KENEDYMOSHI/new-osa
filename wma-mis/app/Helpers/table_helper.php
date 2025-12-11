<?php
// app/Helpers/custom_helper.php

namespace App\Helpers;

function button($class = 'btn-dark btn-sm', $iconClass = 'minus', $functionName = '', ...$args)
{
    /**
     * Generate an HTML button with a removeRow() onclick event.
     *
     * @param string $class Additional classes for the button.
     * @param string $iconClass Class for the <i> element inside the button.
     * @param string $functionName The name of the JavaScript function to call.
     * @param array $argsArray An array of arguments to pass to the JavaScript function.
     * @return string
     */
    $functionArgs = implode(',', array_map('json_encode', $args));
    $icon = "far fa-$iconClass";
    $button = '<button type="button" class="btn ' . esc($class) . '" onclick="' . esc($functionName) . '(' . $functionArgs . ')">
                     <i class="' . esc($icon) . '"></i>
                   </button>';

    return $button;
}


function downloadButton($class = 'btn-dark btn-sm', $icon = 'download',  $link)
{
    /**
     * Generate an HTML button with a removeRow() onclick event.
     *
     * @param string $class Additional classes for the button.
     * @param string $icon Class for the <i> element inside the button.
     * @param array $link for download link.
     * @return string
     */
    $button = <<<HTML
     <a href="$link" target="_blank" class="btn $class">
        <i class="far fa-$icon"></i>
     </a>
    HTML;
     

    return $button;
}
