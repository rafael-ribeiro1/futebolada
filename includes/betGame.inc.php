<?php
if (isset($_POST['bet-game'])) {
    require 'dbh.inc.php';

    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: ../index.php?beterror=nosession");
        exit();
    }

    // DATA
    $gameId = $_POST['game-id'];
    $userId = $_SESSION['userId'];
    $resultHome = $_POST['result-home'];
    $resultAway = $_POST['result-away'];
    $penalties = $resultHome == $resultAway ? $_POST['penalty'] : 0;
    
    $sql = "SELECT done FROM games WHERE id=".$gameId;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult == 0) {
        header("Location: ../index.php?beterror=nogame");
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    if ($row['done'] == 1) {
        header("Location: ../index.php?beterror=gamedone");
        exit();
    }

    $sql = "SELECT * FROM bets WHERE user=".$userId." AND game=".$gameId;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult > 0) {
        header("Location: ../index.php?beterror=gamebet");
        exit();
    }

    $sql = "INSERT INTO bets (user, game, resultHome, resultAway, penalty_winner) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../index.php?beterror=sqlerror");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "iiiii", $userId, $gameId, $resultHome, $resultAway, $penalties);
        mysqli_stmt_execute($stmt);
        header("Location: ../index.php?betsuccess=game");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
else {
    header("Location: ../index.php");
    exit();
}