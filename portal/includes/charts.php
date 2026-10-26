<?php
/**
 * Chart helper — outputs Chart.js config JSON for a lecturer rating summary.
 */
function chart_dataset_from_summary(array $summary): array
{
    $labels = array_values(LECTURER_QUALITIES);
    $values = [];
    foreach (array_keys(LECTURER_QUALITIES) as $key) {
        $values[] = (float) ($summary['averages'][$key] ?? 0);
    }
    return ['labels' => $labels, 'values' => $values, 'count' => $summary['count']];
}
