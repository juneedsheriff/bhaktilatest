<?php

function abroad_listing_order_sql()
{
    return 'order_by DESC, index_id DESC';
}

function abroad_listing_per_page()
{
    return 50;
}

function abroad_listing_photo_fallback()
{
    static $fallback = null;
    if ($fallback !== null) {
        return $fallback;
    }

    $candidates = [
        'assets/images/default-image.png',
        'assets/images/no-image.png',
    ];
    $root = dirname(__DIR__);
    foreach ($candidates as $relative) {
        $full = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (is_file($full)) {
            $fallback = $relative;
            return $fallback;
        }
    }

    $fallback = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23f3ece6' width='100%25' height='100%25'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23c49a6c' font-family='sans-serif' font-size='18'%3ENo Image%3C/text%3E%3C/svg%3E";

    return $fallback;
}

function abroad_listing_photo_filename($photos)
{
    $photos = trim((string) $photos);
    if ($photos === '') {
        return '';
    }

    return basename(str_replace('\\', '/', $photos));
}

function abroad_listing_photo_src($photos)
{
    $filename = abroad_listing_photo_filename($photos);
    if ($filename === '') {
        return abroad_listing_photo_fallback();
    }

    $relative = 'app/uploads/abroad/' . $filename;
    $fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'uploads'
        . DIRECTORY_SEPARATOR . 'abroad' . DIRECTORY_SEPARATOR . $filename;
    if (!is_file($fullPath)) {
        return abroad_listing_photo_fallback();
    }

    return $relative;
}

function abroad_listing_photo_attrs($photos, $alt = '')
{
    $src = htmlspecialchars(abroad_listing_photo_src($photos), ENT_QUOTES, 'UTF-8');
    $fallback = htmlspecialchars(abroad_listing_photo_fallback(), ENT_QUOTES, 'UTF-8');
    $altAttr = htmlspecialchars($alt !== '' ? $alt : 'Temple image', ENT_QUOTES, 'UTF-8');

    return 'src="' . $src . '" alt="' . $altAttr . '" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\';"';
}

function abroad_listing_place_label($db, array $row)
{
    $cityLabel = $row['city'] ?? '';
    $stateLabel = $row['state'] ?? '';

    if (!empty($row['city'])) {
        $cityId = mysqli_real_escape_string($db, $row['city']);
        $cityResult = mysqli_query($db, "SELECT city_name FROM `city` WHERE city_id='{$cityId}'");
        if ($cityResult && ($cityRow = mysqli_fetch_assoc($cityResult)) && !empty($cityRow['city_name'])) {
            $cityLabel = $cityRow['city_name'];
        }
    }

    if (!empty($row['state']) && !empty($row['country'])) {
        $stateCode = mysqli_real_escape_string($db, $row['state']);
        $countryCode = mysqli_real_escape_string($db, $row['country']);
        $stateResult = mysqli_query($db, "SELECT state_name FROM `state` WHERE state_code='{$stateCode}' AND country_code='{$countryCode}'");
        if ($stateResult && ($stateRow = mysqli_fetch_assoc($stateResult)) && !empty($stateRow['state_name'])) {
            $stateLabel = $stateRow['state_name'];
        }
    }

    $placeLabel = trim($cityLabel . ($cityLabel !== '' && $stateLabel !== '' ? ', ' : '') . $stateLabel);
    if ($placeLabel === '' && !empty($row['temple_place'])) {
        $placeLabel = $row['temple_place'];
    }

    return $placeLabel;
}

function abroad_detail_banner_image($row)
{
    $banner = trim((string) (is_array($row) ? ($row['banner'] ?? '') : ($row->banner ?? '')));
    if ($banner !== '') {
        return 'app/uploads/abroad/banner/' . $banner;
    }

    $photos = is_array($row) ? ($row['photos'] ?? '') : ($row->photos ?? '');
    return abroad_listing_photo_src($photos);
}

