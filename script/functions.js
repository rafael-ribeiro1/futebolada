function betLeague(id) {
    var betDiv = document.getElementById("league".concat(id))
    if (betDiv.style.display === "block") {
        betDiv.style.display = "none"
    } else {
        betDiv.style.display = "block"
    }
}

function betGame(id) {
    var betDiv = document.getElementById("game".concat(id))
    if (betDiv.style.display === "block") {
        betDiv.style.display = "none"
    } else {
        betDiv.style.display = "block"
    }
}

function checkResult(id) {
    var home = document.getElementById('result-home'.concat(id))
    var away = document.getElementById('result-away'.concat(id))
    var penalties = document.getElementById('penalties'.concat(id))
    if (home.value == away.value) {
        penalties.style.display = "block"
    } else {
        penalties.style.display = "none"
    }
}

function closeBar() {
    var bar = document.getElementById('bet-bar')
    bar.style.display = "none"
}

function closeBarAuto() {
    setTimeout(function () {
        var bar = document.getElementById('bet-bar')
        bar.style.display = "none"
    }, 10000)
}

function showTeams(id) {
    var xhttp
    var teams = document.getElementById('league-teams')

    if (id == 0) {
        teams.innerHTML = ""
        return
    }

    xhttp = new XMLHttpRequest()
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            teams.innerHTML = this.responseText
        }
    }
    xhttp.open("GET", "includes/selectTeams.ajax.php?league=" + id, true)
    xhttp.send()
}