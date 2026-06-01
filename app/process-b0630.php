<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: https://bhakti-v1.webtechsoftwaresolutions.com");
include_once './class/databaseConn.php';

include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();



include_once './class/XssClean.php';

$xssClean = new xssClean();



error_reporting(0);



session_start();  // REQUIRED

if (isset($_POST['action']) && $_POST['action'] == 'login') {

    $username = strtolower(trim($_POST['username']));
    $password = trim($_POST['password']);

    $num = 0;
    $num2 = 0;

    // ADMIN LOGIN
    if ($username == 'bhaktikalpa') {

        $query = "SELECT * FROM admin 
                  WHERE LOWER(username)='$username' 
                  AND password='" . md5($password) . "'";

        $res = $DatabaseCo->dbLink->query($query);
        $num = mysqli_num_rows($res);

        if ($num > 0) {
            $_SESSION["admin_id"] = 1;
            $_SESSION["staff_id"] = null;
            echo "success";
            exit;
        }

    } else {

        // STAFF LOGIN
        $int = (int) filter_var($username, FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM staff 
                  WHERE index_id='$int' 
                  AND password='" . base64_encode($password) . "'";

        $res = $DatabaseCo->dbLink->query($query);
        $num2 = mysqli_num_rows($res);

        if ($num2 > 0) {
            $row = mysqli_fetch_object($res);
            $_SESSION["staff_id"] = $row->index_id;
            $_SESSION["admin_id"] = null;
            echo "success";
            exit;
        }
    }

    // FAILED LOGIN
    echo "failed";
    exit;
}

if (isset($_REQUEST['submit'])) {

    $salt = "";

    $oldpass = trim($_REQUEST['oldpassword']);

    $oldpass = md5($salt . $oldpass);

    $newpass = trim($_REQUEST['newpassword']);

    $newpass = md5($salt . $newpass);

    $num = mysqli_num_rows(mysqli_query($DatabaseCo->dbLink, "select id from superadmin where `password`='$oldpass'"));

    if ($num > 0) {

        $sql = "update superadmin set `password`='$newpass' where `password`='$oldpass' and id='1'";

        $go = mysqli_query($DatabaseCo->dbLink, $sql);

        echo "<script>window.location='account_setting.php?pwdalt=1'</script>";

    } else {

        echo "<script>window.location='account_setting.php?pwdalt=2'</script>";

    }

}

if (isset($_REQUEST['state_id'])) {

    $SQL_STATEMENT_STATE =  $DatabaseCo->dbLink->query("SELECT state_code FROM state WHERE state_id='" . $_REQUEST['state_id'] . "'");

    $state = mysqli_fetch_assoc($SQL_STATEMENT_STATE);

?>

    <option value="">Select City</option>

    <?php

    $SQL_STATEMENT_city =  $DatabaseCo->dbLink->query("SELECT * FROM city WHERE state_code='" . $state['state_code'] . "' ORDER BY city_name ASC");

    while ($DatabaseCo->dbRow = mysqli_fetch_object($SQL_STATEMENT_city)) { ?>

        <option value="<?php echo $DatabaseCo->dbRow->city_id; ?>"><?php echo $DatabaseCo->dbRow->city_name ?></option>

<?php }

}

?>