function abroad_detail_banner_attrs($row)
{
    $banner = trim((string) (is_array($row) ? ($row['banner'] ?? '') : ($row->banner ?? '')));
    $photos = is_array($row) ? ($row['photos'] ?? '') : ($row->photos ?? '');

    if ($banner !== '') {
        $bannerFile = basename(str_replace('\\', '/', $banner));
        $bannerRelative = 'app/uploads/abroad/banner/' . $bannerFile;
        $bannerFull = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'abroad' . DIRECTORY_SEPARATOR . 'banner' . DIRECTORY_SEPARATOR . $bannerFile;
        $src = htmlspecialchars(is_file($bannerFull) ? $bannerRelative : abroad_listing_photo_src($photos), ENT_QUOTES, 'UTF-8');
        $fallback = htmlspecialchars(abroad_listing_photo_src($photos), ENT_QUOTES, 'UTF-8');

        return 'src="' . $src . '" class="w-100 banner-h-420 img-fluid" alt="Temple Image" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\';"';
    }

    $attrs = abroad_listing_photo_attrs($photos, 'Temple Image');
    return str_replace('loading="lazy"', 'class="w-100 banner-h-420 img-fluid" loading="lazy"', $attrs);
}

function abroad_detail_clean_html_field($value)
{
    $html = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
    $html = preg_replace('#https?://www\.google\.com/maps/embed[^"\s<]*(?:"|\s|$)?#i', '', $html);
    $html = preg_replace('/<iframe\b[^>]*>.*?<\/iframe>/is', '', $html);
    $html = preg_replace('/\b(?:width|height|style|loading|referrerpolicy)\s*=\s*"[^"]*"/i', '', $html);
    $html = preg_replace('/\ballowfullscreen(?:\s*=\s*"[^"]*")?/i', '', $html);
    $html = preg_replace('/\bstyle\s*=\s*"[^"]*border:0[^"]*"/i', '', $html);

    return trim($html, " \t\n\r\0\x0B\"'");
}

function abroad_detail_plain_text($value)
{
    $text = abroad_detail_clean_html_field($value);
    $text = strip_tags($text);
    $text = str_replace("\xc2\xa0", ' ', $text);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    return preg_replace('/\s+/u', ' ', trim($text));
}

function abroad_detail_is_placeholder_time($value)
{
    $value = abroad_detail_plain_text($value);
    if ($value === '' || strtoupper($value) === 'NULL') {
        return true;
    }

    if (preg_match('/^0{1,2}:0{2}(?::0{2})?$/', $value)) {
        return true;
    }

    return (bool) preg_match('/^0{1,2}:0{2}(?::0{2})?(?:\s*-\s*0{1,2}:0{2}(?::0{2})?)?$/', $value);
}

function abroad_detail_clean_empty_paragraphs($html)
{
    $html = preg_replace('/<p>(?:\s|&nbsp;|\xc2\xa0)*<\/p>/iu', '', (string) $html);

    return trim($html);
}

function abroad_detail_has_timing_content($value)
{
    $raw = trim((string) $value);
    if ($raw === '' || strtoupper($raw) === 'NULL') {
        return false;
    }

    $plain = abroad_detail_plain_text($raw);
    if ($plain === '' || abroad_detail_is_placeholder_time($plain)) {
        return false;
    }

    if (preg_match('/\b(?:am|pm)\b|\d{1,2}:\d{2}|<table\b/i', $raw . ' ' . $plain)) {
        return true;
    }

    return abroad_detail_has_content($raw);
}

function abroad_detail_has_content($value)
{
    $plain = abroad_detail_plain_text($value);

    if ($plain === '' || strtoupper($plain) === 'NULL') {
        return false;
    }

    if (preg_match('#^https?://www\.google\.com/maps/embed#i', $plain)) {
        return false;
    }

    if (preg_match('/^(?:width|height|style|allowfullscreen|loading|referrerpolicy)\s*=/i', $plain)) {
        return false;
    }

    if (abroad_detail_is_placeholder_time($plain)) {
        return false;
    }

    return true;
}

