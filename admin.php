<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futebolada ⚽ - Admin</title>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: index.php");
        exit();
    }
    require 'includes/dbh.inc.php';
    $sql = "SELECT admin FROM users WHERE id=" . $_SESSION['userId'];
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row['admin'] == 0) {
        header("Location: index.php");
        exit();
    }
    ?>
    <a href="index.php">Home</a>
    <form action="includes/finalyzeGame.inc.php" method="post">
        <select name="game">
            <?php
            $sql = "SELECT id FROM games;";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) : ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['id']; ?></option>
            <?php endwhile; ?>
        </select>
        <div>
            <input type="number" name="result-home" required value="0" min="0">
            <span>vs</span>
            <input type="number" name="result-away" required value="0" min="0">
        </div>
        <div>
            <input type="radio" name="penalty" value="1" checked>
            <span> ← Vencedor penalties → </span>
            <input type="radio" name="penalty" value="2">
        </div>
        <input type="submit" name="submit-game" value="Finalizar jogo">
    </form>
    <hr>
    <form action="includes/finalyzeLeague.inc.php" method="post">
        <select name="league">
            <?php
            $sql = "SELECT id, name FROM leagues";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) : ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="number" name="winner" required min="1">
        <input type="submit" name="submit-league" value="Finalizar competição">
    </form>
</body>
<?php mysqli_close($conn) ?>

</html>