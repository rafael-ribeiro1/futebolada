<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css?version=2">
    <link rel="stylesheet" href="style/mobile.css?version=1">
    <title>Futebolada ⚽</title>
</head>

<body onload="closeBarAuto()">
    <header>
        <div class="header">
            <h1>FUTEBOLADA</h1>
            <?php
            session_start();
            require 'includes/dbh.inc.php';
            if (isset($_SESSION['username'])) : ?>
                <div class="logout">
                    <a href="admin.php" id="username"><?php echo $_SESSION['username']; ?></a>
                    <form action="includes/logout.inc.php" method="post">
                        <input type="submit" value="Logout">
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <?php if (!isset($_SESSION['username'])) : ?>
        <div class="reglogin-box">
            <div class="reglogin">
                <div class="reg">
                    <form action="includes/signup.inc.php" method="post">
                        <input type="text" name="username" placeholder="Username">
                        <div style="display: flex; flex-direction: row">
                            <input type="password" name="pwd" placeholder="Password">
                            <input type="password" name="pwd-repeat" placeholder="Repetir Password">
                        </div>
                        <input type="submit" name="submit-reg" value="Registar">
                        <?php if (isset($_GET['errorreg'])) echo '<p class="erro" style="display: block;">' . error() . '</p>'; ?>
                    </form>
                </div>
                <div class="bar"></div>
                <div class="login">
                    <form action="includes/login.inc.php" method="post">
                        <input type="text" name="username" placeholder="Username" <?php if (isset($_GET['signup'])) echo 'autofocus'; ?>>
                        <input type="password" name="pwd" placeholder="Password">
                        <input type="submit" name="submit-login" value="Login">
                        <?php if (isset($_GET['errorlog'])) echo '<p class="erro" style="display: block;">' . error() . '</p>'; ?>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['beterror']) xor isset($_GET['betsuccess'])) : ?>
        <div <?php if (isset($_GET['beterror'])) echo 'class="bet-error"';
                else echo 'class="bet-success"'; ?> id="bet-bar">
            <div></div>
            <span class="bet-msg"><?php echo betMsg(); ?></span>
            <button onclick="closeBar()" class="close-button">✖</button>
        </div>
    <?php endif; ?>
    <div class="body">
        <div class="left">
            <div class="comp">
                <h2>Competições</h2>
                <hr>
                <?php
                $sql = "SELECT * FROM leagues ORDER BY done";
                $result = mysqli_query($conn, $sql);
                $queryResult = mysqli_num_rows($result);
                if ($queryResult > 0) :
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <div id="<?php echo $row['id']; ?>" class="league">
                            <div class="league-sup" onclick="betLeague(this.parentNode.id)" style="cursor: pointer;">
                                <img src="<?php echo $row['img']; ?>" alt="<?php echo $row['name']; ?>" width="50px">
                                <div class="league-info">
                                    <span class="league-name"><?php echo $row['name']; ?></span>
                                    <span class="league-start">Começa às <?php echo date("H:i", strtotime($row['start'])); ?> de <?php echo date("d/m/Y", strtotime($row['start'])); ?></span>
                                </div>
                                <?php betLeague($conn, $row) ?>
                            </div>
                            <div id="league<?php echo $row['id']; ?>" class="league-inf">
                                <hr>
                                <?php
                                $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
                                $sql2 = "SELECT * FROM leaguebets WHERE user=" . $userId . " AND league=" . $row['id'];
                                $result2 = mysqli_query($conn, $sql2);
                                $queryResult2 = mysqli_num_rows($result2);
                                if ($queryResult2 == 0) : ?>
                                    <form class="league-form" action="includes/betLeague.inc.php" method="post">
                                        <input type="text" name="league-id" value="<?php echo $row['id']; ?>" readonly style="display: none; width: 0; height: 0;">
                                        <select name="winner" id="league-winner">
                                            <?php
                                            $sql2 = "SELECT id, name FROM teams WHERE league=" . $row['id'];
                                            $result2 = mysqli_query($conn, $sql2);
                                            while ($row2 = mysqli_fetch_assoc($result2)) : ?>
                                                <option value="<?php echo $row2['id']; ?>"><?php echo $row2['name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                        <input type="submit" name="bet-league" value="Apostar">
                                    </form>
                                <?php else :
                                    $row2 = mysqli_fetch_assoc($result2);
                                    $sql3 = "SELECT name FROM teams WHERE id=".$row2['winner'];
                                    $result3 = mysqli_query($conn, $sql3);
                                    $row3 = mysqli_fetch_assoc($result3); ?>
                                    <p class="bet-result">Aposta vencedor: <span class="result-bet"><?php echo $row3['name']; ?></span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile;
                else : ?>
                    <div class="no-leagues">
                        <span id="no-league">Sem competições</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="games">
                <h2>Jogos</h2>
                <hr>
                <?php
                $sql = "SELECT * FROM games ORDER BY done";
                $result = mysqli_query($conn, $sql);
                $queryResult = mysqli_num_rows($result);
                if ($queryResult > 0) :
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <div id="<?php echo $row['id']; ?>" class="game">
                            <div class="game-sup" onclick="betGame(this.parentNode.id)" style="cursor: pointer;">
                                <div class="game-sup-sup">
                                    <?php
                                    $sql2 = "SELECT name, img FROM leagues WHERE id=" . $row['league'];
                                    $result2 = mysqli_query($conn, $sql2);
                                    $row2 = mysqli_fetch_assoc($result2);
                                    ?>
                                    <div class="competition">
                                        <img src="<?php echo $row2['img']; ?>" alt="<?php echo $row2['name']; ?>" width="30px">
                                        <div style="display: flex; flex-direction: column;">
                                            <span class="league-name" style="font-size: 11pt;"><?php echo $row2['name']; ?></span>
                                            <span class="game-info" style="margin-top: -7px;"><?php echo $row['info']; ?></span>
                                        </div>
                                    </div>
                                    <?php betGame($conn, $row) ?>
                                </div>
                                <div class="teams">
                                    <?php
                                    $sql3 = "SELECT name, img FROM teams WHERE id=" . $row['teamHome'];
                                    $result3 = mysqli_query($conn, $sql3);
                                    $row3 = mysqli_fetch_assoc($result3);
                                    ?>
                                    <div class="team-home">
                                        <img src="<?php echo $row3['img']; ?>" alt="<?php echo $row3['name']; ?>" width="40px">
                                        <span class="team-name"><?php echo $row3['name']; ?></span>
                                    </div>
                                    <div class="vs-res">
                                        <?php if ($row['done'] == 0) : ?>
                                            <span style="font-size: 15pt;">vs</span>
                                        <?php else : ?>
                                            <span style="font-size: 13pt; font-weight: 600;"><?php echo gameResult($row); ?></span>
                                        <?php endif; ?>
                                        <span class="game-info"><?php echo date("H:i d/m/Y", strtotime($row['datetime'])); ?></span>
                                    </div>
                                    <?php
                                    $sql4 = "SELECT name, img FROM teams WHERE id=" . $row['teamAway'];
                                    $result4 = mysqli_query($conn, $sql4);
                                    $row4 = mysqli_fetch_assoc($result4);
                                    ?>
                                    <div class="team-away">
                                        <span class="team-name"><?php echo $row4['name']; ?></span>
                                        <img src="<?php echo $row4['img']; ?>" alt="<?php echo $row4['name']; ?>" width="40px">
                                    </div>
                                </div>
                            </div>
                            <div id="game<?php echo $row['id']; ?>" class="game-inf">
                                <hr>
                                <?php
                                $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
                                $sql5 = "SELECT * FROM bets WHERE user=" . $userId . " AND game=" . $row['id'];
                                $result5 = mysqli_query($conn, $sql5);
                                $queryResult5 = mysqli_num_rows($result5);
                                if ($queryResult5 == 0) : ?>
                                    <form class="game-form" action="includes/betGame.inc.php" method="post">
                                        <input type="text" name="game-id" value="<?php echo $row['id']; ?>" readonly style="display: none; width: 0; height: 0;">
                                        <div style="margin-bottom: 5px;">
                                            <input type="number" name="result-home" required id="result-home<?php echo $row['id']; ?>" value="0" min="0" oninput="checkResult(<?php echo $row['id']; ?>)">
                                            <span>vs</span>
                                            <input type="number" name="result-away" required id="result-away<?php echo $row['id']; ?>" value="0" min="0" oninput="checkResult(<?php echo $row['id']; ?>)">
                                        </div>
                                        <div id="penalties<?php echo $row['id']; ?>" style="margin-bottom: 8px;">
                                            <input type="radio" name="penalty" value="1" checked>
                                            <span> ← Vencedor penalties → </span>
                                            <input type="radio" name="penalty" value="2">
                                        </div>
                                        <input type="submit" name="bet-game" value="Apostar">
                                    </form>
                                <?php else :
                                    $row5 = mysqli_fetch_assoc($result5); ?>
                                    <p class="bet-result">Aposta: <span class="result-bet"><?php echo gameResult($row5); ?></span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile;
                else : ?>
                    <div class="no-leagues">
                        <span id="no-league">Sem jogos</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="rigth">
            <div class="classification">
                <h2>Classificação</h2>
                <hr>
                <div class="classi">
                    <table>
                        <tr id="table-header">
                            <th class="pos">Pos</th>
                            <th>User</th>
                            <th>Pontos</th>
                        </tr>
                        <?php
                        $sql = "SELECT username, points FROM users ORDER BY points DESC;";
                        $result = mysqli_query($conn, $sql);
                        $c = 1;
                        while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr class="table-content">
                                <td class="pos"><?php echo $c; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['points']; ?></td>
                            </tr>
                        <?php $c++;
                        endwhile;
                        mysqli_close($conn); ?>
                    </table>
                </div>
            </div>
            <div class="cotacao">
                <h2>Cotação</h2>
                <hr>
                <p>Resultado: <span class="points">+3 pontos</span></p>
                <p>Resultado exato: <span class="points">+3 pontos</span></p>
                <p>Apenas res. de um equipa: <span class="points">+1 pontos</span></p>
                <p>Vencedor penalties: <span class="points">+3 pontos</span></p>
                <p>Vencedor competição: <span class="points">+7 pontos</span></p>
            </div>
        </div>
    </div>
    <script src="script/functions.js"></script>
</body>

</html>

<?php
function error()
{
    if (isset($_GET['errorreg'])) {
        if ($_GET['errorreg'] == "empty") return "Preencha todos os campos";
        if ($_GET['errorreg'] == "invaliduid") return "Username inválido";
        if ($_GET['errorreg'] == "pwdrepeat") return "As passwords não são iguais";
        if ($_GET['errorreg'] == "invalidpwd") return "Password inválida";
        if ($_GET['errorreg'] == "sqlerror") return "Erro na base de dados";
        if ($_GET['errorreg'] == "uidtaken") return "Username indisponível";
    }
    if (isset($_GET['errorlog'])) {
        if ($_GET['errorlog'] == "empty") return "Preencha todos os campos";
        if ($_GET['errorlog'] == "sqlerror") return "Erro na base de dados";
        if ($_GET['errorlog'] == "wrongpwd") return "Password incorreta";
        if ($_GET['errorlog'] == "nouser") return "Username inexistente";
    }
    return "Erro";
}

function betMsg()
{
    if (isset($_GET['beterror'])) {
        if ($_GET['beterror'] == "nosession") return 'Sem sessão iniciada';
        if ($_GET['beterror'] == "nogame") return 'Jogo não encontrado';
        if ($_GET['beterror'] == "gamedone") return 'Este jogo já terminou';
        if ($_GET['beterror'] == "gamebet") return 'Já apostou neste jogo';
        if ($_GET['beterror'] == "sqlerror") return 'Erro na base de dados';
        if ($_GET['beterror'] == "nocomp") return 'Competição não encontrada';
        if ($_GET['beterror'] == "compdone") return 'Esta competição já terminou';
        if ($_GET['beterror'] == "compbet") return 'Já apostou nesta competição';
        return 'Erro';
    }
    if (isset($_GET['betsuccess'])) {
        if ($_GET['betsuccess'] == "game") return 'Aposta (jogo) efetuada';
        if ($_GET['betsuccess'] == "league") return 'Aposta (competição) efetuada';
        return 'Aposta efetuada';
    }
}

function betLeague($conn, $row)
{
    $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
    $sql = "SELECT winner FROM leaguebets WHERE user=" . $userId . " AND league=" . $row['id'];
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($row['done'] == 0) {
        if ($queryResult == 0) {
            echo '<span class="bet-league">Apostar vencedor</span>';
        } else {
            echo '<span class="bet-done">Aposta feita</span>';
        }
    } else {
        if ($queryResult == 0) {
            echo '<span class="bet-red">Não apostou</span>';
        } else {
            $row2 = mysqli_fetch_assoc($result);
            if ($row['winner'] == $row2['winner']) {
                echo '<span class="bet-green">+7 pontos</span>';
            } else {
                echo '<span class="bet-red">+0 pontos</span>';
            }
        }
    }
}
function betGame($conn, $row)
{
    $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
    $sql = "SELECT points FROM bets WHERE user=" . $userId . " AND game=" . $row['id'];
    $result = mysqli_query($conn, $sql);
    $queryResult = mysqli_num_rows($result);
    if ($row['done'] == 0) {
        if ($queryResult == 0) {
            echo '<span class="bet-league">Apostar no jogo</span>';
        } else {
            echo '<span class="bet-done">Aposta feita</span>';
        }
    } else {
        if ($queryResult == 0) {
            echo '<span class="bet-red">Não apostou</span>';
        } else {
            $row2 = mysqli_fetch_assoc($result);
            if ($row2['points'] == 0) {
                echo '<span class="bet-red">+0 pontos</span>';
            } else {
                echo '<span class="bet-green">+' . $row2['points'] . ' pontos</span>';
            }
        }
    }
}

function gameResult($row)
{
    if ($row['resultHome'] != $row['resultAway']) {
        return $row['resultHome'] . ' - ' . $row['resultAway'];
    } else {
        if ($row['penalty_winner'] == 1) {
            return '(P)' . $row['resultHome'] . ' - ' . $row['resultAway'];
        } elseif ($row['penalty_winner'] == 2) {
            return $row['resultHome'] . ' - ' . $row['resultAway'] . '(P)';
        } else {
            return $row['resultHome'] . ' - ' . $row['resultAway'];
        }
    }
}
?>