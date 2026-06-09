<?php ob_start();
$valid_temple_tabs = ['approved', 'pending', 'rejected'];
$list_temple_status = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], $valid_temple_tabs, true)) ? (string) $_REQUEST['temple_status'] : '';
$mystery_rejected_sql = "( LOWER(TRIM(COALESCE(m.`status`, ''))) IN ('rejected', 'reject', 'denied', 'disapproved') OR TRIM(COALESCE(m.`status`, '')) = '' )";
$status_sql_fragment = '';
if ($list_temple_status === 'approved') {
    $status_sql_fragment = " AND LOWER(TRIM(COALESCE(m.`status`,''))) = 'approved' ";
} elseif ($list_temple_status === 'pending') {
    $status_sql_fragment = " AND LOWER(TRIM(COALESCE(m.`status`,''))) = 'unapproved' ";
} elseif ($list_temple_status === 'rejected') {
    $status_sql_fragment = ' AND ' . $mystery_rejected_sql . ' ';
}
include_once './includes/header.php';
error_reporting(1);

if (!empty($_REQUEST['del_t'])) {
    $del_id = (int) $_REQUEST['del_t'];
    $query = "DELETE FROM `mystery` WHERE `mystery`.`index_id` = '$del_id'";

    if ($DatabaseCo->dbLink->query($query)) {
        $qs = [];
        if ($list_temple_status !== '') {
            $qs['temple_status'] = $list_temple_status;
        }
        $redir = 'temple-mystery-listing.php' . (!empty($qs) ? '?' . http_build_query($qs) : '');
        header('Location: ' . $redir);
        exit;
    }

    die('Error: ' . mysqli_error($DatabaseCo->dbLink));
}

function mystery_listing_photo_url($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return '';
    }

    foreach (['Mystery', 'mystery'] as $dir) {
        $path = __DIR__ . '/uploads/' . $dir . '/' . $filename;
        if (is_file($path)) {
            return './uploads/' . $dir . '/' . $filename;
        }
    }

    return './uploads/Mystery/' . $filename;
}
?>
<style>
    .icon-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        text-align: center;
    }
</style>
<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1"><?php
                if ($list_temple_status === 'approved') {
                    echo 'Approved Mystery Temple';
                } elseif ($list_temple_status === 'pending') {
                    echo 'Approval Pending';
                } elseif ($list_temple_status === 'rejected') {
                    echo 'Rejected Mystery Temple';
                } else {
                    echo 'Mystery Temples';
                }
            ?></h6>
        </div>
        <div class="text-end">
            <a href="add-mystery-temple.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New Temples</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-15">Temple Photo</th>
                            <th class="w-25">Name</th>
                            <th class="w-20">God Name</th>
                            <th class="w-5">Status</th>
                            <th class="w-5">Action</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$select = "SELECT m.*, g.god_name AS listing_god_name
    FROM `mystery` m
    LEFT JOIN `god` g ON g.index_id = m.god_id
    WHERE m.index_id != '0' {$status_sql_fragment}
    ORDER BY m.index_id DESC";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
$listing_query_error = '';
if (!$SQL_STATEMENT) {
    $listing_query_error = mysqli_error($DatabaseCo->dbLink);
    $num_rows = 0;
} else {
    $num_rows = mysqli_num_rows($SQL_STATEMENT);
}

