<?php

if (!function_exists('getEnumCases')) {
    function getEnumCases(array $enumCases): array
    {
        return array_map(fn($item) => $item->name, $enumCases);
    }
}