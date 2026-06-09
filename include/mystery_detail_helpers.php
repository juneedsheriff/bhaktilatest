<?php

require_once __DIR__ . '/mystery_table_helpers.php';

function mystery_detail_has_content($html)
{
    return trim(strip_tags(html_entity_decode((string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) !== '';
}

function mystery_detail_banner_attrs(array $item)
{
    $src = htmlspecialchars((string) ($item['image_url'] ?? 'assets/images/default-image.png'), ENT_QUOTES, 'UTF-8');
    $fallback = htmlspecialchars('assets/images/default-image.png', ENT_QUOTES, 'UTF-8');

    return 'src="' . $src . '" class="w-100 banner-h-420 img-fluid" alt="Mystery Temple Image" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\';"';
}

function mystery_detail_build_view(array $item)
{
    $introText = (string) ($item['v_for'] ?? $item['description'] ?? '');
    $historyText = (string) ($item['history'] ?? $item['small_description'] ?? '');

    $galleryImages = array_values(array_filter($item['gallery_images'] ?? []));
    $hasGallery = count($galleryImages) > 0;
    $hasLocation = trim((string) ($item['location'] ?? '')) !== '';
    $hasIntro = mystery_detail_has_content($introText);
    $hasHistory = mystery_detail_has_content($historyText);

    $specialityTitle = 'Mystery Temples';
    $godName = trim((string) ($item['god_name'] ?? $item['god_label'] ?? ''));
    $placeLabel = trim((string) ($item['location'] ?? ''));
    $specialityText = $hasIntro ? $introText : '';

    $navSections = [];
    if ($hasGallery) {
        $navSections[] = ['anchor' => 'Photo', 'title' => 'Photos'];
    }

    return compact(
        'galleryImages',
        'hasGallery',
        'hasLocation',
        'specialityText',
        'specialityTitle',
        'godName',
        'placeLabel',
        'navSections',
        'historyText',
        'hasHistory',
        'hasIntro'
    );
}

function mystery_detail_gallery_url($image)
{
    $image = trim((string) $image);

    return $image !== '' ? $image : '';
}

function mystery_detail_print_html(array $item, array $view)
{
    $title = htmlspecialchars((string) ($item['name'] ?? $item['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $godName = htmlspecialchars((string) ($view['godName'] ?? ''), ENT_QUOTES, 'UTF-8');
    $placeLabel = htmlspecialchars((string) ($view['placeLabel'] ?? ''), ENT_QUOTES, 'UTF-8');

    ob_start();
    ?>
    <div style="padding:20px;font-family:Arial,sans-serif;">
        <h1 style="font-family:'Caveat',cursive;"><?php echo $title; ?></h1>
        <?php if ($godName !== '' || $placeLabel !== '') : ?>
            <p><strong><?php echo $godName; ?><?php echo ($godName !== '' && $placeLabel !== '') ? ' &mdash; ' : ''; ?><?php echo $placeLabel; ?></strong></p>
        <?php endif; ?>
        <?php if (!empty($view['specialityText'])) : ?>
            <div><?php echo $view['specialityText']; ?></div>
        <?php endif; ?>
        <?php if (!empty($view['hasHistory'])) : ?>
            <div><?php echo $view['historyText']; ?></div>
        <?php endif; ?>
        <?php if (!empty($view['hasGallery'])) : ?>
            <h2>Photos</h2>
            <?php foreach ($view['galleryImages'] as $image) : ?>
                <img src="<?php echo htmlspecialchars((string) $image, ENT_QUOTES, 'UTF-8'); ?>" style="max-width:300px;margin:10px;border-radius:8px;" alt="">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}