function abroad_detail_extract_map_embed_from_html($html)
{
    $html = (string) $html;
    if (preg_match('/src=["\']([^"\']*google\.com\/maps\/embed[^"\']*)["\']/i', $html, $matches)) {
        return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
    }
    if (preg_match('#(https?://www\.google\.com/maps/embed[^\s"\'<>]+)#i', $html, $matches)) {
        return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
    }

    return '';
}

function abroad_detail_strip_map_markup($html)
{
    $html = preg_replace('#https?://www\.google\.com/maps/embed[^"\s<]*(?:"|\s|$)?#i', '', (string) $html);
    $html = preg_replace('/<iframe\b[^>]*>.*?<\/iframe>/is', '', $html);

    return abroad_detail_clean_html_field($html);
}

function abroad_sanitize_abroad_content_fields($sthalam, $varnam, $mapEmbedInput = '')
{
    $mapEmbed = trim((string) $mapEmbedInput);

    foreach ([$sthalam, $varnam] as $html) {
        $extracted = abroad_detail_extract_map_embed_from_html($html);
        if ($extracted !== '') {
            $mapEmbed = $extracted;
        }
    }

    $sthalam = abroad_detail_strip_map_markup($sthalam);
    $varnam = abroad_detail_strip_map_markup($varnam);

    if (!abroad_detail_has_content($sthalam)) {
        $sthalam = '';
    }
    if (!abroad_detail_has_content($varnam)) {
        $varnam = '';
    }

    return [
        'sthalam' => $sthalam,
        'varnam' => $varnam,
        'map_embed' => $mapEmbed,
    ];
}

function abroad_detail_map_embed_src($row)
{
    $mapEmbed = trim((string) (is_object($row) ? ($row->map_embed ?? '') : ($row['map_embed'] ?? '')));
    if ($mapEmbed !== '') {
        return $mapEmbed;
    }

    $fields = [
        is_object($row) ? ($row->varnam ?? '') : ($row['varnam'] ?? ''),
        is_object($row) ? ($row->sthalam ?? '') : ($row['sthalam'] ?? ''),
    ];

    foreach ($fields as $html) {
        $extracted = abroad_detail_extract_map_embed_from_html($html);
        if ($extracted !== '') {
            return $extracted;
        }
    }

    return '';
}

function abroad_detail_location_visible($row)
{
    return abroad_detail_map_embed_src($row) !== '' || abroad_detail_has_content(is_object($row) ? ($row->address ?? '') : ($row['address'] ?? ''));
}

function abroad_detail_is_contact_like($html)
{
    $text = abroad_detail_plain_text($html);
    if ($text === '') {
        return false;
    }

    if (preg_match('/^(phone|tel|telephone|website|web|email|e-mail|contact|fax)\s*:/i', $text)) {
        return true;
    }
    if (preg_match('/\b(phone|tel|telephone|website|web|email|e-mail|fax)\s*:/i', $text)) {
        return true;
    }
    if (preg_match('/^\+?\d[\d\s().\-]{6,}\d$/', $text)) {
        return true;
    }
    if (preg_match('#^https?://[^\s]+$#i', $text)) {
        return true;
    }
    if (preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $text)) {
        return true;
    }

    return (bool) preg_match('/(\+?\d[\d\s().\-]{7,}|https?:\/\/|@[^\s@]+\.[^\s@]+)/i', $text);
}

function abroad_detail_extract_contact_from_html($html)
{
    $html = (string) $html;
    $contactParts = [];

    if (preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            if (abroad_detail_is_contact_like($match[1])) {
                $contactParts[] = $match[0];
            }
        }
    }

    if ($contactParts === [] && abroad_detail_is_contact_like($html)) {
        return trim($html);
    }

    return implode('', $contactParts);
}

function abroad_detail_strip_contact_markup($html)
{
    $html = (string) $html;

    if (preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            if (abroad_detail_is_contact_like($match[1])) {
                $html = str_replace($match[0], '', $html);
            }
        }
    }

    return abroad_detail_clean_html_field($html);
}

