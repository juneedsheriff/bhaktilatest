<?php

require_once __DIR__ . '/abroad_listing_helpers.php';

function temple_detail_image_field_names()
{
    return ['image1', 'image2', 'image3', 'image4', 'image5', 'image6', 'image7', 'image8', 'image9', 'image10'];
}

function temple_detail_normalize_image_src($img)
{
    $img = trim(str_replace('\\', '/', (string) $img));
    if ($img === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $img)) {
        return $img;
    }

    $img = ltrim($img, '/');
    if (strpos($img, 'app/uploads/') === 0) {
        return $img;
    }
    if (strpos($img, 'uploads/') === 0) {
        return 'app/' . $img;
    }

    return 'app/uploads/temple/' . basename($img);
}

function temple_detail_photo_src($rowOrPhotos)
{
    if (is_object($rowOrPhotos) || is_array($rowOrPhotos)) {
        $row = (array) $rowOrPhotos;
        foreach (temple_detail_image_field_names() as $field) {
            $img = trim((string) ($row[$field] ?? ''));
            if ($img !== '') {
                return temple_detail_normalize_image_src($img);
            }
        }
        $photos = trim((string) ($row['photos'] ?? ''));
    } else {
        $photos = trim((string) $rowOrPhotos);
    }

    if ($photos === '') {
        return abroad_listing_photo_fallback();
    }

    return temple_detail_normalize_image_src($photos);
}

function temple_detail_photo_attrs($photos)
{
    $src = htmlspecialchars(temple_detail_photo_src($photos), ENT_QUOTES, 'UTF-8');
    $fallback = htmlspecialchars(abroad_listing_photo_fallback(), ENT_QUOTES, 'UTF-8');

    return 'src="' . $src . '" class="w-100 banner-h-420 img-fluid" alt="Temple Image" loading="lazy" onerror="this.onerror=null;this.src=\'' . $fallback . '\';"';
}

function temple_detail_map_embed_src($row)
{
    $videoUrl = trim((string) (is_object($row) ? ($row->video_url ?? '') : ($row['video_url'] ?? '')));
    if ($videoUrl !== '' && temple_detail_is_map_url($videoUrl)) {
        return $videoUrl;
    }

    return abroad_detail_map_embed_src($row);
}

function temple_detail_is_map_url($url)
{
    return (bool) preg_match('#(google\.com/maps|maps\.app\.goo\.gl|maps/embed)#i', (string) $url);
}

function temple_detail_is_video_url($url)
{
    return (bool) preg_match('#(youtube\.com|youtu\.be)#i', (string) $url);
}

function temple_detail_live_embed_url($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    if (stripos($url, 'embed/') !== false) {
        return $url;
    }

    $url = preg_replace('#youtube\.com/watch\?v=#i', 'youtube.com/embed/', $url);
    $url = preg_replace('#youtu\.be/#i', 'youtube.com/embed/', $url);

    if (strpos($url, '?') === false) {
        $url .= '?rel=0&modestbranding=1&showinfo=0&autohide=1&fs=1';
    }

    return $url;
}

function temple_detail_live_streams($db, $templeId)
{
    $templeId = (int) $templeId;
    if ($templeId <= 0) {
        return [];
    }

    $streams = [];
    $sql = "SELECT temple_name, god_name, live_url, stream_start, stream_end, status
            FROM live_darshan
            WHERE temple_id = '" . $templeId . "'
            ORDER BY stream_start ASC";
    $result = @mysqli_query($db, $sql);
    if (!$result) {
        return [];
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $embedUrl = temple_detail_live_embed_url($row['live_url'] ?? '');
        if ($embedUrl === '') {
            continue;
        }

        $streams[] = [
            'temple_name' => trim((string) ($row['temple_name'] ?? '')),
            'god_name' => trim((string) ($row['god_name'] ?? '')),
            'youtube_url' => $embedUrl,
            'start_time' => substr((string) ($row['stream_start'] ?? ''), 0, 5),
            'end_time' => substr((string) ($row['stream_end'] ?? ''), 0, 5),
            'timezone' => 'Asia/Kolkata',
            'status' => trim((string) ($row['status'] ?? 'Offline')),
        ];
    }

    return $streams;
}

