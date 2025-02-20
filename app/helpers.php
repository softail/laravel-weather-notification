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
                    // Recursively calculate averages for nested arrays
                    $carry[$key] = isset($carry[$key])
                        ? getAverageValues([$carry[$key], $value])
                        : $value;
                } elseif ($value !== null) {
                    // Sum only non-null scalar values
                    $carry[$key] = isset($carry[$key]) ? $carry[$key] + $value : $value;
                }
            }

            return $carry;
        }, []);

        return collect($averages)->map(function ($sum, $key) use ($data) {
            if (is_array($sum)) {
                // Process nested averages but exclude `null` values
                $filtered = $data->pluck($key)->filter(fn ($v) => is_array($v))->toArray();

                return getAverageValues($filtered);
            }

            // Filter out `null` values when calculating the count for scalar values
            $nonNullValues = $data->pluck($key)->filter(fn ($v) => $v !== null);

            return (float) number_format($sum / $nonNullValues->count(), 1);
        })->toArray();
    }
}
