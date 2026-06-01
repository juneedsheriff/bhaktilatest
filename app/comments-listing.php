<?php ob_start();
include_once './includes/header.php';

// Handle approve/deny action
if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
    $id = intval($_REQUEST['id']);
    $action = $_REQUEST['action'];
    if (in_array($action, ['approve', 'deny']) && $id > 0) {
        $is_approved = ($action === 'approve') ? 1 : 0;
        $query = "UPDATE `comments` SET `is_approved` = '$is_approved' WHERE `index_id` = '$id'";
        if ($DatabaseCo->dbLink->query($query)) {
            header('Location: comments-listing.php?msg=' . ($action === 'approve' ? 'approved' : 'denied'));
            exit;
        }
    }
}
?>
<style>
    .icon-container { display: flex; justify-content: center; align-items: center; height: 100%; text-align: center; }
</style>
<!-- Page-Title -->
<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1">Comments</h6>
        </div>
    </div>
</div>

<?php if (isset($_GET['msg']) && in_array($_GET['msg'], ['approved', 'denied'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Comment <?php echo $_GET['msg'] === 'approved' ? 'approved' : 'denied'; ?> successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-10">Name</th>
                            <th class="w-10">Type</th>
                            <th class="w-10">Temple ID</th>
                            <th class="w-30">Comment</th>
                            <th class="w-10">Date</th>
                            <th class="w-5">Status</th>
                            <th class="w-15">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $select = "SELECT * FROM `comments` ORDER BY index_id DESC";
                        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
                        $num_rows = mysqli_num_rows($SQL_STATEMENT);
                        if ($num_rows != 0) {
                            $i = 1;
                            while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
                                $type_labels = ['india' => 'India', 'abroad' => 'Abroad', 'iconic' => 'Iconic', 'icon-cate' => 'Icon Category'];
                                $type_text = isset($type_labels[$Row->type]) ? $type_labels[$Row->type] : $Row->type;
                        ?>
                                <tr>
                                    <td><?php echo $i; $i++; ?></td>
                                    <td><?php echo htmlspecialchars($Row->name ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($type_text); ?></td>
                                    <td><?php echo htmlspecialchars($Row->temple_id ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(mb_substr($Row->comment ?? '', 0, 80)); ?><?php echo mb_strlen($Row->comment ?? '') > 80 ? '...' : ''; ?></td>
                                    <td><?php echo htmlspecialchars($Row->log_date); ?></td>
                                    <td>
                                        <?php if ($Row->is_approved == 1): ?>
                                        <div class="icon-container">
                                            <i class="fa fa-thumbs-up text-success" style="font-size: 20px;"></i>
                                        </div>
                                        <?php else: ?>
                                        <div class="icon-container">
                                            <i class="fa fa-thumbs-down text-danger" style="font-size: 20px;"></i>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user_role === 'Admin'): ?>
                                        <?php if ($Row->is_approved != 1): ?>
                                        <a class="btn btn-sm p-2 btn-success text-white" href="comments-listing.php?action=approve&id=<?php echo $Row->index_id; ?>" title="Approve">
                                            <i class="fa fa-thumbs-up"></i> Approve
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($Row->is_approved != 0): ?>
                                        <a class="btn btn-sm p-2 btn-danger text-white ms-1" href="comments-listing.php?action=deny&id=<?php echo $Row->index_id; ?>" title="Deny">
                                            <i class="fa fa-thumbs-down"></i> Deny
                                        </a>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="8">
                                    <div align="center"><strong>No Comments!</strong></div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include_once './includes/footer.php';
?>
