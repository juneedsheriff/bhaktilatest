<?php

/**
 * Append a language query parameter to the current URL.
 */
function breadcrumb_lang_url(string $lang): string
{
    $current_url = '//' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
    $current_url = preg_replace('/(\?|&)lang=[a-z]{2}/', '', $current_url);
    $current_url = rtrim($current_url, '?&');

    return $current_url . (strpos($current_url, '?') === false ? '?' : '&') . 'lang=' . $lang;
}

/**
 * Render language switcher links.
 */
function render_language_links(): void
{
    ?>
    <div class="sn_language_links">
        <p class="notranslate mb-0">
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('en'), ENT_QUOTES, 'UTF-8'); ?>">A</a> /
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('hi'), ENT_QUOTES, 'UTF-8'); ?>">अ</a> /
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('kn'), ENT_QUOTES, 'UTF-8'); ?>">ಕ</a> /
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('ml'), ENT_QUOTES, 'UTF-8'); ?>">അ</a> /
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('bn'), ENT_QUOTES, 'UTF-8'); ?>">অ</a> /
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('ta'), ENT_QUOTES, 'UTF-8'); ?>">அ</a> /
            <a href="<?php echo htmlspecialchars(breadcrumb_lang_url('te'), ENT_QUOTES, 'UTF-8'); ?>">అ</a>
        </p>
    </div>
    <?php
}

/**
 * Render breadcrumb navigation list only.
 *
 * @param array<int, array{label: string, url?: string}> $items
 */
function render_breadcrumb_nav(array $items): void
{
    if (empty($items)) {
        return;
    }

    echo '<nav aria-label="breadcrumb" class="breadcrumb-toolbar__nav">';
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
}

/**
 * Toolbar with language links only (right side).
 */
function render_language_toolbar(): void
{
    echo '<section class="breadcrumb-toolbar breadcrumb-toolbar--language-only breadcrumb-sec">';
    echo '<div class="container">';
    echo '<div class="breadcrumb-toolbar__inner">';
    echo '<div class="breadcrumb-toolbar__nav"></div>';
    render_language_links();
    echo '</div></div></section>';
}

/**
 * Render breadcrumbs (left) and language links (right) on one line.
 *
 * @param array<int, array{label: string, url?: string}> $items
 */
function render_breadcrumbs(array $items): void
{
    echo '<style>.breadcrumb-toolbar--language-only{display:none!important}</style>';
    echo '<section class="breadcrumb-toolbar breadcrumb-wrap breadcrumb-sec">';
    echo '<div class="container">';
    echo '<div class="breadcrumb-toolbar__inner">';
    render_breadcrumb_nav($items);
    render_language_links();
    echo '</div></div></section>';
}
