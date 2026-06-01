<?php

include('./include/header.php');

include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';



$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

// Check if god_id is set to 7 in the URL
error_reporting(0);

?>
<div class="py-3 py-xl-5 bg-gradient">

    <div class="container">
        <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Priest </div>
                    <!-- start button group -->
                    <!-- end /. button group -->
                </div>
<div id="listings-container" class="">
<div class="row">
    <?php
    // Records per page
    $records_per_page = 9;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    $offset = ($page - 1) * $records_per_page;

    // Total record count
    $query = "SELECT COUNT(*) AS total FROM priests";
    $total_result = mysqli_query($DatabaseCo->dbLink, $query);
    $total_records = ($total_result) ? mysqli_fetch_assoc($total_result)['total'] : 0;
    $total_pages = ceil($total_records / $records_per_page);

    // Paginated data
    $select = "SELECT * FROM priests ORDER BY created_at DESC LIMIT $records_per_page OFFSET $offset";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
    ?>
            <div class="col-md-3">
                <div class="priest-list row2 g-3 align-items-center">
                    <div class="figure text-center">
                        <?php if($Row['photo']){?>
                        <a href="priest-details.php?id=<?= htmlspecialchars($Row['id']) ?>"><img src="app/<?= htmlspecialchars($Row['photo']) ?>" alt="<?= htmlspecialchars($Row['full_name']) ?>" class="img-fluid rounded"></a>
                        <?php } ?>
                    </div>
                    <div class="info">
                        <h4><?= htmlspecialchars($Row['full_name']) ?></h4>
                       
                        <p><strong>DOB:</strong> <?= htmlspecialchars($Row['date_of_birth']) ?> | <strong>Gender:</strong> <?= htmlspecialchars($Row['gender']) ?></p>
                        <p><strong>Experience:</strong> <?= htmlspecialchars($Row['experience_years']) ?> years</p>
                        <p><strong>Specialization:</strong> <?= htmlspecialchars($Row['specialization']) ?></p>
                        <p><strong>Available:</strong> <?= $Row['available'] ? 'Yes' : 'No' ?></p>
                       
                        <a href="priest-details.php?id=<?= htmlspecialchars($Row['id']) ?>" class="btn btn-sm btn-primary mt-2">View Profile</a>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
        echo "<p class='text-center'>No priests found.</p>";
    }
    ?>
</div>
</div>
<!-- Pagination -->
<?php if ($total_pages > 1): ?>
    <nav class="pagination-nav mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
            </div>
            </div>


<style>
    .figure img{
        width:100%;
        margin-bottom:15px;
    }
    .priest-list{
        background:#fff;
        margin-bottom:30px;
    }
    .priest-list .info{
        padding:15px;
    }
    .priest-list .info p{
        display:block;
        font-size:14px;
    }
</style>
<?php include_once './include/footer.php' ?>