if ($listing_query_error !== '') { ?>
    <tr>
        <td colspan="6">
            <div class="alert alert-danger mb-0">Unable to load mystery temples. <?php echo htmlspecialchars($listing_query_error, ENT_QUOTES, 'UTF-8'); ?></div>
        </td>
    </tr>
<?php } elseif ($num_rows != 0) {
    $i = 1;
    while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
        $rowStatus = strtolower(trim((string) ($Row->status ?? '')));
        $is_rejected_row = ($rowStatus === 'rejected' || $rowStatus === 'reject' || $rowStatus === 'denied' || $rowStatus === 'disapproved' || $rowStatus === '');

        $godName = trim((string) ($Row->listing_god_name ?? ''));
        if ($godName === '' && !empty($Row->god_label) && strtolower(trim((string) $Row->god_label)) !== 'me') {
            $godName = (string) $Row->god_label;
        }

        $photoUrl = mystery_listing_photo_url($Row->photos ?? '');
        $title = htmlspecialchars((string) $Row->title, ENT_QUOTES, 'UTF-8');
        $godDisplay = htmlspecialchars($godName, ENT_QUOTES, 'UTF-8');
        $editQs = $list_temple_status !== '' ? '?temple_status=' . rawurlencode($list_temple_status) : '';
?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td>
                <?php if ($photoUrl !== '') { ?>
                    <a href="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                        <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>" class="header-profile-user" width="60" alt="">
                    </a>
                <?php } ?>
            </td>
            <td><?php echo $title; ?></td>
            <td><?php echo $godDisplay; ?></td>
            <td>
                <?php if ($rowStatus === 'approved') { ?>
                    <div class="icon-container">
                        <i class="fa fa-thumbs-up text-success" style="font-size: 20px;" title="Approved"></i>
                    </div>
                <?php } elseif ($is_rejected_row) { ?>
                    <div class="icon-container">
                        <i class="fa fa-ban text-danger" style="font-size: 20px;" title="Rejected"></i>
                    </div>
                <?php } else { ?>
                    <div class="icon-container">
                        <i class="fa fa-clock text-warning" style="font-size: 20px;" title="Approval pending"></i>
                    </div>
                <?php } ?>
            </td>
            <td>
                <?php if ($user_role === 'Admin') : ?>
                    <a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop"
                       href="add-mystery-temple.php?id=<?php echo (int) $Row->index_id; ?><?php echo $list_temple_status !== '' ? '&temple_status=' . rawurlencode($list_temple_status) : ''; ?>">
                        <i class="fas fa-pencil-alt"></i>
                    </a> &nbsp; &nbsp;
                    <a class="btn btn-sm p-2 btn-danger delete-board alert-box-trigger waves-effect waves-light kill-drop text-white"
                       data-modal="delete-board-alert"
                       data-toggle="modal"
                       data-target="#delete-board-alert"
                       href="#0"
                       data-id="<?php echo (int) $Row->index_id; ?>"
                       id="delete-board<?php echo (int) $Row->index_id; ?>">
                        <i class="fa fa-trash text-white"></i>
                    </a>
                <?php elseif ($user_role === 'Staff') :
                    if ($rowStatus === 'unapproved') : ?>
                        <a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop"
                           href="add-mystery-temple.php?id=<?php echo (int) $Row->index_id; ?><?php echo $list_temple_status !== '' ? '&temple_status=' . rawurlencode($list_temple_status) : ''; ?>">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    <?php endif;
                endif; ?>
            </td>
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="6">
            <div align="center"><strong>No Records!</strong></div>
        </td>
    </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="delete-board-alert" class="modal fade alert-box">
    <form action="" method="post" name="delete_form" id="delete_form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="header-title">Delete Mystery Temple</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" align="center">
                    <h5 class="text-center">Delete Mystery Temple Details?</h5>
                    <p>Are you sure you want to delete this Mystery Temple? All data will be lost.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="form_action" value="Delete" />
                    <input type="hidden" name="del_t" id="delid" value="" />
                    <?php if ($list_temple_status !== '') : ?>
                        <input type="hidden" name="temple_status" value="<?php echo htmlspecialchars($list_temple_status, ENT_QUOTES, 'UTF-8'); ?>" />
                    <?php endif; ?>
                    <button class="btn raised bg-primary text-white ml-2 mt-2" data-dismiss="modal">Cancel</button>
                    <button name="delete_now" type="submit" class="btn mt-2 btn-dash btn-danger raised has-icon" id="modalDelete" value="Delete">Delete</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include_once './includes/footer.php'; ?>

<script>
document.querySelectorAll('.delete-board').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('delid').value = this.getAttribute('data-id');
    });
});
</script>
