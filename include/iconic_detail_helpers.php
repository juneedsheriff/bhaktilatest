<?php

require_once __DIR__ . '/abroad_listing_helpers.php';
require_once __DIR__ . '/temple_detail_helpers.php';

function iconic_detail_banner_field($row)
{
    $banner = trim((string) (is_object($row) ? ($row->banner ?? '') : ($row['banner'] ?? '')));
    if ($banner !== '') {
        return $banner;
    }

    return trim((string) (is_object($row) ? ($row->photos ?? '') : ($row['photos'] ?? '')));
}

function iconic_detail_photo_src($row)
{
    $banner = iconic_detail_banner_field($row);
    if ($banner === '') {
        return abroad_listing_photo_fallback();
    }

    return 'app/uploads/iconic_temple/banner/' . $banner;
}

function iconic_detail_photo_attrs($row)
{
    $src = htmlspecialchars(iconic_detail_photo_src($row), ENT_QUOTES, 'UTF-8');
    $fallback = htmlspecialchars(abroad_listing_photo_fallback(), ENT_QUOTES, 'UTF-8');

    return 'src="' . $src . '" class="w-100 banner-h-420 img-fluid" alt="Temple Image" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\';"';
}

function iconic_detail_image_url($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    $path = 'app/uploads/iconic_temple/gallery/' . $filename;

    return file_exists($path) ? $path : $path;
}

function iconic_detail_gallery_images($row)
{
    $images = [];
    $gallery = trim((string) (is_object($row) ? ($row->gallery_image ?? '') : ($row['gallery_image'] ?? '')));
    if ($gallery === '') {
        return $images;
    }

    foreach (explode(',', $gallery) as $image) {
        $image = trim($image);
        if ($image !== '') {
            $images[] = $image;
        }
    }

    return $images;
}

function iconic_detail_speciality_title($row)
{
    $title = trim((string) (is_object($row) ? ($row->speciality_title ?? '') : ($row['speciality_title'] ?? '')));
    if ($title === '') {
        return '';
    }

    $items = array_map('trim', explode(',', $title));
    if (count($items) > 1) {
        $last = array_pop($items);

        return implode(', ', $items) . ' and ' . $last;
    }

    return $items[0];
}

function iconic_detail_map_embed_src($row)
{
    $embed = abroad_detail_map_embed_src($row);
    if ($embed !== '') {
        return $embed;
    }

    $parts = array_filter([
        trim((string) (is_object($row) ? ($row->address ?? '') : ($row['address'] ?? ''))),
        trim((string) (is_object($row) ? ($row->city ?? '') : ($row['city'] ?? ''))),
        trim((string) (is_object($row) ? ($row->state ?? '') : ($row['state'] ?? ''))),
        trim((string) (is_object($row) ? ($row->country ?? '') : ($row['country'] ?? ''))),
    ]);

    if (empty($parts)) {
        return '';
    }

    return 'https://www.google.com/maps?q=' . urlencode(implode(', ', $parts)) . '&output=embed';
}

function iconic_detail_contact_text($row)
{
    $contact = abroad_detail_contact_text($row);
    if ($contact !== '') {
        return $contact . '<p><strong>Wheelchair Access:</strong> Available</p>';
    }

    $address = temple_detail_address_text($row);
    if ($address !== '') {
        return $address . '<p><strong>Wheelchair Access:</strong> Available</p>';
    }

    $raw = trim((string) (is_object($row) ? ($row->address ?? '') : ($row['address'] ?? '')));
    if ($raw === '') {
        return '';
    }

    return nl2br(htmlspecialchars($raw, ENT_QUOTES, 'UTF-8')) . '<p><strong>Wheelchair Access:</strong> Available</p>';
}

function iconic_detail_sections($db, $row)
{
    return [
        ['anchor' => 'About', 'title' => 'Temple Overview', 'content' => abroad_detail_sthalam_text($row)],
        ['anchor' => 'History', 'title' => 'Origin Story', 'content' => temple_detail_puranam_text($row)],
        ['anchor' => 'Deity', 'title' => 'Architecture', 'content' => temple_detail_varnam_text($row)],
        ['anchor' => 'Mystical', 'title' => 'Mystical Beliefs', 'content' => temple_detail_highlights_text($row)],
        ['anchor' => 'Seva', 'title' => 'Festivals & Daily Rituals', 'content' => temple_detail_sevas_text($row)],
        ['anchor' => 'Address', 'title' => 'Address', 'content' => temple_detail_address_text($row)],
        ['anchor' => 'Contact', 'title' => 'Contact', 'content' => iconic_detail_contact_text($row)],
    ];
}

function iconic_detail_section_id(array $section)
{
    return temple_detail_section_id($section);
}