function temple_detail_image_url($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    $candidates = [
        'app/uploads/temple/' . $filename,
        'app/uploads/Temple_gallery/' . $filename,
    ];

    foreach ($candidates as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return 'app/uploads/temple/' . $filename;
}

function temple_detail_gallery_images($row)
{
    $images = [];
    $rowArr = is_object($row) ? (array) $row : $row;

    foreach (temple_detail_image_field_names() as $field) {
        $img = trim((string) ($rowArr[$field] ?? ''));
        if ($img !== '' && !in_array($img, $images, true)) {
            $images[] = $img;
        }
    }

    $photos = trim((string) ($rowArr['photos'] ?? ''));
    if ($photos !== '' && !in_array($photos, $images, true)) {
        $images[] = $photos;
    }

    $gallery = trim((string) ($rowArr['gallery_image'] ?? ''));
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

function temple_detail_place_label($db, $row)
{
    return abroad_listing_place_label($db, is_object($row) ? (array) $row : $row);
}

function temple_detail_god_name($db, $row)
{
    return abroad_detail_god_name($db, $row);
}

function temple_detail_speciality_text($row)
{
    $speciality = is_object($row) ? ($row->speciality ?? '') : ($row['speciality'] ?? '');
    if (!abroad_detail_has_content($speciality) || abroad_detail_is_contact_like($speciality)) {
        return '';
    }

    return abroad_detail_clean_empty_paragraphs(abroad_detail_clean_html_field($speciality));
}

function temple_detail_puranam_text($row)
{
    $puranam = is_object($row) ? ($row->puranam ?? '') : ($row['puranam'] ?? '');

    return abroad_detail_has_content($puranam) ? abroad_detail_clean_empty_paragraphs(abroad_detail_clean_html_field($puranam)) : '';
}

function temple_detail_varnam_text($row)
{
    $varnam = abroad_detail_varnam_text($row);
    if ($varnam !== '') {
        return $varnam;
    }

    $highlights = is_object($row) ? ($row->highlights ?? '') : ($row['highlights'] ?? '');
    $puranam = is_object($row) ? ($row->puranam ?? '') : ($row['puranam'] ?? '');
    if (abroad_detail_has_content($highlights) && abroad_detail_has_content($puranam)) {
        return abroad_detail_clean_empty_paragraphs(abroad_detail_clean_html_field($highlights));
    }

    return '';
}

function temple_detail_highlights_text($row)
{
    $highlights = is_object($row) ? ($row->highlights ?? '') : ($row['highlights'] ?? '');
    if (!abroad_detail_has_content($highlights)) {
        return '';
    }

    $varnamDisplay = temple_detail_varnam_text($row);
    if ($varnamDisplay !== '' && abroad_detail_plain_text($highlights) === abroad_detail_plain_text($varnamDisplay)) {
        return '';
    }

    return abroad_detail_clean_empty_paragraphs(abroad_detail_clean_html_field($highlights));
}

function temple_detail_sevas_text($row)
{
    return abroad_detail_sevas_text($row);
}

function temple_detail_address_text($row)
{
    $address = is_object($row) ? ($row->address ?? '') : ($row['address'] ?? '');

    return abroad_detail_has_content($address) ? abroad_detail_clean_empty_paragraphs(abroad_detail_clean_html_field($address)) : '';
}

function temple_detail_sections($db, $row)
{
    return [
        ['anchor' => 'About', 'title' => 'Sthalam', 'content' => abroad_detail_sthalam_text($row)],
        ['anchor' => 'History', 'title' => 'Puranam', 'content' => temple_detail_puranam_text($row)],
        ['anchor' => 'Deity', 'title' => 'Varnam', 'content' => temple_detail_varnam_text($row)],
        ['anchor' => 'Mystical', 'title' => 'Highlights', 'content' => temple_detail_highlights_text($row)],
        ['anchor' => 'Seva', 'title' => 'Sevas', 'content' => temple_detail_sevas_text($row)],
        ['anchor' => 'Address', 'title' => 'Address', 'content' => temple_detail_address_text($row)],
        ['anchor' => 'Timings', 'title' => 'Timings', 'content' => abroad_detail_timings_text($row)],
        ['anchor' => 'Contact', 'title' => 'Contact', 'content' => abroad_detail_contact_text($row)],
    ];
}

function temple_detail_section_visible(array $section)
{
    return abroad_detail_section_visible($section);
}

function temple_detail_section_id(array $section)
{
    $map = [
        'About' => 'Sthalam',
        'History' => 'Puranam',
        'Deity' => 'Varnam',
        'Mystical' => 'Highlights',
        'Seva' => 'Sevas',
        'Photo' => 'Photo',
        'Address' => 'Address',
        'Contact' => 'Contact',
        'Location' => 'Location',
        'Timings' => 'Timings',
    ];

    return $map[$section['anchor'] ?? ''] ?? ($section['anchor'] ?? 'Section');
}

function temple_detail_location_visible($row)
{
    return temple_detail_map_embed_src($row) !== '' || temple_detail_address_text($row) !== '';
}

function temple_detail_build_view($db, $row)
{
    $sections = temple_detail_sections($db, $row);
    $sectionByAnchor = [];
    foreach ($sections as $section) {
        $sectionByAnchor[$section['anchor']] = $section;
    }

    $galleryImages = temple_detail_gallery_images($row);
    $hasGallery = count($galleryImages) > 0;
    $mapEmbed = temple_detail_map_embed_src($row);
    $hasLocation = temple_detail_location_visible($row);
    $timingsContent = abroad_detail_timings_text($row);
    $specialityText = temple_detail_speciality_text($row);
    $specialityTitle = trim((string) (is_object($row) ? ($row->speciality_title ?? '') : ($row['speciality_title'] ?? '')));
    $godName = temple_detail_god_name($db, $row);
    $placeLabel = temple_detail_place_label($db, $row);

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
        'placeLabel',
        'navSections',
        'addressSection',
        'contactSection'
    );
}
