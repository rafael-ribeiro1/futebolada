<?php
if (isset($_POST['submit-reg'])) {
    require 'dbh.inc.php';

    $username = $_POST['username'];
    $password = $_POST['pwd'];
    $passwordRpt = $_POST['pwd-repeat'];

    if (empty($username) || empty($password) || empty($passwordRpt)) {
        header("Location: ../index.php?errorreg=empty");
        exit();
    }
    else if (!preg_match("/^[a-zA-Z\d_]{1,250}$/", $username)) {
        header("Location: ../index.php?errorreg=invaliduid");
        exit();
    }
    else if ($password !== $passwordRpt) {
        header("Location: ../index.php?errorreg=pwdrepeat");
        exit();
    }
    else if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,12}$/', $password)) {
        header("Location: ../index.php?errorreg=invalidpwd");
        exit();
    } else {
        $sql = "SELECT username FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?errorreg=sqlerror");
            exit();
        }
        else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../index.php?errorreg=uidtaken");
                exit();
            } else {
                $sql = "INSERT INTO users (username, password) VALUES (?, ?);";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../index.php?errorreg=sqlerror");
                    exit();
                }
                else {
                    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPwd);
                    mysqli_stmt_execute($stmt);
                    header("Location: ../index.php?signup=success");
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
else {
    header("Location: ../index.php");
    exit();
}