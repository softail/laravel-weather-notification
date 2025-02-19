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

if (! function_exists('getAverageValues')) {
    function getAverageValues(array $data): array
    {
        $data = collect($data);

        $averages = $data->reduce(function ($carry, $item) {
            foreach ($item as $key => $value) {
                if (is_array($value)) {
                    $carry[$key] = isset($carry[$key]) ? getAverageValues([$carry[$key], $value]) : $value;
                } else {
                    $carry[$key] = isset($carry[$key]) ? $carry[$key] + $value : $value;
                }
            }

            return $carry;
        }, []);

        return collect($averages)->map(function ($sum) use ($data) {
            if (is_array($sum)) {
                return getAverageValues([$sum]);
            }

            return (float) number_format($sum / $data->count(), 1);
        })->toArray();
    }
}
