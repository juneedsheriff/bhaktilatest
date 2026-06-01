<?php
include_once './includes/header.php';
?>
<div class="body-content">
    <div class="container-xxl">
        <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
            <div class="col-lg-6 col-md-8 text-center">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="fa-solid fa-lock fa-4x text-secondary opacity-75"></i>
                        </div>
                        <h2 class="h4 mb-3">No Access</h2>
                        <p class="text-muted mb-4">
                            You do not have permission to view this page.
                        </p>
                        <p class="text-muted mb-4">
                            Please contact the administrator to request access.
                        </p>
                        <?php
                        $base = rtrim(dirname($_SERVER['PHP_SELF'] ?? ''), '/\\');
                        $first = function_exists('staff_first_allowed_page') ? staff_first_allowed_page() : null;
                        if ($first):
                            $back_url = $base ? $base . '/' . $first : $first;
                        ?>
                        <a href="<?php echo htmlspecialchars($back_url); ?>" class="btn btn-primary">Go to my dashboard</a>
                        <?php else: ?>
                        <a href="<?php echo $base ? $base . '/' : ''; ?>index.html" class="btn btn-outline-secondary">Return to login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once './includes/footer.php';
?>