function iconic_detail_location_visible($row)
{
    return iconic_detail_map_embed_src($row) !== '' || temple_detail_address_text($row) !== '';
}

function iconic_detail_build_view($db, $row)
{
    $sections = iconic_detail_sections($db, $row);
    $sectionByAnchor = [];
    foreach ($sections as $section) {
        $sectionByAnchor[$section['anchor']] = $section;
    }

    $galleryImages = iconic_detail_gallery_images($row);
    $hasGallery = count($galleryImages) > 0;
    $mapEmbed = iconic_detail_map_embed_src($row);
    $hasLocation = iconic_detail_location_visible($row);
    $timingsContent = abroad_detail_timings_text($row);
    $specialityText = temple_detail_speciality_text($row);
    $specialityTitle = iconic_detail_speciality_title($row);
    $godName = temple_detail_god_name($db, $row);

    $navSections = [];
    foreach (['About', 'History', 'Deity', 'Mystical', 'Seva'] as $anchor) {
        if (isset($sectionByAnchor[$anchor]) && temple_detail_section_visible($sectionByAnchor[$anchor])) {
            $navSections[] = $sectionByAnchor[$anchor];
        }
    }
    if ($hasGallery) {
        $navSections[] = ['anchor' => 'Photo', 'title' => 'Photos'];
    }

    $addressSection = null;
    $contactSection = null;
    foreach ($sections as $section) {
        if ($section['anchor'] === 'Address' && temple_detail_section_visible($section)) {
            $addressSection = $section;
        }
        if ($section['anchor'] === 'Contact' && temple_detail_section_visible($section)) {
            $contactSection = $section;
        }
    }

    return compact(
        'sections',
        'sectionByAnchor',
        'galleryImages',
        'hasGallery',
        'mapEmbed',
        'hasLocation',
        'timingsContent',
        'specialityText',
        'specialityTitle',
        'godName',
        'navSections',
        'addressSection',
        'contactSection'
    );
}

function iconic_detail_print_content($row)
{
    $safe = function ($data) {
        return !empty($data) ? $data : 'Information not available.';
    };

    $galleryHTML = '';
    if (!empty($row->gallery_image)) {
        $galleryHTML .= "<h2 class='sec-head font-caveat' style='width:fit-content;'>Gallery</h2><div>";
        $imgs = array_filter(explode(',', $row->gallery_image));
        foreach ($imgs as $img) {
            $path = 'app/uploads/iconic_temple/gallery/' . trim($img);
            if (file_exists($path)) {
                $galleryHTML .= "<img src='$path' style='width:300px; margin:10px; border-radius:8px; height:220px;'/>";
            }
        }
        $galleryHTML .= '</div>';
    }

    $banner = iconic_detail_banner_field($row);

    return "
    <link href='assets/css/css.css' rel='stylesheet'>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap');
    .para{font-family:georgia;font-size:18px!important;text-align:justify;word-spacing:-0.5px;}
    .sec-head{font-family:georgia;}
    .caveat-text{font-family:'Caveat',cursive!important;}
    </style>
    <div style='padding:20px;font-family:Arial;'>
        <h1 class='caveat-text font-caveat' style='font-weight:600!important;'>{$safe($row->title)}</h1>
        <img src='app/uploads/iconic_temple/banner/{$banner}' style='width:100%;max-height:300px;object-fit:cover;border-radius:8px;margin-bottom:20px;' />
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Speciality</h2>
        <p class='para'>{$safe($row->speciality)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Temple Overview</h2>
        <p class='para'>{$safe($row->sthalam)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Origin Story</h2>
        <p class='para'>{$safe($row->puranam)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Architecture</h2>
        <p class='para'>{$safe($row->varnam)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Mystical Beliefs</h2>
        <p class='para'>{$safe($row->highlights)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Festivals & Daily Rituals</h2>
        <p class='para'>{$safe($row->sevas)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Timings</h2>
        <p class='para'>{$safe($row->time)}</p>
        <h2 class='sec-head font-caveat' style='width:fit-content;'>Address</h2>
        <p class='para'>{$safe($row->address)}, {$safe($row->city)}, {$safe($row->state)}, {$safe($row->country)}</p>
        {$galleryHTML}
    </div>";
}

function iconic_detail_category_breadcrumb($db, $row)
{
    $categoryId = (int) (is_object($row) ? ($row->categories_id ?? 0) : ($row['categories_id'] ?? 0));
    if ($categoryId <= 0) {
        return null;
    }

    $result = @mysqli_query($db, "SELECT title FROM iconic WHERE index_id='" . $categoryId . "' LIMIT 1");
    if (!$result || !($cat = mysqli_fetch_object($result))) {
        return null;
    }

    return [
        'label' => $cat->title,
        'url' => 'iconic.php?id=' . $categoryId,
    ];
}
