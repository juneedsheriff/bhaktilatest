<?php
/**
 * Sidebar menu configuration for role-based visibility.
 * Each key is a menu block; value is [ BlockLabel, submenu_items ].
 * submenu_items = array of [ Label, page_filename ].
 * Used by header.php for Staff visibility and by stafflist.php for menu assignment.
 * Staff can be granted the whole block (block key) or individual submenu pages.
 */
$sidebar_menu_blocks = [
    'dashboard.php'           => ['Dashboard', [['Dashboard', 'dashboard.php']]],
    'comments-listing.php'    => ['Comments', [['Comments', 'comments-listing.php']]],
    'temples_india'           => [
        'Temples in India',
        [
            ['Add New Temple', 'add-temple.php'],
            ['Approved Temple', 'temple-listing.php'],
            ['Approval Pending', 'temple-listing.php'],
            ['Rejected Temple', 'temple-listing.php'],
            ['Bulk Upload', 'temple-upload.php'],
        ],
    ],
    'temples_abroad'          => [
        'Temples in Abroad',
        [
            ['Add New Temple', 'add-abroad-temple.php'],
            ['Approved Temple', 'temple-abroad-listing.php'],
            ['Approval Pending', 'temple-abroad-listing.php'],
            ['Rejected Temple', 'temple-abroad-listing.php'],
            ['Bulk Upload', 'abroad-upload.php'],
        ],
    ],
    'iconic_temples'          => [
        'Iconic Temples',
        [
            ['Add Iconic Homepage', 'add-iconic-category.php'],
            ['Iconic Homepage Listing', 'temple-iconic-category-listing.php'],
            ['Iconic Homepage Bulk Upload', 'iconic-category-upload.php'],
            ['Add Iconic Temple', 'iconic_temple_add.php'],
            ['Iconic Temples Listing', 'iconic_temple_list.php'],
            ['Temples Bulk Upload', 'iconic-temple-upload.php'],
        ],
    ],
    'mystery_temples'         => [
        'Mystery Temples',
        [
            ['Add Mystery Temples', 'add-mystery-temple.php'],
            ['Approved Temple', 'temple-mystery-listing.php'],
            ['Approval Pending', 'temple-mystery-listing.php'],
            ['Rejected Temple', 'temple-mystery-listing.php'],
        ],
    ],
    'mantras'                 => [
        'Mantras and Stotras',
        [
            ['Mantras Category Add', 'mantras_category_add.php'],
            ['Mantras Category List', 'mantras_category.php'],
            ['Mantras Sub Category Add', 'add_mantras_subcategory.php'],
            ['Mantras Sub Category List', 'mantras_subcategory.php'],
            ['Mantras Title Add', 'mantras_title_add.php'],
            ['Mantras Title List', 'mantras_title.php'],
            ['Mantras Add', 'mantras_add.php'],
            ['Mantras List', 'mantras_list.php'],
            ['Mantras Upload', 'mantras-upload.php'],
        ],
    ],
    'private_ads'             => [
        'Private Ads',
        [
            ['Private Ads', 'private-ads.php'],
            ['Add New Private Ad', 'add-new-private-ad.php'],
        ],
    ],
    'temple_request'          => ['Temple Request', [['Temple Submission Request', 'temple-submission-request.php']]],
    'priests'                 => ['Priest', [['Priests List', 'priests-list.php']]],
    'other_pages'             => [
        'Other Pages (Category)',
        [
            ['Others Page Add', 'others_page_add.php'],
            ['Other Page List', 'other_page.php'],
        ],
    ],
    'category_page'           => [
        'Category Page',
        [
            ['Add New', 'category_add.php'],
            ['Page List', 'category.php'],
        ],
    ],
    'staff_list'              => [
        'Staff',
        [
            ['Staffs List', 'stafflist.php'],
            ['Bulk Add', 'bulk_add.php'],
        ],
    ],
    'gallery'                 => [
        'Gallery',
        [
            ['Add Gallery', 'gallery_add.php'],
            ['Galleries Listing', 'gallery_list.php'],
        ],
    ],
    'country'                 => [
        'Country',
        [
            ['Country Add', 'country_add.php'],
            ['Country List', 'country.php'],
        ],
    ],
    'state'                   => [
        'State',
        [
            ['State Add', 'state_add.php'],
            ['State List', 'state.php'],
        ],
    ],
    'city'                    => [
        'City',
        [
            ['City Add', 'city_add.php'],
            ['City List', 'city.php'],
        ],
    ],
    'god'                     => [
        'God',
        [
            ['God Add', 'god_add.php'],
            ['God List', 'god.php'],
        ],
    ],
    'subscribe_list'          => ['Subscribers', [['Subscribers List', 'subscribe_list.php']]],
    'settings'                => ['Settings', [['Business Setting', 'setting-app.php']]],
];

/**
 * Build a flat map: page_filename => block_key (for staff_can_see_page).
 */
$sidebar_page_to_block = [];
foreach ($sidebar_menu_blocks as $block_key => $block_data) {
    $subs = isset($block_data[1]) ? $block_data[1] : [];
    foreach ($subs as $item) {
        $page = is_array($item) ? $item[1] : $item;
        $sidebar_page_to_block[$page] = $block_key;
    }
}
