$(document).ready(function () {

    setInterval(verifGame, 5000);

    function verifGame() {
        $.ajax({
            url: "/lobby/data",
            method: "post",
            data: {
                idGame: $("#data").data("gameid"),
                idPlayer: $("#data").data("playerid")
            }
        }).done(function (msg) {
            updateData(msg);
        })
    }

    function updateData(msg) {
        let exists = JSON.parse(msg['exists']);
        let kicked = JSON.parse(msg['kicked']);
        let waiting = JSON.parse(msg['waiting']);
        let launch_possible = JSON.parse(msg['launch_possible']);

        if (exists === true) {

            if (waiting === true) {

                if (kicked === false) {

                    let playerStillHere = [];
                    $.each(JSON.parse(msg['data']), function (i, player) {

                        playerStillHere.push(player.id);
                        let nbHere = playerStillHere.length + 1;

                        if ($("#row_" + player.id).length) {
                            $("#monster_" + player.id).val(player.monster);

                            if (player.ready === false)
                                $('#ready_' + player.id).html("<span class='badge badge-danger pt-2' style='display:block; min-width:110px; min-height:36px; font-size: 1rem;'>Attente</span>");
                            else
                                $('#ready_' + player.id).html("<span class='badge badge-success pt-2' style='display:block; min-width:110px; min-height:36px; font-size: 1rem;'>Prêt</span>");
                        } else {
                            let html = "<div id='row_" + player.id + "' class='row justify-content-center row_player mb-2' data-playerid='" + player.id + "'>";
                            // OCCURENCE JOUEUR
                            html = html + "<div class='col-auto text-center align-middle'><p class='text-center bg-purple lbl text-yellow mt-1 p-1'style='min-width:100px;'>Joueur " + nbHere + " :</p></div>";
                            html = html + "<div class='col-xl-1 col-lg-1 col-md-1 text-center'><span>" + player.name + "</span></div>";
                            html = html + "<div class='col-auto'><label class='col-form-label'><span class='align-middle bg-purple lbl text-yellow text-center p-2'>Monstre :</span></label></div>";
                            html = html + "<div class='col-xl-3 col-lg-3 col-md-3'><input type='text' id='monster_" + player.id + "' name='monster_" + player.id + "' readonly='readonly' disabled='disabled' value='" + player.monster + "' class='form-control'></div>";
                            html = html + "<div class='col-xl-1 col-lg-1 col-md-1text-center'><span id='ready_" + player.id + "'>";

                            html = html + "<span class='badge badge-";
                            if (player.ready === true) {
                                html = html + "success";
                            } else {
                                html = html + "danger";
                            }
                            html = html+" pt-2' style='display:block; min-width:110px; min-height:36px; font-size: 1rem;'>";
                            if (player.ready === true) {
                                html = html + "Prêt";
                            } else {
                                html = html + "Attente";
                            }
                            html = html + "</span></span></div>";

                            if ($("#data").data("playeruserid") === $("#data").data("creatorid")) {
                                html = html + "<div class='col-xl-1 col-lg-1 col-md-1 text-right'><a class='btn btn-danger btn-sm mr-2' href='/player/kick/" + player.id + "'>Kick</a></div>";
                            }
                            html = html + "</div>";

                            $("#list").append(html);

                            nbHere = +nbHere + 1;

                        }
                    })

                    let rows = $(".row_player").each(function () {
                        if (playerStillHere.indexOf($(this).data("playerid")) === -1 && $(this).data("playerid") !== $("#data").data("playerid")) {
                            $(this).remove();
                        }

                    });
                } else {


                    $('#modal_msg').html("Vous avez été exclu de la partie.");
                    $('#modal').modal();
                    setTimeout(function () {
                        window.location.replace("/game/list");
                    }, 2000);
                }
            } else {
                $('#modal_msg').html("La partie va commencer !");
                $('#modal').modal();
                setTimeout(function () {
                    window.location.replace("/play/"+$("#data").data("gameid"));
                }, 2000);
            }
        } else {
            $('#modal_msg').html("La partie a été annulée par son créateur.");
            $('#modal').modal();
            setTimeout(function () {
                window.location.replace("/game/list");
            }, 2000);
        }

        if ( $("#data").data("playeruserid") === $("#data").data("creatorid")) {
            if(launch_possible === true)
                $("#launchgame").prop('disabled', false);
            else
                $("#launchgame").prop('disabled', true);
        }
    }

    $("#lobby_monster_self").change(function () {
        selectMonster();
    });

    function selectMonster() {
        $.ajax({
            url: "/lobby/select/monster",
            method: "post",
            data: {
                idGame: $("#data").data("gameid"),
                idPlayer: $("#data").data("playerid"),
                idMonster: $("#lobby_monster_self").val()
            }
        }).done(function (msg) {
            if (msg === "taken") {
                $('#modal_msg').html("Ce monstre a déjà été sélectionné par un autre joueur.");
                $('#modal').modal();
                $("#lobby_monster_self").val("0");
                $('#lobby_ready_self').bootstrapToggle('off');
            }
        })
    }

    $("#lobby_ready_self").change(function () {
        if (document.getElementById('lobby_ready_self').checked && $("#lobby_monster_self").val() === "0") {
            $('#lobby_ready_self').bootstrapToggle('off');
            $('#modal_msg').html("Vous devez d'abord choisir un monstre.");
            $('#modal').modal();
        } else {
            readyState();
        }
    });

    function readyState() {
        let isReady = document.getElementById('lobby_ready_self').checked;

        $.ajax({
            url: "/lobby/ready",
            method: "post",
            data: {
                idPlayer: $("#data").data("playerid"),
                ready: isReady
            }
        }).done(function (msg) {
        })
    }

    $("#launchgame").click(function () {
        $.ajax({
            url: "/lobby/islaunchready",
            method: "post",
            data: {
                idGame: $("#data").data("gameid"),
            }
        }).done(function (msg) {
            let launch_possible = JSON.parse(msg['launch_possible']);

            if(launch_possible == true){
                launchGame();
            }
            else {
                $('#modal_msg').html("Nope.");
                $('#modal').modal();
            }
        })
    });

    function launchGame()
    {
        $.ajax({
            url: "/lobby/launch",
            method: "post",
            data: {
                idGame: $("#data").data("gameid")
            }
        }).done(function (msg) {

        })
    }
});