$(document).ready(function () {

    resizeLabelName();
    resizeNames();

    //setInterval(verifGame, 5000);

    function resizeNames()
    {
        let maxSizeName=0;
        $('.playerNameP').each(function () {
            maxSizeName = parseFloat($(this).width());
        });
        let length=0;
        let newFontSize = 1.5;
        $('.playerNameSpan').each(function () {
            length = parseFloat($(this).width());
            newFontSize = Math.min(1.5, (Math.round(150 * maxSizeName / length)/100));
            $(this).css('font-size', newFontSize+'rem');
            $(this).css('max-width', maxSizeName+'px');
            $(this).css('visibility', 'visible');
        });
    }

    function resizeLabelName()
    {
        let maxSizeName=0;
        $('.labelName').each(function () {
            maxSizeName = Math.max(maxSizeName, parseFloat($(this).width()));
        });

        $('.labelName').each(function () {
            $(this).css('min-width', maxSizeName+'px');
        });
    }

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
                            $("#imgMonster_" + player.id).attr('src', '/img/monsters/to-right/regular/'+player.monsterImg);

                            if (player.ready === false)
                                $('#ready_' + player.id).html("<span class='badge badge-danger' style='display:block; min-width:110px; min-height:36px; font-size: 1rem;'>Attente</span>");
                            else
                                $('#ready_' + player.id).html("<span class='badge badge-success' style='display:block; min-width:110px; min-height:36px; font-size: 1rem;'>Prêt</span>");

                        } else {
                            let html = "<div id='row_" + player.id + "' class='row justify-content-center row_player' data-playerid='" + player.id + "'>";
                            // OCCURENCE JOUEUR
                            html = html + "<div class='col-auto text-center align-middle'><label class='col-form-label'><span class='align-middle bg-purple lbl text-yellow text-center'>Joueur " + nbHere + " :</span></label></div>";
                            html = html + "<div class='col-1 text-center font150'><span>" + player.name + "</span></div>";
                            html = html + "<div class='col-auto'><label class='col-form-label'><span class='align-middle bg-purple lbl text-yellow text-center'>Monstre :</span></label></div>";
                            html = html + "<div class='col-3'><input type='text' id='monster_" + player.id + "' name='monster_" + player.id + "' readonly='readonly' disabled='disabled' value='" + player.monster + "' class='form-control'></div>";
                            html = html + "<div class='col-auto'><img id='imgMonster_" + player.id + "' class='img-fluid' style='max-height:4rem;' src='/img/monsters/to-right/regular/" + player.monsterImg + "'></div>";
                            html = html + "<div class='col-1text-center'><span id='ready_" + player.id + "'>";

                            html = html + "<span class='badge badge-";
                            if (player.ready === true) {
                                html = html + "success";
                            } else {
                                html = html + "danger";
                            }
                            html = html+"' style='display:block; min-width:110px; min-height:36px; font-size: 1rem;'>";
                            if (player.ready === true) {
                                html = html + "Prêt";
                            } else {
                                html = html + "Attente";
                            }
                            html = html + "</span></span></div>";

                            if ($("#data").data("playeruserid") === $("#data").data("creatorid")) {
                                html = html + "<div class='col-1 text-right'><a class='btn btn-danger btn-sm' href='/player/kick/" + player.id + "'>Kick</a></div>";
                            }
                            html = html + "</div>";

                            $("#list").append(html);

                            nbHere = +nbHere + 1;

                        }
                    })

                    $(".row_player").each(function () {
                        if (playerStillHere.indexOf($(this).data("playerid")) === -1 && $(this).data("playerid") !== $("#data").data("playerid")) {
                            $(this).remove();
                        }

                    });
                    resizeNames();
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

        let imgName = "/img/monsters/";
        if($("#lobby_monster_self").val() == 0) {
            imgName = imgName + "to-right/regular/none.png";

        } else {
            imgName = imgName + "to-right/regular/" + $('#opt_monster_'+$("#lobby_monster_self").val()).data("imgname");
        }

        $("#imgMonster_self").attr("src",imgName);
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
                $("#imgMonster_self").attr("src","/img/monsters/to-right/regular/none.png");
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