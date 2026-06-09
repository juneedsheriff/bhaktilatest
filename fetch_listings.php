<?php
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './include/mystery_helpers.php';
include_once './include/mystery_table_helpers.php';
include_once './include/abroad_listing_helpers.php';
include_once './include/saints_media.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();

// Set the number of records per page

if(isset($_POST['selectedFilters']) || isset($_POST['selectedTempleFilters'])){
    include_once './include/temple_listing_helpers.php';

    $records_per_page = 8;
    $selectedFilters = isset($_POST['selectedFilters']) ? array_filter(explode(',', $_POST['selectedFilters'])) : [];
    $selectedTempleFilters = isset($_POST['selectedTempleFilters']) ? array_map('intval', array_filter(explode(',', $_POST['selectedTempleFilters']))) : [];
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $page = max($page, 1);
    $country = isset($_POST['country']) ? trim($xssClean->clean_input($_POST['country'])) : '';
    $offset = ($page - 1) * $records_per_page;

    $result = temples_india_fetch_god_temple_listings(
        $DatabaseCo->dbLink,
        $selectedFilters,
        $selectedTempleFilters,
        $country !== '' ? $country : 'IN',
        $records_per_page,
        $offset
    );

    $total_records = $result['total'];
    $total_pages = $records_per_page > 0 ? (int) ceil($total_records / $records_per_page) : 0;

    $paginationHtml = '';
    for ($i = 1; $i <= $total_pages; $i++) {
        $activeClass = $i == $page ? 'active' : '';
        $paginationHtml .= "<button class='pagination-button {$activeClass}' onclick='fetchFilteredListings({$i})'>{$i}</button>";
    }

    echo json_encode([
        'listings' => $result['html'],
        'pagination' => $paginationHtml,
        'total' => $total_records,
    ]);
    exit;
}else{

}





