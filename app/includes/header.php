<?php
//error_reporting(0);

include_once './class/databaseConn.php';

include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();

include_once './class/XssClean.php';

$xssClean = new xssClean();

if (empty($_SESSION['admin_id']) && empty($_SESSION['staff_id'])) {

    echo "<script>window.location='index.html';</script>";

    exit();

}



// Get user role and email



// Check user role and fetch corresponding name

if (!empty($_SESSION['admin_id'])) {

    // Admin logic

    $user_role = "Admin";

    $user_name = "Bhaktikalpa"; // Default name for admin

    $user_email = "bhaktikalpa@gmail.com"; // Replace with admin's default email if needed

} elseif (!empty($_SESSION['staff_id'])) {

    // Staff logic

    $user_role = "Staff";



    // Fetch staff details dynamically

    $staffpriv_query = $DatabaseCo->dbLink->query("SELECT name, username,photos FROM `staff` WHERE index_id='" . $DatabaseCo->dbLink->real_escape_string($_SESSION["staff_id"]) . "'");

    $staffpriv_fetch = mysqli_fetch_assoc($staffpriv_query);



    if ($staffpriv_fetch && isset($staffpriv_fetch['name'], $staffpriv_fetch['username'])) {

        $user_name = $staffpriv_fetch['name']; // Staff name

        $user_email = $staffpriv_fetch['username']; // Staff email (username here)

        $user_photos = $staffpriv_fetch['photos']; // Staff email (username here)

    } else {

        // Fallback for missing staff details

        $user_name = "Unknown Staff";

        $user_email = "No Email";

    }

} else {

    // Unknown user fallback

    $user_role = "Unknown";

    $user_name = "Guest";

    $user_email = "";

}



// // Output for verification

// echo "Role: $user_role<br>";

// echo "Name: $user_name<br>";

// echo "Email: $user_email<br>";

