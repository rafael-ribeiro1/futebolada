<?php
if (isset($_POST['submit-league'])) {
    require 'dbh.inc.php';

    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: ../index.php");
        exit();
    }
    $sql = "SELECT admin FROM users WHERE id=" . $_SESSION['userId'];
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row['admin'] == 0) {
        header("Location: index.php");
        exit();
    }

    // DATA
    $league = $_POST['league'];
    $winner = $_POST['winner'];

    $sql = "SELECT done from leagues WHERE id=".$league;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult == 0) {
        header("Location: ../admin.php?errorleague=noleague");
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    if ($row['done'] == 1) {
        header("Location: ../admin.php?errorlaegue=leaguedone");
        exit();
    }

    $sql = "SELECT * FROM teams WHERE id=".$winner." AND league=".$league;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult == 0) {
        header("Location: ../admin.php?errorlaegue=invalidteam");
        exit();
    }

    $sql = "UPDATE leagues SET done = '1', winner = '".$winner."' WHERE id=".$league;
    $result = mysqli_query($conn, $sql);

    $sql = "SELECT * FROM leaguebets WHERE league=".$league;
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        // BET DATA
        $userId = $row['user'];
        $betWinner = $row['winner'];

        $points = $winner == $betWinner ? 7 : 0;

        $sql3 = "SELECT points FROM users WHERE id=".$row['user'];
        $result3 = mysqli_query($conn, $sql3);
        $row3 = mysqli_fetch_assoc($result3);
        $userPoints = $row3['points'] + $points;

        $sql4 = "UPDATE users SET points = '".$userPoints."' WHERE id=".$row['user'];
        $result4 = mysqli_query($conn, $sql4);
    }

    header("Location: ../admin.php");
    exit();

    mysqli_close($conn);
}
else {
    header("Location: ../admin.php");
    exit();
}