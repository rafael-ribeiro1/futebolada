<?php
if (isset($_POST['bet-league'])) {
    require 'dbh.inc.php';

    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: ../index.php?beterror=nosession");
        exit();
    }
    
    // DATA
    $leagueId = $_POST['league-id'];
    $winner = $_POST['winner'];
    $userId = $_SESSION['userId'];

    $sql = "SELECT done FROM leagues WHERE id=".$leagueId;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult == 0) {
        header("Location: ../index.php?beterror=nocomp");
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    if ($row['done'] == 1) {
        header("Location: ../index.php?beterror=compdone");
        exit();
    }

    $sql = "SELECT * FROM leaguebets WHERE user=".$userId." AND league=".$leagueId;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult > 0) {
        header("Location: ../index.php?beterror=compbet");
        exit();
    }

    $sql = "INSERT INTO leaguebets (user, league, winner) VALUES (?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../index.php?beterror=sqlerror");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "iii", $userId, $leagueId, $winner);
        mysqli_stmt_execute($stmt);
        header("Location: ../index.php?betsuccess=league");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
else {
    header("Location: ../index.php");
    exit();
}