<?php
if (isset($_POST['submit-login'])) {
    require 'dbh.inc.php';

    $username = $_POST['username'];
    $password = $_POST['pwd'];

    if (empty($username) || empty($password)) {
        header("Location: ../index.php?errorlog=empty");
        exit();
    }
    else {
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?errorlog=sqlerror");
            exit();
        }
        else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $pwdCheck = password_verify($password, $row['password']);
                if ($pwdCheck == true) {
                    session_start();
                    $_SESSION['userId'] = $row['id'];
                    $_SESSION['username'] = $row['username'];

                    header("Location: ../index.php?login=success");
                    exit();
                }
                else {
                    header("Location: ../index.php?errorlog=wrongpwd");
                    exit();
                }
            }
            else {
                header("Location: ../index.php?errorlog=nouser");
                exit();
            }
        }
    }
}
else {
    header("Location: ../index.php");
    exit();
}