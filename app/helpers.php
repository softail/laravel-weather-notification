<?php

if (! function_exists('getEnumCases')) {
    function getEnumCases(array $enumCases): array
    {
        return array_map(fn ($item) => $item->name, $enumCases);
    }
}

if (! function_exists('getEnumCase')) {
    function getEnumCase($enum, string $case): string
    {
        return constant($enum."::$case")->value;
    }
}
