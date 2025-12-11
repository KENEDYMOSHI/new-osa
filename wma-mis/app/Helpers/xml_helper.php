<?php

function formatXml($xml)
{
    $dom = new DOMDocument;
    $dom->preserveWhiteSpace = FALSE;
    $dom->loadXML($xml);
    $dom->formatOutput = TRUE;
    return (string)$dom->saveXML($dom->documentElement);
}
