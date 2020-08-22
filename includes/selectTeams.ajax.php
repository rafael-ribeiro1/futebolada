<?php
require 'dbh.inc.php';

$league = $_GET['league'];

$sql = "SELECT * FROM teams WHERE league=".$league;
$result = mysqli_query($conn, $sql);
$queryResult = mysqli_num_rows($result);

if ($queryResult > 0) {
    echo "<select name='winner'>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='".$row['id']."'>".$row['name']."</option>";
    }
    echo "</select>";
}

mysqli_close($conn);