function abroad_sanitize_abroad_contact_fields($sevas, $contactInput = '')
{
    $contact = trim((string) $contactInput);
    $extracted = abroad_detail_extract_contact_from_html($sevas);

    if ($extracted !== '') {
        if ($contact === '') {
            $contact = $extracted;
        } elseif (strpos($contact, abroad_detail_plain_text($extracted)) === false) {
            $contact = trim($contact . ' ' . $extracted);
        }
    }

    $sevas = abroad_detail_strip_contact_markup($sevas);

    if (!abroad_detail_has_content($sevas)) {
        $sevas = '';
    }
    if (!abroad_detail_has_content($contact)) {
        $contact = '';
    }

    return [
        'sevas' => $sevas,
        'contact' => $contact,
    ];
}

function abroad_detail_contact_text($row)
{
    $speciality = is_object($row) ? ($row->speciality ?? '') : ($row['speciality'] ?? '');
    $sevas = is_object($row) ? ($row->sevas ?? '') : ($row['sevas'] ?? '');
    $extracted = abroad_detail_extract_contact_from_html($sevas);

    if (abroad_detail_has_content($speciality) && abroad_detail_is_contact_like($speciality)) {
        return $speciality;
    }
    if (abroad_detail_has_content($extracted)) {
        return $extracted;
    }
    if (abroad_detail_has_content($speciality) && abroad_detail_is_contact_like($speciality)) {
        return $speciality;
    }

    return '';
}

function abroad_detail_contact_field_value($row)
{
    $speciality = is_object($row) ? ($row->speciality ?? '') : ($row['speciality'] ?? '');
    $sevas = is_object($row) ? ($row->sevas ?? '') : ($row['sevas'] ?? '');
    $extracted = abroad_detail_extract_contact_from_html($sevas);

    if (abroad_detail_has_content($speciality) && abroad_detail_is_contact_like($speciality)) {
        return $speciality;
    }
    if (abroad_detail_has_content($extracted)) {
        return $extracted;
    }

    return $speciality;
}

function abroad_detail_sevas_text($row)
{
    $sevas = is_object($row) ? ($row->sevas ?? '') : ($row['sevas'] ?? '');
    $stripped = abroad_detail_strip_contact_markup($sevas);

    return abroad_detail_has_content($stripped) ? $stripped : '';
}

function abroad_detail_sthalam_text($row)
{
    $sthalam = is_object($row) ? ($row->sthalam ?? '') : ($row['sthalam'] ?? '');
    $cleaned = abroad_detail_clean_html_field($sthalam);

    return abroad_detail_has_content($cleaned) ? $cleaned : '';
}

function abroad_detail_varnam_text($row)
{
    $varnam = is_object($row) ? ($row->varnam ?? '') : ($row['varnam'] ?? '');
    $cleaned = abroad_detail_clean_html_field($varnam);

    return abroad_detail_has_content($cleaned) ? $cleaned : '';
}

function abroad_detail_timings_text($row)
{
    $timings = trim((string) (is_object($row) ? ($row->time ?? '') : ($row['time'] ?? '')));
    if (abroad_detail_has_timing_content($timings)) {
        return abroad_detail_clean_empty_paragraphs($timings);
    }

    $open = trim((string) (is_object($row) ? ($row->open_time ?? '') : ($row['open_time'] ?? '')));
    $close = trim((string) (is_object($row) ? ($row->close_time ?? '') : ($row['close_time'] ?? '')));
    $openValid = !abroad_detail_is_placeholder_time($open);
    $closeValid = !abroad_detail_is_placeholder_time($close);

    if (!$openValid && !$closeValid) {
        return '';
    }

    if ($openValid && $closeValid) {
        return trim($open . ' - ' . $close);
    }

    return $openValid ? $open : $close;
}

function abroad_detail_gallery_images($row)
{
    $images = [];
    $photos = trim((string) (is_object($row) ? ($row->photos ?? '') : ($row['photos'] ?? '')));
    if ($photos !== '') {
        $images[] = $photos;
    }

    $gallery = trim((string) (is_object($row) ? ($row->gallery_image ?? '') : ($row['gallery_image'] ?? '')));
    if ($gallery !== '') {
        foreach (explode(',', $gallery) as $image) {
            $image = trim($image);
            if ($image !== '' && !in_array($image, $images, true)) {
                $images[] = $image;
            }
        }
    }

    return $images;
}