if(isset($_POST['selectedFilters_iconic'])){
    $records_per_page = 8;
// Get selected filters and page number
$selectedFilters = isset($_POST['selectedFilters_iconic']) ? explode(',', $_POST['selectedFilters_iconic']) : [];
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$page = max($page, 1);

// Calculate the OFFSET for SQL query
$offset = ($page - 1) * $records_per_page;

// Build the query based on selected filters
$query = "SELECT * FROM iconic";
$params = [];
$bind_types = '';

if (!empty($selectedFilters)) {
    $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
    $query .= " WHERE god_id IN ($placeholders)";
    $bind_types .= str_repeat('s', count($selectedFilters));
    $params = $selectedFilters;
}

// Add pagination limit
$query .= " LIMIT ?, ?";
$bind_types .= 'ii';
$params[] = $offset;
$params[] = $records_per_page;

$stmt = $DatabaseCo->dbLink->prepare($query);
$stmt->bind_param($bind_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for listings
$listingsHtml = '';
while ($Row = $result->fetch_assoc()) {
    $photos = !empty($Row['photos']) ? $Row['photos'] : ($Row['banner'] ?? '');
    $title = htmlspecialchars($Row['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $index_id = (int) ($Row['index_id'] ?? 0);
    $photoSrc = trim((string) $photos) !== ''
        ? 'app/uploads/iconic/' . htmlspecialchars($photos, ENT_QUOTES, 'UTF-8')
        : 'assets/images/default-image.png';

    $cityName = '';
    $stateName = '';
    if (!empty($Row['city'])) {
        $cityId = mysqli_real_escape_string($DatabaseCo->dbLink, $Row['city']);
        $cityResult = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='{$cityId}' LIMIT 1");
        if ($cityResult && ($cityRow = mysqli_fetch_array($cityResult))) {
            $cityName = trim((string) ($cityRow['city_name'] ?? ''));
        }
    }
    if (!empty($Row['state'])) {
        $stateCode = mysqli_real_escape_string($DatabaseCo->dbLink, $Row['state']);
        $countryCode = mysqli_real_escape_string($DatabaseCo->dbLink, $Row['country'] ?? '');
        $stateResult = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='{$stateCode}' AND country_code='{$countryCode}' LIMIT 1");
        if ($stateResult && ($stateRow = mysqli_fetch_array($stateResult))) {
            $stateName = trim((string) ($stateRow['state_name'] ?? ''));
        }
    }

    $placeLine = $cityName;
    if ($stateName !== '') {
        $placeLine .= ($placeLine !== '' ? ', ' : '') . $stateName;
    }
    $placeHtml = $placeLine !== '' ? '<br>' . htmlspecialchars($placeLine, ENT_QUOTES, 'UTF-8') : '';

    $listingsHtml .= "<div class='col-lg-4 col-sm-6 iconic-featured-col'>
                        <div class='product-item1'>
                            <a href='iconic-category-details.php?id={$index_id}'>
                                <div class='iconic-featured-card-image' style=\"background-image:url('{$photoSrc}')\" role='img' aria-label='{$title}'></div>
                                <div class='iconic-featured-card-title'>
                                    <span class='shiny' style='margin: 0'>
                                        <span style='margin: 0'>{$title}{$placeHtml}</span>
                                    </span>
                                </div>
                                <div class='iconic-featured-card-footer'>
                                    <img src='assets/images/backbutton.png' alt=''>
                                </div>
                            </a>
                        </div>
                      </div>";
}

// Fetch total number of records for pagination
$total_query = "SELECT COUNT(*) AS total FROM iconic";
if (!empty($selectedFilters)) {
    $total_query .= " WHERE god_id IN ($placeholders)";
}

$total_stmt = $DatabaseCo->dbLink->prepare($total_query);
$total_stmt->bind_param(str_repeat('s', count($selectedFilters)), ...$selectedFilters);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Generate pagination controls
$paginationHtml = '';
for ($i = 1; $i <= $total_pages; $i++) {
    $activeClass = $i == $page ? 'active' : '';
    $paginationHtml .= "<button class='pagination-button {$activeClass}' onclick='fetchFilteredListings({$i})'>{$i}</button>";
}

// Return JSON response
echo json_encode(['listings' => $listingsHtml, 'pagination' => $paginationHtml]);

$stmt->close();
$total_stmt->close();

}else{

}


if(isset($_POST['selectedFilters_abroad'])){
    $records_per_page = abroad_listing_per_page();
// Get selected filters and page number
$selectedFilters = isset($_POST['selectedFilters_abroad']) ? array_filter(explode(',', $_POST['selectedFilters_abroad'])) : [];
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$page = max($page, 1);

// Calculate the OFFSET for SQL query
$offset = ($page - 1) * $records_per_page;

// Build the query based on selected filters
$query = "SELECT * FROM abroad WHERE status='approved'";
$params = [];
$bind_types = '';

if (!empty($selectedFilters)) {
    $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
    $query .= " AND god_id IN ($placeholders)";
    $bind_types .= str_repeat('s', count($selectedFilters));
    $params = $selectedFilters;
}

// Add pagination limit
$query .= " ORDER BY " . abroad_listing_order_sql() . " LIMIT ?, ?";
$bind_types .= 'ii';
$params[] = $offset;
$params[] = $records_per_page;

$stmt = $DatabaseCo->dbLink->prepare($query);
$stmt->bind_param($bind_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for listings
$listingsHtml = '';
while ($Row = $result->fetch_assoc()) {
    $listingsHtml .= abroad_listing_html($DatabaseCo->dbLink, $Row);
}

// Fetch total number of records for pagination
$total_query = "SELECT COUNT(*) AS total FROM abroad WHERE status='approved'";
if (!empty($selectedFilters)) {
    $total_query .= " AND god_id IN ($placeholders)";
}

$total_stmt = $DatabaseCo->dbLink->prepare($total_query);
if (!empty($selectedFilters)) {
    $total_stmt->bind_param(str_repeat('s', count($selectedFilters)), ...$selectedFilters);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Generate pagination controls
$paginationHtml = '';
for ($i = 1; $i <= $total_pages; $i++) {
    $activeClass = $i == $page ? 'active' : '';
    $paginationHtml .= "<button class='pagination-button {$activeClass}' onclick='fetchFilteredListings({$i})'>{$i}</button>";
}

// Return JSON response
echo json_encode(['listings' => $listingsHtml, 'pagination' => $paginationHtml, 'total' => (int) $total_records]);

$stmt->close();
$total_stmt->close();

}else{

}


// mantras

if(isset($_POST['selectedFilters_mantras'])){
    $records_per_page = 8;
// Get selected filters and page number
$selectedFilters = isset($_POST['selectedFilters_mantras']) ? explode(',', $_POST['selectedFilters_mantras']) : [];
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$page = max($page, 1);

// Calculate the OFFSET for SQL query
$offset = ($page - 1) * $records_per_page;

// Build the query based on selected filters
$query = "SELECT * FROM mantras_subcategory";
$params = [];
$bind_types = '';

if (!empty($selectedFilters)) {
    $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
    $query .= " WHERE index_id IN ($placeholders)";
    $bind_types .= str_repeat('s', count($selectedFilters));
    $params = $selectedFilters;
}

// Add pagination limit
$query .= " LIMIT ?, ?";
$bind_types .= 'ii';
$params[] = $offset;
$params[] = $records_per_page;

$stmt = $DatabaseCo->dbLink->prepare($query);
$stmt->bind_param($bind_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for listings
$listingsHtml = '';
while ($Row = $result->fetch_assoc()) {
    $photos = htmlspecialchars($Row['photos']);
    $title = htmlspecialchars($Row['title']);
    $index_id = (int)$Row['index_id'];

    $listingsHtml .= "<div class='listing'>
                        <a href='mantras-details.php?id={$index_id}' target='_blank'>
                            <img src='app/uploads/gods/{$photos}' alt=''>
                        </a>
                        <div class='listing-details'>
                            <a href='mantras-details.php?id={$index_id}' target='_blank'>
                                <div class='listing-title'>{$title}</div>
                            </a>
                            <div class='listing-rating text-dark'><a href='mantras-details.php?id={$index_id}' target='_blank'>Read more</a></div>
                        </div>
                      </div>";
}

// Fetch total number of records for pagination
$total_query = "SELECT COUNT(*) AS total FROM mantras_subcategory";
if (!empty($selectedFilters)) {
    $total_query .= " WHERE index_id IN ($placeholders)";
}

$total_stmt = $DatabaseCo->dbLink->prepare($total_query);
$total_stmt->bind_param(str_repeat('s', count($selectedFilters)), ...$selectedFilters);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);


// Generate pagination controls
$paginationHtml = '';
for ($i = 1; $i <= $total_pages; $i++) {
    $activeClass = $i == $page ? 'active' : '';
    $paginationHtml .= "<button class='pagination-button {$activeClass}' onclick='fetchFilteredListings({$i})'>{$i}</button>";
}

// Return JSON response
echo json_encode(['listings' => $listingsHtml, 'pagination' => $paginationHtml]);

$stmt->close();
$total_stmt->close();

}else{

}
if (isset($_POST['saints_listing']) || isset($_POST['selectedFilters_saints'])) {
    $selectedFilters = isset($_POST['selectedFilters_saints']) ? array_filter(explode(',', $_POST['selectedFilters_saints'])) : [];
    $saintsPageId = isset($_POST['page_id']) && $_POST['page_id'] !== ''
        ? trim($xssClean->clean_input($_POST['page_id']))
        : saints_poets_page_id($DatabaseCo->dbLink);

    $query = 'SELECT * FROM other_page WHERE page_id = ?';
    $params = [$saintsPageId];
    $bind_types = 's';

    if (!empty($selectedFilters)) {
        $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
        $query .= " AND index_id IN ($placeholders)";
        $bind_types .= str_repeat('s', count($selectedFilters));
        $params = array_merge($params, $selectedFilters);
    }

    $query .= saints_public_listing_status_sql() . ' ORDER BY order_by ASC';

    $stmt = $DatabaseCo->dbLink->prepare($query);
    $stmt->bind_param($bind_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $listingsHtml = '';
    while ($Row = $result->fetch_assoc()) {
        $photos = $Row['photos'];
        $title = htmlspecialchars($Row['title']);
        $index_id = (int) $Row['index_id'];
        $page_id = htmlspecialchars((string) $Row['page_id']);
        $photoSrc = htmlspecialchars(saints_photo_src($photos, $Row['page_id'], $DatabaseCo->dbLink, $Row['title']));

        $listingsHtml .= "<div class='listing'>
                        <a href='saints-details.php?id={$index_id}&page_id={$page_id}' class='d-block' aria-label='{$title}'>
                            <div class='listing-img-bg' style=\"background-image: url('{$photoSrc}');\"></div>
                        </a>
                        <div class='listing-details'>
                            <a href='saints-details.php?id={$index_id}&page_id={$page_id}'>
                                <div class='listing-title'>{$title}</div>
                            </a>
                            <div class='listing-rating text-dark'><a href='saints-details.php?id={$index_id}&page_id={$page_id}'>Read more</a></div>
                        </div>
                      </div>";
    }

    echo json_encode(['listings' => $listingsHtml]);
    $stmt->close();
    exit;
}



if (isset($_POST['selectedFilters_mystery'])) {
    $selectedFilters = array_values(array_filter(array_map('trim', explode(',', $_POST['selectedFilters_mystery'] ?? ''))));
    $allItems = mystery_table_filter_items($DatabaseCo->dbLink, $selectedFilters);

    $listingsHtml = '';
    foreach ($allItems as $Row) {
        $listingsHtml .= mystery_table_listing_html($Row);
    }

    echo json_encode(['listings' => $listingsHtml, 'pagination' => '']);
}


//iconic temple

if(isset($_POST['selectedFilters_iconic_temple'])){
    $records_per_page = 8;
// Get selected filters and page number
$selectedFilters = isset($_POST['selectedFilters_iconic_temple']) ? explode(',', $_POST['selectedFilters_iconic_temple']) : [];
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$page = max($page, 1);

// Calculate the OFFSET for SQL query
$offset = ($page - 1) * $records_per_page;

// Build the query based on selected filters
$query = "SELECT * FROM iconic_temples";
$params = [];
$bind_types = '';

if (!empty($selectedFilters)) {
    $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
    $query .= " WHERE categories_id IN ($placeholders)";
    $bind_types .= str_repeat('s', count($selectedFilters));
    $params = $selectedFilters;
}

// Add pagination limit
$query .= " LIMIT ?, ?";
$bind_types .= 'ii';
$params[] = $offset;
$params[] = $records_per_page;

$stmt = $DatabaseCo->dbLink->prepare($query);
$stmt->bind_param($bind_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for listings
$listingsHtml = '';
while ($Row = $result->fetch_assoc()) {
    $photos = $Row['photos'];
    $title = htmlspecialchars($Row['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $index_id = (int) ($Row['index_id'] ?? 0);
    $photoSrc = trim((string) $photos) !== ''
        ? 'app/uploads/iconic_temple/' . htmlspecialchars($photos, ENT_QUOTES, 'UTF-8')
        : 'assets/images/default-image.png';

    $cityName = '';
    $stateName = '';
    if (!empty($Row['city'])) {
        $cityId = mysqli_real_escape_string($DatabaseCo->dbLink, $Row['city']);
        $cityResult = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='{$cityId}' LIMIT 1");
        if ($cityResult && ($cityRow = mysqli_fetch_array($cityResult))) {
            $cityName = trim((string) ($cityRow['city_name'] ?? ''));
        }
    }
    if (!empty($Row['state'])) {
        $stateCode = mysqli_real_escape_string($DatabaseCo->dbLink, $Row['state']);
        $countryCode = mysqli_real_escape_string($DatabaseCo->dbLink, $Row['country'] ?? '');
        $stateResult = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='{$stateCode}' AND country_code='{$countryCode}' LIMIT 1");
        if ($stateResult && ($stateRow = mysqli_fetch_array($stateResult))) {
            $stateName = trim((string) ($stateRow['state_name'] ?? ''));
        }
    }

    $placeLine = $cityName;
    if ($stateName !== '') {
        $placeLine .= ($placeLine !== '' ? ', ' : '') . $stateName;
    }
    $placeHtml = $placeLine !== '' ? '<br>' . htmlspecialchars($placeLine, ENT_QUOTES, 'UTF-8') : '';

    $listingsHtml .= "<div class='col-lg-4 col-sm-6 iconic-featured-col'>
                        <div class='product-item1'>
                            <a href='iconic-details.php?id={$index_id}'>
                                <div class='iconic-featured-card-image' style=\"background-image:url('{$photoSrc}')\" role='img' aria-label='{$title}'></div>
                                <div class='iconic-featured-card-title'>
                                    <span class='shiny' style='margin: 0'>
                                        <span style='margin: 0'>{$title}{$placeHtml}</span>
                                    </span>
                                </div>
                                <div class='iconic-featured-card-footer'>
                                    <img src='assets/images/backbutton.png' alt=''>
                                </div>
                            </a>
                        </div>
                      </div>";
}

// Fetch total number of records for pagination
$total_query = "SELECT COUNT(*) AS total FROM iconic_temples";
if (!empty($selectedFilters)) {
    $total_query .= " WHERE categories_id IN ($placeholders)";
}

$total_stmt = $DatabaseCo->dbLink->prepare($total_query);
$total_stmt->bind_param(str_repeat('s', count($selectedFilters)), ...$selectedFilters);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Generate pagination controls
$paginationHtml = '';
for ($i = 1; $i <= $total_pages; $i++) {
    $activeClass = $i == $page ? 'active' : '';
    $paginationHtml .= "<button class='pagination-button {$activeClass}' onclick='fetchFilteredListings({$i})'>{$i}</button>";
}

// Return JSON response
echo json_encode(['listings' => $listingsHtml, 'pagination' => $paginationHtml]);

$stmt->close();
$total_stmt->close();

}

// mantras title
if(isset($_POST['selectedFilters_mantras_title'])){
    $records_per_page = 8;
// Get selected filters and page number
$selectedFilters = isset($_POST['selectedFilters_mantras_title']) ? explode(',', $_POST['selectedFilters_mantras_title']) : [];
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$page = max($page, 1);

// Calculate the OFFSET for SQL query
$offset = ($page - 1) * $records_per_page;

// Build the query based on selected filters
$query = "SELECT * FROM mantras_subcategory";
$params = [];
$bind_types = '';

if (!empty($selectedFilters)) {
    $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
    $query .= " WHERE index_id IN ($placeholders)";
    $bind_types .= str_repeat('s', count($selectedFilters));
    $params = $selectedFilters;
}

// Add pagination limit
$query .= " LIMIT ?, ?";
$bind_types .= 'ii';
$params[] = $offset;
$params[] = $records_per_page;

$stmt = $DatabaseCo->dbLink->prepare($query);
$stmt->bind_param($bind_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for listings
$listingsHtml = '';
while ($Row = $result->fetch_assoc()) {
    $photos = htmlspecialchars($Row['photos']);
    $title = htmlspecialchars($Row['title']);
    $index_id = (int)$Row['index_id'];

    $listingsHtml .= "
    <div class='mx-auto mb-3'>
        <div class='card shadow-sm' style='background-color: #fff;'>
            <a href='mantras-details.php?id={$index_id}' target='_blank'>
                <img src='app/uploads/gods/{$photos}' class='card-img-top' alt='{$title}' style='height: 300px; object-fit: cover;'>
            </a>
            <div class='card-body'>
                <a href='mantras-details.php?id={$index_id}' target='_blank' class='text-decoration-none'>
                    <h5 class='card-title text-dark' style='font-size: 20px;'>{$title}</h5>
                </a>
                <p class='card-text text-dark'>
                    <a href='mantras-details.php?id={$index_id}' target='_blank' class=''>Read more</a>
                </p>
            </div>
        </div>
    </div>";
    
    

}

// Fetch total number of records for pagination
$total_query = "SELECT COUNT(*) AS total FROM mantras_subcategory";
if (!empty($selectedFilters)) {
    $total_query .= " WHERE index_id IN ($placeholders)";
}

$total_stmt = $DatabaseCo->dbLink->prepare($total_query);
$total_stmt->bind_param(str_repeat('s', count($selectedFilters)), ...$selectedFilters);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);


// Generate pagination controls
$paginationHtml = '';
for ($i = 1; $i <= $total_pages; $i++) {
    $activeClass = $i == $page ? 'active' : '';
    $paginationHtml .= "<button class='pagination-button {$activeClass}' onclick='fetchFilteredListings({$i})'>{$i}</button>";
}

// Return JSON response
echo json_encode(['listings' => $listingsHtml, 'pagination' => $paginationHtml]);

$stmt->close();
$total_stmt->close();

}else{

}
// mantras_title_2



// Assuming you have already connected to the database using $DatabaseCo->dbLink

if (isset($_POST['title_id'])) {
    // Get selected filters
    $selectedFilters = isset($_POST['title_id']) ? explode(',', $_POST['title_id']) : [];

    // Build the query based on selected filters
    $query = "SELECT * FROM mantras_stotras";
    $params = [];
    $bind_types = '';

    if (!empty($selectedFilters)) {
        $placeholders = implode(',', array_fill(0, count($selectedFilters), '?'));
        $query .= " WHERE mantras_title IN ($placeholders)";
        $bind_types .= str_repeat('s', count($selectedFilters));
        $params = $selectedFilters;
    }

    $stmt = $DatabaseCo->dbLink->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($bind_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate HTML for listings
    $listingsHtml = '';
    while ($Row = $result->fetch_assoc()) {
        $photos = htmlspecialchars($Row['photos']);
        $title = htmlspecialchars($Row['title']);
        $index_id = (int)$Row['index_id'];

        $listingsHtml .= "
        <div class='col-12 col-sm-6 col-md-4 mb-3'>
            <a href='mantras_title_details.php?id={$index_id}' class='text-decoration-none'>
                <div class='border border-4 border-warning rounded p-3 text-center' style='cursor: pointer; font-size: 16px;'>
                    {$title}
                </div>
            </a>
        </div>";
        
    }

    // Return JSON response
    echo json_encode(['listings_1' => $listingsHtml]);

    $stmt->close();
}

?>











