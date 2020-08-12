<?php
if (isset($_POST['submit-game'])) {
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
    $gameId = $_POST['game'];
    $resultHome = $_POST['result-home'];
    $resultAway = $_POST['result-away'];
    $penalties = $resultHome == $resultAway ? $_POST['penalty'] : 0;

    $res = $resultHome > $resultAway ? 1 : ($resultHome < $resultAway ? 3 : 2);

    $sql = "SELECT done FROM games WHERE id=".$gameId;
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($queryResult == 0) {
        header("Location: ../admin.php?errorgame=nogame");
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    if ($row['done'] == 1) {
        header("Location: ../index.php?errorgame=gamedone");
        exit();
    }

    $sql = "UPDATE games SET done = '1', resultHome = '".$resultHome."', resultAway = '".$resultAway."', penalty_winner = '".$penalties."' WHERE id=".$gameId;
    $result = mysqli_query($conn, $sql);

    $sql = "SELECT * FROM bets WHERE game=".$gameId;
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        // BET DATA
        $userId = $row['user'];
        $betHome = $row['resultHome'];
        $betAway = $row['resultAway'];
        $betPenalties = $row['penalty_winner'];

        $betRes = $betHome > $betAway ? 1 : ($betHome < $betAway ? 3 : 2);

        $points = 0;

        if ($res == $betRes) $points += 3;

        if (($resultHome == $betHome) && ($resultAway == $betAway)) $points += 3;
        else {
            if ($resultHome == $betHome) $points += 1;
            if ($resultAway == $betAway) $points += 1;
        }

        if (($res == 2) && ($betRes == 2) && ($penalties == $betPenalties)) $points += 3;

        $sql2 = "UPDATE bets SET points = '".$points."' WHERE id=".$row['id'];
        $result2 = mysqli_query($conn, $sql2);

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