function abroad_detail_image_url($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    $candidates = [
        'app/uploads/abroad/' . $filename,
        'app/uploads/abroad/gallery/' . $filename,
    ];

    foreach ($candidates as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return 'app/uploads/abroad/' . $filename;
}

function abroad_admin_uploads_dir()
{
    return dirname(__DIR__) . '/app/uploads/abroad';
}

function abroad_admin_gallery_disk_path($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    $base = abroad_admin_uploads_dir();
    $candidates = [
        $base . '/gallery/' . $filename,
        $base . '/' . $filename,
    ];

    foreach ($candidates as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return $base . '/' . $filename;
}

function abroad_admin_gallery_url($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    $encoded = rawurlencode($filename);
    $base = abroad_admin_uploads_dir();
    $candidates = [
        ['disk' => $base . '/gallery/' . $filename, 'url' => 'uploads/abroad/gallery/' . $encoded],
        ['disk' => $base . '/' . $filename, 'url' => 'uploads/abroad/' . $encoded],
    ];

    foreach ($candidates as $candidate) {
        if (file_exists($candidate['disk'])) {
            return $candidate['url'];
        }
    }

    return 'uploads/abroad/' . $encoded;
}

function abroad_admin_gallery_images($row)
{
    $gallery = trim((string) (is_object($row) ? ($row->gallery_image ?? '') : ($row['gallery_image'] ?? '')));
    if ($gallery === '') {
        return [];
    }

    $images = [];
    foreach (explode(',', $gallery) as $image) {
        $image = trim($image);
        if ($image !== '' && !in_array($image, $images, true)) {
            $images[] = $image;
        }
    }

    return $images;
}

function abroad_detail_god_name($db, $row)
{
    $godId = (int) (is_object($row) ? ($row->god_id ?? 0) : ($row['god_id'] ?? 0));
    if ($godId <= 0) {
        return '';
    }

    $result = mysqli_query($db, "SELECT god_name FROM god WHERE index_id='{$godId}' LIMIT 1");
    if ($result && ($godRow = mysqli_fetch_assoc($result))) {
        return trim((string) $godRow['god_name']);
    }

    return '';
}

function abroad_detail_sections($db, $row)
{
    return [
        ['anchor' => 'About', 'title' => 'Sthalam', 'content' => abroad_detail_sthalam_text($row)],
        ['anchor' => 'History', 'title' => 'Puranam', 'content' => is_object($row) ? ($row->puranam ?? '') : ($row['puranam'] ?? '')],
        ['anchor' => 'Photo', 'title' => 'Photos', 'gallery' => abroad_detail_gallery_images($row)],
        ['anchor' => 'Deity', 'title' => 'Varnam', 'content' => abroad_detail_varnam_text($row)],
        ['anchor' => 'Mystical', 'title' => 'Highlights', 'content' => is_object($row) ? ($row->highlights ?? '') : ($row['highlights'] ?? '')],
        ['anchor' => 'Seva', 'title' => 'Sevas', 'content' => abroad_detail_sevas_text($row)],
        ['anchor' => 'Address', 'title' => 'Address', 'content' => is_object($row) ? ($row->address ?? '') : ($row['address'] ?? '')],
        ['anchor' => 'Timings', 'title' => 'Timings', 'content' => abroad_detail_timings_text($row)],
        ['anchor' => 'Contact', 'title' => 'Contact', 'content' => abroad_detail_contact_text($row)],
    ];
}

function abroad_detail_section_visible(array $section)
{
    if (!empty($section['gallery'])) {
        return count($section['gallery']) > 0;
    }

    if (($section['anchor'] ?? '') === 'Timings') {
        return abroad_detail_has_timing_content($section['content'] ?? '');
    }

    return abroad_detail_has_content($section['content'] ?? '');
}

function abroad_detail_print_content($row)
{
    $safe = function ($data) {
        return abroad_detail_has_content($data) ? $data : 'Information not available.';
    };

    $title = htmlspecialchars((string) (is_object($row) ? ($row->title ?? '') : ($row['title'] ?? '')), ENT_QUOTES, 'UTF-8');
    $photoSrc = htmlspecialchars(abroad_detail_banner_image($row), ENT_QUOTES, 'UTF-8');
    $sthalam = $safe(abroad_detail_sthalam_text($row));
    $puranam = $safe(is_object($row) ? ($row->puranam ?? '') : ($row['puranam'] ?? ''));
    $varnam = $safe(abroad_detail_varnam_text($row));
    $highlights = $safe(is_object($row) ? ($row->highlights ?? '') : ($row['highlights'] ?? ''));
    $sevas = $safe(abroad_detail_sevas_text($row));
    $timings = $safe(abroad_detail_timings_text($row));
    $contact = $safe(abroad_detail_contact_text($row));
    $address = htmlspecialchars((string) (is_object($row) ? ($row->address ?? '') : ($row['address'] ?? '')), ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars((string) (is_object($row) ? ($row->city ?? '') : ($row['city'] ?? '')), ENT_QUOTES, 'UTF-8');
    $state = htmlspecialchars((string) (is_object($row) ? ($row->state ?? '') : ($row['state'] ?? '')), ENT_QUOTES, 'UTF-8');
    $country = htmlspecialchars((string) (is_object($row) ? ($row->country ?? '') : ($row['country'] ?? '')), ENT_QUOTES, 'UTF-8');

    $galleryHTML = '';
    foreach (abroad_detail_gallery_images($row) as $image) {
        $path = abroad_detail_image_url($image);
        if ($path === '' || !file_exists($path)) {
            continue;
        }
        $galleryHTML .= "<img src='" . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . "' style='width:300px; margin:10px; border-radius:8px; height:220px;'/>";
    }
    if ($galleryHTML !== '') {
        $galleryHTML = "<h2 class='sec-head font-caveat' style='width:fit-content;'>Gallery</h2><div>{$galleryHTML}</div>";
    }

    return "
    <link href='assets/css/css.css' rel='stylesheet'>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap');
    .para{font-family:georgia;font-size:18px !important;text-align:justify;word-spacing:-0.5px;}
    .sec-head{font-family:georgia;}
    .caveat-text{font-family:'Caveat',cursive !important;}
    </style>
    <div style='padding:20px; font-family:Arial;'>
        <h1 class='caveat-text font-caveat' style='font-weight:600 !important;'>{$title}</h1>
        <img src='{$photoSrc}' style='width:100%; max-height:300px; object-fit:cover; border-radius:8px; margin-bottom:20px;' />
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Sthalam</h2>
        <div class='para'>{$sthalam}</div>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Puranam</h2>
        <div class='para'>{$puranam}</div>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Varnam</h2>
        <div class='para'>{$varnam}</div>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Highlights</h2>
        <div class='para'>{$highlights}</div>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Sevas</h2>
        <div class='para'>{$sevas}</div>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Timings</h2>
        <div class='para'>{$timings}</div>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Address</h2>
        <p class='para'>{$address}, {$city}, {$state}, {$country}</p>
        <h2 class='sec-head caveat-text' style='width:fit-content;'>Contact</h2>
        <div class='para'>{$contact}</div>
        {$galleryHTML}
    </div>";
}

function abroad_listing_html($db, array $row)
{
    $title = htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $indexId = (int) ($row['index_id'] ?? 0);
    $placeLabel = htmlspecialchars(abroad_listing_place_label($db, $row), ENT_QUOTES, 'UTF-8');
    $titleLine = $title . ($placeLabel !== '' ? ', ' . $placeLabel : '');
    $photoAttrs = abroad_listing_photo_attrs($row['photos'] ?? '', $row['title'] ?? '');

    return "<div class='listing'>
                <a href='abroad-details.php?id={$indexId}'>
                    <img {$photoAttrs}>
                </a>
                <div class='listing-details'>
                    <a href='abroad-details.php?id={$indexId}'>
                        <div class='listing-title'>{$titleLine}</div>
                    </a>
                </div>
            </div>";
}
