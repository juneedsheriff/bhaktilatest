<?php

/**
 * Render a Bootstrap breadcrumb trail.
 *
 * @param array<int, array{label: string, url?: string}> $items
 */
function render_breadcrumbs(array $items): void
{
    if (empty($items)) {
        return;
    }

    echo '<section class="breadcrumb-wrap">';
    echo '<div class="container">';
    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb mb-0">';

    $lastIndex = count($items) - 1;
    foreach ($items as $index => $item) {
        $label = htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
        $url = $item['url'] ?? '';

        if ($index === $lastIndex || $url === '') {
            echo '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
        } else {
            echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a></li>';
        }
    }

    echo '</ol>';
    echo '</nav>';
    echo '</div>';
    echo '</section>';
}