// Current page for sidebar menu active state (mm-active)
$current_page = isset($_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : '';
$iconic_temple_list_tab = '';
if ($current_page === 'iconic_temple_list.php' && !empty($_GET['temple_status']) && in_array((string) $_GET['temple_status'], ['approved', 'pending', 'rejected'], true)) {
    $iconic_temple_list_tab = (string) $_GET['temple_status'];
}
$abroad_temple_list_tab = '';
if ($current_page === 'temple-abroad-listing.php' && !empty($_GET['temple_status']) && in_array((string) $_GET['temple_status'], ['approved', 'pending', 'rejected'], true)) {
    $abroad_temple_list_tab = (string) $_GET['temple_status'];
}
$india_temple_list_tab = '';
if ($current_page === 'temple-listing.php' && !empty($_GET['temple_status']) && in_array((string) $_GET['temple_status'], ['approved', 'pending', 'rejected'], true)) {
    $india_temple_list_tab = (string) $_GET['temple_status'];
}

// Staff menu permissions: load allowed menu keys for staff (comma-separated stored in staff.allowed_menus)
$staff_allowed_menus = [];
if ($user_role === 'Staff' && !empty($_SESSION['staff_id'])) {
    $sid = (int)$_SESSION['staff_id'];
    $col_check = @$DatabaseCo->dbLink->query("SHOW COLUMNS FROM `staff` LIKE 'allowed_menus'");
    if ($col_check && mysqli_num_rows($col_check) > 0) {
        $res = @mysqli_fetch_assoc($DatabaseCo->dbLink->query("SELECT allowed_menus FROM staff WHERE index_id=$sid"));
        $staff_allowed_menus = ($res && isset($res['allowed_menus']) && $res['allowed_menus'] !== '') ? array_map('trim', explode(',', $res['allowed_menus'])) : [];
    }
}
include_once __DIR__ . '/sidebar_menu_config.php';

function staff_can_see_menu($menu_key_or_pages) {
    global $user_role, $staff_allowed_menus, $sidebar_menu_blocks;
    if ($user_role === 'Admin') return true;
    if ($user_role !== 'Staff') return false;
    if (is_array($menu_key_or_pages)) {
        foreach ($menu_key_or_pages as $p) {
            if (in_array($p, $staff_allowed_menus)) return true;
        }
        return false;
    }
    if (in_array($menu_key_or_pages, $staff_allowed_menus)) return true;
    // Block key: also true if any page in this block is allowed (submenu-level access)
    if (isset($sidebar_menu_blocks[$menu_key_or_pages])) {
        $subs = $sidebar_menu_blocks[$menu_key_or_pages][1] ?? [];
        foreach ($subs as $item) {
            $page = is_array($item) ? $item[1] : $item;
            if (in_array($page, $staff_allowed_menus)) return true;
        }
    }
    return false;
}

function staff_can_see_page($page) {
    global $user_role, $staff_allowed_menus, $sidebar_page_to_block;
    if ($user_role === 'Admin') return true;
    if ($user_role !== 'Staff') return false;
    if (in_array($page, $staff_allowed_menus)) return true;
    $block = $sidebar_page_to_block[$page] ?? null;
    return $block && in_array($block, $staff_allowed_menus);
}

/**
 * Return the first allowed page filename for staff (for redirect when accessing disallowed URL).
 */
function staff_first_allowed_page() {
    global $user_role, $sidebar_menu_blocks;
    if ($user_role !== 'Staff') return null;
    foreach ($sidebar_menu_blocks as $block_key => $block_data) {
        if (!staff_can_see_menu($block_key)) continue;
        $subs = $block_data[1] ?? [];
        if (!empty($subs)) {
            $first = $subs[0];
            return is_array($first) ? $first[1] : $first;
        }
    }
    return null;
}

// Enforce menu access: if staff has no permission for this page, redirect to no-access.php
if ($user_role === 'Staff' && !empty($current_page)) {
    $is_controlled_page = isset($sidebar_page_to_block[$current_page]);
    if ($is_controlled_page && !staff_can_see_page($current_page)) {
        $base = rtrim(dirname($_SERVER['PHP_SELF'] ?? ''), '/\\');
        $redirect = ($base ? $base . '/' : '') . 'no-access.php';
        header('Location: ' . $redirect);
        exit;
    }
}

?>

<!doctype html>



<html lang="en">







<head>



    <!-- Required meta tags -->



    <meta charset="utf-8">



    <meta name="viewport" content="width=device-width, initial-scale=1">



    <title>Bhaktikalpa</title>



    <!-- App favicon -->



    <link rel="shortcut icon" href="assets/dist/img/logo.png">



    <!-- Global Styles(used by all pages) -->



    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">



    <link href="assets/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">



    <link href="assets/plugins/fontawesome/css/all.min.css" rel="stylesheet">



    <!-- Third party Styles(used by this page) -->



    <link href="assets/plugins/toastr/toastr.css" rel="stylesheet">



    <link href="assets/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet">



    <!-- App css -->



    <link href="assets/dist/css/app.min.css" rel="stylesheet">



    <!-- Start Your Custom Style Now -->



    <link href="assets/dist/css/style.css" rel="stylesheet">











    <!-- new link -->



    <link href="assets/libs/jqvmap/jqvmap.min.css" rel="stylesheet" />



    <!-- DataTables -->



    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />



    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />







    <link href="assets/libs/air-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css" />







    <!-- Responsive datatable examples -->



    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />



    <!-- Bootstrap Css -->



    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />



    <!-- Icons Css -->



    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />



    <!-- Selectize -->



    <link href="assets/libs/selectize/css/selectize.css" rel="stylesheet" type="text/css" />



    <!-- App Css-->



    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />



    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>







    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">







    <script src="path/to/ckeditor.js"></script>











    <!-- Select2 CSS -->



    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />











</head>



<style>
    /* Prevent sidebar overlay from blocking modals */
    body.modal-open .overlay { display: none !important; pointer-events: none !important; }

    .cke_notification_warning {



    display: none;



    }



</style>



<body class="fixed sidebar-mini">



    <!-- Start preloader -->



    <div id="page-loader" class="page-loader page-loader-active">



        <div class="page-loader-content">



            <div class="page-loader-logo">



                <img src="assets/dist/img/logo.png" alt="Logo">



            </div>



            <div class="page-loader-progress">



                <div class="page-loader-bar"></div>



                <div class="page-loader-percent"></div>



            </div>



        </div>



    </div>



    <!-- End /. preloader -->



    <div class="wrapper">



        <!-- Sidebar  -->



        <nav class="sidebar">



            <div class="sidebar-header">



                <a href="dashboard.php" class="sidebar-brand">



                    <img class="sidebar-brand_icon" src="assets/dist/img/logo.png" alt="">



                    <span class="sidebar-brand_text">Admin<span> Panel </span></span>



                </a>



            </div>



            <!--/.sidebar header-->



            <div class="sidebar-body">



                <nav class="sidebar-nav">



                    <ul class="metismenu">



                        <li class="nav-label">



                            <span class="nav-label_text">Main Menu</span>



                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-three-dots nav-label_ellipsis m-auto" viewbox="0 0 16 16">



                                <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3"></path>



                            </svg>



                        </li>

                        <?php if (staff_can_see_menu('dashboard.php')): ?>
                        <li class="<?php echo ($current_page === 'dashboard.php') ? 'mm-active' : ''; ?>">



                            <a href="dashboard.php">



                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-speedometer" viewbox="0 0 16 16">



                                    <path d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2M3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.389.389 0 0 0-.527-.02L7.547 7.31A.91.91 0 1 0 8.85 8.569l3.434-4.297a.389.389 0 0 0-.029-.518z"></path>



                                    <path fill-rule="evenodd" d="M6.664 15.889A8 8 0 1 1 9.336.11a8 8 0 0 1-2.672 15.78zm-4.665-4.283A11.945 11.945 0 0 1 8 10c2.186 0 4.236.585 6.001 1.606a7 7 0 1 0-12.002 0z"></path>



                                </svg>



                                <span class="ms-2">Dashboard</span>



                            </a>



                        </li>
                        <?php endif; ?>












                        <?php
                        $india_menu_counts = ['approved' => 0, 'pending' => 0, 'rejected' => 0];
                        if (staff_can_see_page('temple-listing.php')) {
                            $indiaCntSql = "SELECT COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(`status`,''))) = 'approved' THEN 1 ELSE 0 END), 0) AS c_app, COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(`status`,''))) = 'unapproved' THEN 1 ELSE 0 END), 0) AS c_pend, COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(`status`,''))) IN ('rejected', 'reject', 'denied', 'disapproved') OR TRIM(COALESCE(`status`,'')) = '' THEN 1 ELSE 0 END), 0) AS c_rej FROM `temples` WHERE index_id != '0'";
                            if ($icr = @mysqli_query($DatabaseCo->dbLink, $indiaCntSql)) {
                                $irow = mysqli_fetch_assoc($icr);
                                if ($irow) {
                                    $india_menu_counts['approved'] = (int) $irow['c_app'];
                                    $india_menu_counts['pending'] = (int) $irow['c_pend'];
                                    $india_menu_counts['rejected'] = (int) $irow['c_rej'];
                                }
                            }
                        }
                        if (staff_can_see_menu('temples_india')): ?>
                        <li class="<?php echo in_array($current_page, ['add-temple.php','temple-listing.php','temple-upload.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa-solid fa-gopuram"></i>



                                <span class="ms-2">Temples in India</span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('add-temple.php')): ?><li class="<?php echo ($current_page === 'add-temple.php') ? 'mm-active' : ''; ?>"><a href="add-temple.php">Add New Temple</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('temple-listing.php')): ?>
                                <li class="<?php echo ($current_page === 'temple-listing.php' && $india_temple_list_tab === 'approved') ? 'mm-active' : ''; ?>"><a href="temple-listing.php?temple_status=approved">Approved Temple(<?php echo (int) $india_menu_counts['approved']; ?>)</a></li>
                                <li class="<?php echo ($current_page === 'temple-listing.php' && $india_temple_list_tab === 'pending') ? 'mm-active' : ''; ?>"><a href="temple-listing.php?temple_status=pending">Approval Pending(<?php echo (int) $india_menu_counts['pending']; ?>)</a></li>
                                <li class="<?php echo ($current_page === 'temple-listing.php' && $india_temple_list_tab === 'rejected') ? 'mm-active' : ''; ?>"><a href="temple-listing.php?temple_status=rejected">Rejected Temple(<?php echo (int) $india_menu_counts['rejected']; ?>)</a></li>
                                <?php endif; ?>



                                <?php if (staff_can_see_page('temple-upload.php')): ?><li class="<?php echo ($current_page === 'temple-upload.php') ? 'mm-active' : ''; ?>"><a href="temple-upload.php">Bulk Upload</a></li><?php endif; ?>



                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php
                        $abroad_menu_counts = ['approved' => 0, 'pending' => 0, 'rejected' => 0];
                        if (staff_can_see_page('temple-abroad-listing.php')) {
                            $abroadCntSql = "SELECT COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(`status`,''))) = 'approved' THEN 1 ELSE 0 END), 0) AS c_app, COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(`status`,''))) = 'unapproved' THEN 1 ELSE 0 END), 0) AS c_pend, COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(`status`,''))) IN ('rejected', 'reject', 'denied', 'disapproved') OR TRIM(COALESCE(`status`,'')) = '' THEN 1 ELSE 0 END), 0) AS c_rej FROM `abroad` WHERE index_id != '0'";
                            if ($acr = @mysqli_query($DatabaseCo->dbLink, $abroadCntSql)) {
                                $acrow = mysqli_fetch_assoc($acr);
                                if ($acrow) {
                                    $abroad_menu_counts['approved'] = (int) $acrow['c_app'];
                                    $abroad_menu_counts['pending'] = (int) $acrow['c_pend'];
                                    $abroad_menu_counts['rejected'] = (int) $acrow['c_rej'];
                                }
                            }
                        }
                        if (staff_can_see_menu('temples_abroad')): ?>
                        <li class="<?php echo in_array($current_page, ['add-abroad-temple.php','temple-abroad-listing.php','abroad-upload.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa-solid fa-gopuram"></i><!-- Icon for Temple -->



                                <span class="ms-2">Temples in Abroad</span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('add-abroad-temple.php')): ?><li class="<?php echo ($current_page === 'add-abroad-temple.php') ? 'mm-active' : ''; ?>"><a href="add-abroad-temple.php">Add New Temple</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('temple-abroad-listing.php')): ?>
                                <li class="<?php echo ($current_page === 'temple-abroad-listing.php' && $abroad_temple_list_tab === 'approved') ? 'mm-active' : ''; ?>"><a href="temple-abroad-listing.php?temple_status=approved">Approved Temple(<?php echo (int) $abroad_menu_counts['approved']; ?>)</a></li>
                                <li class="<?php echo ($current_page === 'temple-abroad-listing.php' && $abroad_temple_list_tab === 'pending') ? 'mm-active' : ''; ?>"><a href="temple-abroad-listing.php?temple_status=pending">Approval Pending(<?php echo (int) $abroad_menu_counts['pending']; ?>)</a></li>
                                <li class="<?php echo ($current_page === 'temple-abroad-listing.php' && $abroad_temple_list_tab === 'rejected') ? 'mm-active' : ''; ?>"><a href="temple-abroad-listing.php?temple_status=rejected">Rejected Temple(<?php echo (int) $abroad_menu_counts['rejected']; ?>)</a></li>
                                <?php endif; ?>



                                <?php if (staff_can_see_page('abroad-upload.php')): ?><li class="<?php echo ($current_page === 'abroad-upload.php') ? 'mm-active' : ''; ?>"><a href="abroad-upload.php">Bulk Upload</a></li><?php endif; ?>



                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php
                        $iconic_pages = ['add-iconic-category.php','temple-iconic-category-listing.php','iconic-category-upload.php','iconic_temple_add.php','iconic_temple_list.php','iconic-temple-upload.php','add-iconic_temple_add.php'];
                        $iconic_temple_menu_counts = ['approved' => 0, 'pending' => 0, 'rejected' => 0];
                        if (staff_can_see_page('iconic_temple_list.php')) {
                            $cntSql = "SELECT COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(status,''))) = 'approved' THEN 1 ELSE 0 END), 0) AS c_app, COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(status,''))) = 'unapproved' THEN 1 ELSE 0 END), 0) AS c_pend, COALESCE(SUM(CASE WHEN LOWER(TRIM(COALESCE(status,''))) IN ('rejected', 'reject', 'denied', 'disapproved') OR TRIM(COALESCE(status,'')) = '' THEN 1 ELSE 0 END), 0) AS c_rej FROM iconic_temples WHERE index_id != '0'";
                            if ($cr = @mysqli_query($DatabaseCo->dbLink, $cntSql)) {
                                $crow = mysqli_fetch_assoc($cr);
                                if ($crow) {
                                    $iconic_temple_menu_counts['approved'] = (int) $crow['c_app'];
                                    $iconic_temple_menu_counts['pending'] = (int) $crow['c_pend'];
                                    $iconic_temple_menu_counts['rejected'] = (int) $crow['c_rej'];
                                }
                            }
                        }
                        $is_iconic_active = in_array($current_page, $iconic_pages);
                        if (staff_can_see_menu('iconic_temples')): ?>
                        <li class="<?php echo $is_iconic_active ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa-solid fa-place-of-worship"></i>



                                <span class="ms-2">Iconic Temples</span>



                            </a>



                            <ul class="nav-second-level">



                                <li class="<?php echo in_array($current_page, ['add-iconic-category.php','temple-iconic-category-listing.php','iconic-category-upload.php']) ? 'mm-active' : ''; ?>">



                                    <a class="has-arrow" href="#" aria-expanded="false">Iconic Homepage</a>



                                    <ul class="nav-third-level">



                                        <?php if (staff_can_see_page('add-iconic-category.php')): ?><li class="<?php echo ($current_page === 'add-iconic-category.php') ? 'mm-active' : ''; ?>"><a href="add-iconic-category.php">Add Iconic Homepage</a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('temple-iconic-category-listing.php')): ?><li class="<?php echo ($current_page === 'temple-iconic-category-listing.php') ? 'mm-active' : ''; ?>"><a href="temple-iconic-category-listing.php">Iconic Homepage Listing</a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('iconic-category-upload.php')): ?><li class="<?php echo ($current_page === 'iconic-category-upload.php') ? 'mm-active' : ''; ?>"><a href="iconic-category-upload.php">Bulk Upload</a></li><?php endif; ?>











                                    </ul>



                                </li>







                                <?php if (staff_can_see_page('iconic_temple_add.php')): ?><li class="<?php echo ($current_page === 'iconic_temple_add.php') ? 'mm-active' : ''; ?>"><a href="iconic_temple_add.php">Add Iconic Temple</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('iconic_temple_list.php')): ?>
                                <li class="<?php echo ($current_page === 'iconic_temple_list.php' && $iconic_temple_list_tab === 'approved') ? 'mm-active' : ''; ?>"><a href="iconic_temple_list.php?temple_status=approved">Approved Temple(<?php echo (int) $iconic_temple_menu_counts['approved']; ?>)</a></li>
                                <li class="<?php echo ($current_page === 'iconic_temple_list.php' && $iconic_temple_list_tab === 'pending') ? 'mm-active' : ''; ?>"><a href="iconic_temple_list.php?temple_status=pending">Approval Pending(<?php echo (int) $iconic_temple_menu_counts['pending']; ?>)</a></li>
                                <li class="<?php echo ($current_page === 'iconic_temple_list.php' && $iconic_temple_list_tab === 'rejected') ? 'mm-active' : ''; ?>"><a href="iconic_temple_list.php?temple_status=rejected">Rejected Temple(<?php echo (int) $iconic_temple_menu_counts['rejected']; ?>)</a></li>
                                <?php endif; ?>



                                <?php if (staff_can_see_page('iconic-temple-upload.php')): ?><li class="<?php echo ($current_page === 'iconic-temple-upload.php') ? 'mm-active' : ''; ?>"><a href="iconic-temple-upload.php">Temples Bulk Upload</a></li><?php endif; ?>



                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php if (staff_can_see_menu('mystery_temples')): ?>
                        <li class="<?php echo ($current_page === 'temple-mystery-listing.php') ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa-solid fa-gopuram"></i>



                                <span class="ms-2">Mystery Temples</span>



                            </a>



                            <ul class="nav-second-level">



                                <!-- <li><a href="add-mystery-temple.php">Add Mystery Temples</a></li> -->



                                <?php if (staff_can_see_page('temple-mystery-listing.php')): ?><li class="<?php echo ($current_page === 'temple-mystery-listing.php') ? 'mm-active' : ''; ?>"><a href="temple-mystery-listing.php"> Mystery Temples Listing</a></li><?php endif; ?>



                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php
                        $mantras_pages = ['mantras_category_add.php','mantras_category.php','add_mantras_subcategory.php','mantras_subcategory.php','mantras_title_add.php','mantras_title.php','mantras_add.php','mantras_list.php','mantras-upload.php'];
                        $is_mantras_active = in_array($current_page, $mantras_pages);
                        if (staff_can_see_menu('mantras')): ?>
                        <li class="<?php echo $is_mantras_active ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa fa-praying-hands" style="font-size: 16px;"></i>



                                <span class="ms-2">Mantras and Stotras</span>



                            </a>



                            <ul class="nav-second-level">



                                <li class="<?php echo in_array($current_page, ['mantras_category_add.php','mantras_category.php']) ? 'mm-active' : ''; ?>">



                                    <a class="has-arrow" href="#" aria-expanded="false">Mantras Category</a>



                                    <ul class="nav-third-level">



                                        <?php if (staff_can_see_page('mantras_category_add.php')): ?><li class="<?php echo ($current_page === 'mantras_category_add.php') ? 'mm-active' : ''; ?>"><a href="mantras_category_add.php"> Mantras Category Add </a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('mantras_category.php')): ?><li class="<?php echo ($current_page === 'mantras_category.php') ? 'mm-active' : ''; ?>"><a href="mantras_category.php"> Mantras Category List</a></li><?php endif; ?>



                                    </ul>



                            </ul>



                            <ul class="nav-second-level">



                                <!-- <li><a href="#">Menu - 2(1)</a></li> -->



                                <li class="<?php echo in_array($current_page, ['add_mantras_subcategory.php','mantras_subcategory.php']) ? 'mm-active' : ''; ?>">



                                    <a class="has-arrow" href="#" aria-expanded="false">Sub Category</a>



                                    <ul class="nav-third-level">



                                        <?php if (staff_can_see_page('add_mantras_subcategory.php')): ?><li class="<?php echo ($current_page === 'add_mantras_subcategory.php') ? 'mm-active' : ''; ?>"><a href="add_mantras_subcategory.php"> Mantras Sub Category Add </a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('mantras_subcategory.php')): ?><li class="<?php echo ($current_page === 'mantras_subcategory.php') ? 'mm-active' : ''; ?>"><a href="mantras_subcategory.php"> Mantras Sub Category List </a></li><?php endif; ?>







                                    </ul>



                            </ul>







                            <!-- <ul class="nav-second-level">



                            



                                <li>



                                    <a class="has-arrow" href="#" aria-expanded="false">Navagraha</a>



                                    <ul class="nav-third-level">



                                    <li><a href="mantras_navagraha_add.php"> Navagraha Add God</a></li>



                                <li><a href="mantras_navagraha_list.php"> Nvagraha God List</a></li>



                                       



                                    </ul>



                               </ul> -->



                            <!-- <ul class="nav-second-level">



                              



                                <li>



                                    <a class="has-arrow" href="#" aria-expanded="false">River God</a>



                                    <ul class="nav-third-level">



                                    <li><a href="mantras_river_god_add.php"> River Add God</a></li>



                                <li><a href="mantras_river_god_list.php"> River God List</a></li>



                                       



                                    </ul>



                               </ul> -->



                            <ul class="nav-second-level">



                                <!-- <li><a href="#">Menu - 2(1)</a></li> -->



                                <li class="<?php echo in_array($current_page, ['mantras_title_add.php','mantras_title.php']) ? 'mm-active' : ''; ?>">



                                    <a class="has-arrow" href="#" aria-expanded="false">Mantras Group</a>



                                    <ul class="nav-third-level">



                                        <?php if (staff_can_see_page('mantras_title_add.php')): ?><li class="<?php echo ($current_page === 'mantras_title_add.php') ? 'mm-active' : ''; ?>"><a href="mantras_title_add.php"> Mantras Group Add</a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('mantras_title.php')): ?><li class="<?php echo ($current_page === 'mantras_title.php') ? 'mm-active' : ''; ?>"><a href="mantras_title.php"> Mantras Group List</a></li><?php endif; ?>







                                    </ul>



                            </ul>







                            <ul class="nav-second-level">



                                <!-- <li><a href="#">Menu - 2(1)</a></li> -->



                                <li class="<?php echo in_array($current_page, ['mantras_add.php','mantras_list.php','mantras-upload.php']) ? 'mm-active' : ''; ?>">



                                    <a class="has-arrow" href="#" aria-expanded="false">Mantras</a>



                                    <ul class="nav-third-level">



                                        <?php if (staff_can_see_page('mantras_add.php')): ?><li class="<?php echo ($current_page === 'mantras_add.php') ? 'mm-active' : ''; ?>"><a href="mantras_add.php"> Mantras Add</a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('mantras_list.php')): ?><li class="<?php echo ($current_page === 'mantras_list.php') ? 'mm-active' : ''; ?>"><a href="mantras_list.php"> Mantras List</a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('mantras-upload.php')): ?><li class="<?php echo ($current_page === 'mantras-upload.php') ? 'mm-active' : ''; ?>"><a href="mantras-upload.php"> Bulk Upload</a></li><?php endif; ?>







                                    </ul>



                            </ul>











                        </li>
                        <?php endif; ?>







                        <?php if (staff_can_see_menu('private_ads')): ?>
                        <li class="<?php echo in_array($current_page, ['private-ads.php','add-new-private-ad.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa-solid fa-gopuram"></i>



                                <span class="ms-2">Private Ads</span>



                            </a>



                            <ul class="nav-second-level">



                                <!-- <li><a href="add-mystery-temple.php">Add Mystery Temples</a></li> -->



                                <?php if (staff_can_see_page('private-ads.php')): ?><li class="<?php echo ($current_page === 'private-ads.php') ? 'mm-active' : ''; ?>"><a href="private-ads.php"> Private Ads List</a></li><?php endif; ?>

                                <?php if (staff_can_see_page('add-new-private-ad.php')): ?><li class="<?php echo ($current_page === 'add-new-private-ad.php') ? 'mm-active' : ''; ?>"><a href="add-new-private-ad.php"> Add New Private Ads</a></li><?php endif; ?>



                            </ul>



                        </li>
                        <?php endif; ?>



                        <?php if (staff_can_see_menu('temple_request')): ?>
                        <li class="<?php echo ($current_page === 'temple-submission-request.php') ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="temple-submission-request.php">



                                <i class="fa-solid fa-gopuram"></i>



                                <span class="ms-2">Temple Request</span>



                            </a>



                           



                        </li>
                        <?php endif; ?>





                        <?php if (staff_can_see_menu('priests')): ?>
                        <li class="<?php echo ($current_page === 'priests-list.php') ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="priests-list.php">



                                <i class="fa-solid fa-gopuram"></i>



                                <span class="ms-2">Priest</span>



                            </a>



                           



                        </li>
                        <?php endif; ?>







                     











                        <?php
                        if (staff_can_see_menu('other_pages')) {

                        $select = "SELECT * FROM `category` WHERE index_id != '0' ORDER BY index_id DESC";



                        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



                        $num_rows = mysqli_num_rows($SQL_STATEMENT);







                        if ($num_rows != 0) {



                            while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {



                        ?>



                                <?php
                                $is_other_active = ($current_page === 'others_page_add.php' || $current_page === 'other_page.php') && isset($_GET['page_id']) && (string)$_GET['page_id'] === (string)$Row->index_id;
                                ?>
                                <li class="<?php echo $is_other_active ? 'mm-active' : ''; ?>">



                                    <a class="has-arrow material-ripple" href="#" aria-expanded="false">



                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-layers">



                                            <path d="M8 1a1 1 0 0 1 .707.293l5.657 5.657a1 1 0 0 1 0 1.414l-5.657 5.657a1 1 0 0 1-1.414 0L1.636 8.364a1 1 0 0 1 0-1.414L7.293 1.293A1 1 0 0 1 8 1zm0 1.414L2.929 7.485 8 12.556l5.071-5.071L8 2.414zM1.636 9.636L8 15.071l6.364-5.435-1.414-1.415L8 13.243l-5.95-5.95L1.636 9.636z" />



                                        </svg>



                                        <span class="ms-2"><?php echo htmlspecialchars($Row->name); ?></span>



                                    </a>



                                    <ul class="nav-second-level">



                                        <?php if (staff_can_see_page('others_page_add.php')): ?><li class="<?php echo ($current_page === 'others_page_add.php' && isset($_GET['page_id']) && (string)$_GET['page_id'] === (string)$Row->index_id) ? 'mm-active' : ''; ?>"><a href="others_page_add.php?page_id=<?php echo htmlspecialchars($Row->index_id); ?>"><?php echo htmlspecialchars($Row->name); ?> Add</a></li><?php endif; ?>



                                        <?php if (staff_can_see_page('other_page.php')): ?><li class="<?php echo ($current_page === 'other_page.php' && isset($_GET['page_id']) && (string)$_GET['page_id'] === (string)$Row->index_id) ? 'mm-active' : ''; ?>"><a href="other_page.php?page_id=<?php echo htmlspecialchars($Row->index_id); ?>"><?php echo htmlspecialchars($Row->name); ?> List</a></li><?php endif; ?>



                                    </ul>



                                </li>



                            <?php



                            }



                        } else {



                            ?>



                            <li>



                                <div align="center"><strong>No Records!</strong></div>



                            </li>



                        <?php



                        }

                        }

                        ?>







                        <?php if ($user_role === 'Admin' || staff_can_see_menu('category_page')): ?>



                            <li class="<?php echo in_array($current_page, ['category_add.php','category.php']) ? 'mm-active' : ''; ?>">



                                <a class="has-arrow material-ripple" href="#">



                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">



                                        <path d="M9.828 4a3 3 0 0 1 2.121.879l.672.672A1 1 0 0 1 14 6.828V12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h3a1 1 0 0 1 .707.293l.672.672A3 3 0 0 1 9.828 4zM4 2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V7.828a1 1 0 0 0-.293-.707l-.672-.672A2 2 0 0 0 9.828 6H4V2z" />



                                    </svg> <span class="ms-2">Category Page</span>



                                </a>



                                <ul class="nav-second-level">



                                    <?php if (staff_can_see_page('category_add.php')): ?><li class="<?php echo ($current_page === 'category_add.php') ? 'mm-active' : ''; ?>"><a href="category_add.php">Add New</a></li><?php endif; ?>



                                    <?php if (staff_can_see_page('category.php')): ?><li class="<?php echo ($current_page === 'category.php') ? 'mm-active' : ''; ?>"><a href="category.php">Page List</a></li><?php endif; ?>















                                </ul>



                            </li>



                        <?php endif; ?>



                        <?php if ($user_role === 'Admin' || staff_can_see_menu('staff_list')): ?>



                            <li class="<?php echo in_array($current_page, ['stafflist.php','bulk_add.php']) ? 'mm-active' : ''; ?>">



                                <a class="has-arrow material-ripple" href="#">



                                    <span class="d-flex align-items-center">



                                        <i class="fa fa-users" style="font-size: 16px;"></i>



                                        <span class="ms-2">Staff</span>



                                    </span>



                                </a>



                                <ul class="nav-second-level">



                                    <?php if (staff_can_see_page('stafflist.php')): ?><li class="<?php echo ($current_page === 'stafflist.php') ? 'mm-active' : ''; ?>"><a href="stafflist.php"> Staffs List</a></li><?php endif; ?>



                                    <?php if (staff_can_see_page('bulk_add.php')): ?><li class="<?php echo ($current_page === 'bulk_add.php') ? 'mm-active' : ''; ?>"><a href="bulk_add.php"> Bulk Add</a></li><?php endif; ?>



                                </ul>



                            </li>



                        <?php endif; ?>


                        <?php if (staff_can_see_menu('comments-listing.php')): ?>
                        <li class="<?php echo ($current_page === 'comments-listing.php') ? 'mm-active' : ''; ?>">
                            <a href="comments-listing.php" class="material-ripple">
                            <i class="fa-solid fa-comments"></i>

                                <span class="ms-2">Comments</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- <li>



                                <a class="has-arrow material-ripple" href="#">



                                    <span class="d-flex align-items-center">



                                        <i class="fa fa-users" style="font-size: 16px;"></i>



                                        <span class="ms-2">Bulk</span>



                                    </span>



                                </a>



                                <ul class="nav-second-level">



                                    <li><a href="bulk_add.php"> Bulk Add</a></li>







                                </ul>



                            </li> -->



                            <?php if ($user_role === 'Admin' || staff_can_see_menu('gallery') || staff_can_see_menu('country') || staff_can_see_menu('state') || staff_can_see_menu('city') || staff_can_see_menu('god')): ?>



                        <li class="nav-label">



                            <span class="nav-label_text">Master</span>







                        </li>



                        <?php if ($user_role === 'Admin' || staff_can_see_menu('gallery')): ?>
                        <li class="<?php echo in_array($current_page, ['gallery_add.php','gallery_list.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <span class="d-flex align-items-center">



                                    <i class="fa fa-images" style="font-size: 16px;"></i>



                                    <span class="ms-2">Gallery</span>



                                </span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('gallery_add.php')): ?><li class="<?php echo ($current_page === 'gallery_add.php') ? 'mm-active' : ''; ?>"><a href="gallery_add.php">Add Gallery</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('gallery_list.php')): ?><li class="<?php echo ($current_page === 'gallery_list.php') ? 'mm-active' : ''; ?>"><a href="gallery_list.php">Galleries Listing</a></li><?php endif; ?>



                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php if ($user_role === 'Admin' || staff_can_see_menu('country')): ?>
                        <li class="<?php echo in_array($current_page, ['country_add.php','country.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <span class="d-flex align-items-center">



                                    <i class="fa fa-globe" style="font-size: 16px;"></i>



                                    <span class="ms-2">Country</span>



                                </span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('country_add.php')): ?><li class="<?php echo ($current_page === 'country_add.php') ? 'mm-active' : ''; ?>"><a href="country_add.php">Add New Country </a></li><?php endif; ?>



                                <?php if (staff_can_see_page('country.php')): ?><li class="<?php echo ($current_page === 'country.php') ? 'mm-active' : ''; ?>"><a href="country.php">Countries Listing</a></li><?php endif; ?>











                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php if ($user_role === 'Admin' || staff_can_see_menu('state')): ?>
                        <li class="<?php echo in_array($current_page, ['state_add.php','state.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <span class="d-flex align-items-center">



                                    <i class="fa fa-map-marked-alt" style="font-size: 16px;"></i> <!-- Icon for State -->



                                    <span class="ms-2">State</span>



                                </span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('state_add.php')): ?><li class="<?php echo ($current_page === 'state_add.php') ? 'mm-active' : ''; ?>"><a href="state_add.php">Add New State</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('state.php')): ?><li class="<?php echo ($current_page === 'state.php') ? 'mm-active' : ''; ?>"><a href="state.php">States Listing</a></li><?php endif; ?>











                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php if ($user_role === 'Admin' || staff_can_see_menu('city')): ?>
                        <li class="<?php echo in_array($current_page, ['city_add.php','city.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <span class="d-flex align-items-center">



                                    <i class="fa fa-city" style="font-size: 16px;"></i>



                                    <span class="ms-2">City</span>



                                </span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('city_add.php')): ?><li class="<?php echo ($current_page === 'city_add.php') ? 'mm-active' : ''; ?>"><a href="city_add.php">Add New City</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('city.php')): ?><li class="<?php echo ($current_page === 'city.php') ? 'mm-active' : ''; ?>"><a href="city.php">Cities Listing</a></li><?php endif; ?>











                            </ul>



                        </li>
                        <?php endif; ?>

                        <?php if ($user_role === 'Admin' || staff_can_see_menu('god')): ?>
                        <li class="<?php echo in_array($current_page, ['god_add.php','god.php']) ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <span class="d-flex align-items-center">



                                    <i class="fa fa-praying-hands" style="font-size: 16px;"></i> <!-- Icon for God -->



                                    <span class="ms-2">God</span>



                                </span>



                            </a>



                            <ul class="nav-second-level">



                                <?php if (staff_can_see_page('god_add.php')): ?><li class="<?php echo ($current_page === 'god_add.php') ? 'mm-active' : ''; ?>"><a href="god_add.php">Add New God</a></li><?php endif; ?>



                                <?php if (staff_can_see_page('god.php')): ?><li class="<?php echo ($current_page === 'god.php') ? 'mm-active' : ''; ?>"><a href="god.php">Gods Listing</a></li><?php endif; ?>











                            </ul>



                        </li>
                        <?php endif; ?>



                        <?php endif; ?>



                        <li class="nav-label">



                            <span class="nav-label_text">Account</span>



                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-three-dots nav-label_ellipsis m-auto" viewbox="0 0 16 16">



                                <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3"></path>



                            </svg>



                        </li>



                        <?php if ($user_role === 'Admin' || staff_can_see_menu('subscribe_list')): ?>



                        <li class="<?php echo ($current_page === 'subscribe_list.php') ? 'mm-active' : ''; ?>">



                            <a class="has-arrow material-ripple" href="#">



                                <i class="fa fa-user-check" style="font-size: 16px;"></i>



                                <span class="ms-2">Subscribers </span>



                            </a>



                            <ul class="nav-second-level">



                                <!-- <li><a href="#0">Add Subscribers </a></li> -->



                                <?php if (staff_can_see_page('subscribe_list.php')): ?><li class="<?php echo ($current_page === 'subscribe_list.php') ? 'mm-active' : ''; ?>"><a href="subscribe_list.php">Subscribers List</a></li><?php endif; ?>



                            </ul>



                        </li>



                        <?php endif; ?>



                        <?php if ($user_role === 'Admin' || staff_can_see_menu('settings')): ?>



                            <li class="<?php echo ($current_page === 'setting-app.php') ? 'mm-active' : ''; ?>">



                                <a class="has-arrow material-ripple" href="#">



                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewbox="0 0 16 16">



                                        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"></path>



                                        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"></path>



                                    </svg>



                                    <span class="ms-2">Settings</span>



                                </a>



                                <ul class="nav-second-level">



                                    <?php if (staff_can_see_page('setting-app.php')): ?><li class="<?php echo ($current_page === 'setting-app.php') ? 'mm-active' : ''; ?>">



                                        <a href="setting-app.php">



                                            Business Setting</a>



                                    </li><?php endif; ?>











                                </ul>



                            </li>



                        <?php endif; ?>



                        <li>



                            <a href="logout.php" title="Logout" class="mb-4">



                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left" viewbox="0 0 16 16">



                                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"></path>



                                    <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"></path>



                                </svg>



                                <span class="ms-2">Logout</span>



                            </a>



                        </li>



                    </ul>



                </nav>







            </div><!-- sidebar-body -->



        </nav>



        <!-- Page Content  -->



        <div class="content-wrapper">



            <div class="main-content">



                <!-- Star navbar -->



                <nav class="navbar-custom-menu navbar navbar-expand-xl m-0 navbar-transfarent">



                    <div class="sidebar-toggle">



                        <div class="sidebar-toggle-icon" id="sidebarCollapse">



                            sidebar toggle<span></span>



                        </div>



                    </div>



                    <!--/.sidebar toggle icon-->



                    <!-- Collapse -->



                    <!-- <div class="collapse navbar-collapse" id="navbarSupportedContent">



                   



                        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar-collapse" aria-expanded="true" aria-label="Toggle navigation"><span></span> <span></span></button>



                



                        <form class="search" action="#" method="get">



                            <div class="search__inner">



                                <input type="text" class="search__text" placeholder="Search (Ctrl+/)">



                                <svg data-sa-action="search-close" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search search__helper" viewbox="0 0 16 16">



                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"></path>



                                </svg>



                                <span class="search-shortcode">(Ctrl+/)</span>



                            </div>



                        </form>



                   







                    </div> -->



                    <div class="navbar-icon d-flex">



                        <ul class="navbar-nav flex-row align-items-center">



                            <li class="nav-item">



                                <a class="nav-link" href="#" id="btnFullscreen">



                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fullscreen" viewbox="0 0 16 16">



                                        <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5M.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5"></path>



                                    </svg>



                                </a>



                            </li>



                            <li class="nav-item">



                                <button class="nav-link dark-button">



                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon" viewbox="0 0 16 16">



                                        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278M4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"></path>



                                    </svg>



                                </button>



                            </li>



                            <li class="nav-item">



                                <button class="nav-link light-button">



                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-brightness-high" viewbox="0 0 16 16">



                                        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"></path>



                                    </svg>



                                </button>



                            </li>



                            <li class="nav-item dropdown user-menu user-menu-custom">



                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">



                                    <div class="profile-element d-flex align-items-center flex-shrink-0 p-0 text-start">



                                        <div class="avatar online">



                                        <?php if ($user_role === 'Admin'): ?>



                                                <img src="assets/dist/img/logo.png" alt="Admin Logo" class="img-fluid rounded-circle">



                                            <?php else: ?>



                                                <img src="./uploads/staff/<?php echo ucfirst($user_photos); ?>" alt="User Image" class="img-fluid rounded-circle">



                                            <?php endif; ?>



                                           



                                        </div>



                                        <div class="profile-text">



                                            <h6 class="m-0 fw-medium fs-8" style="color: black;"><?php echo ucfirst($user_name); ?></h6>



                                            <!-- <span><span class="__cf_email__" data-cfemail="d0b5a8b1bda0bcb590b7bdb1b9bcfeb3bfbd">hjik</span></span> -->



                                        </div>



                                    </div>



                                </a>



                                <div class="dropdown-menu">



                                    <div class="dropdown-header d-sm-none">



                                        <a href="" class="header-arrow"><i class="icon ion-md-arrow-back"></i></a>



                                    </div>



                                    <div class="user-header">



                                        <div class="img-user">



                                            <?php if ($user_role === 'Admin'): ?>



                                                <img src="assets/dist/img/logo.png" alt="Admin Logo" class="img-fluid rounded-circle">



                                            <?php else: ?>



                                                <img src="./uploads/staff/<?php echo ucfirst($user_photos); ?>" alt="User Image" class="img-fluid rounded-circle">



                                            <?php endif; ?>



                                        </div>



                                        <!-- img-user -->



                                        <h6 style="font-weight: 500;"> <?php echo ucfirst($user_role); ?></h6>



                                        <!-- Display 'Admin' or 'Staff' -->



                                        <span class="fs-6"> <a href="mailto:<?php echo htmlspecialchars($user_email); ?>" class="email-link">



                                                <?php echo htmlspecialchars($user_email); ?>



                                            </a></span>



                                    </div><!-- user-header -->



                                    <!-- <a href="#0" class="dropdown-item">



                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person" viewbox="0 0 16 16">



                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"></path>



                                        </svg>



                                        My Profile</a>



                                    <a href="#0" class="dropdown-item">



                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pen" viewbox="0 0 16 16">



                                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z"></path>



                                        </svg>



                                        Edit Profile</a>



                                    



                                    <a href="#0" class="dropdown-item">



                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-gear" viewbox="0 0 16 16">



                                            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"></path>



                                            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"></path>



                                        </svg>



                                        Account Settings</a> -->



                                    <a href="logout.php" title="Logout" class="dropdown-item">



                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-in-right" viewbox="0 0 16 16">



                                            <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"></path>



                                            <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"></path>



                                        </svg>



                                        Sign Out</a>



                                </div>



                                <!--/.dropdown-menu -->



                            </li>



                        </ul>



                    </div>







                </nav>



                <!-- End /. navbar -->







            </div>