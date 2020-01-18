$(document).ready(function () {

    resizeLabelName();
    resizeNames();

    setInterval(verifGame, 5000);

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

    function verifGame()
    {
        $.ajax({
            url: "/lobby/data",
            method: "post",
            async: false,
            data: {
                idGame: $("#data").data("gameid"),
                idPlayer: $("#data").data("playerid")
            }
        }).done(function (msg) {
            updateData(msg);
        })
    }

    function updateData(msg)
    {
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

                            if (player.ready === false) {
                                $('#ready_' + player.id).removeClass("badge-success");
                                $('#ready_' + player.id).addClass("badge-danger");
                                $('#ready_' + player.id).html("Attente");
                            }else {
                                $('#ready_' + player.id).removeClass("badge-danger");
                                $('#ready_' + player.id).addClass("badge-success");
                                $('#ready_' + player.id).html("Prêt");
                            }
                        } else {

                            let html = "<div id='row_" + player.id + "' class='row justify-content-center align-items-center row_player' data-playerid='" + player.id + "'>";

                            // Label Joueur
                            html = html + "<div class='col-1 m-0 p-0 ml-0'>";
                            html = html + "<div class='row m-0 p-0 align-middle'>";
                            html = html + "<div class='col-";
                            if ($("#data").data("playeruserid") === $("#data").data("creatorid"))
                                html = html + "auto";
                            else
                                html = html + "11";
                            html = html + " m-0 p-0 text-left'>";
                            html = html + "<p class=' pr-2 pl-2 pt-1 pb-1 m-0 align-middle bg-purple lbl text-yellow text-center'><span class='labelName d-block'>Joueur ";


                            // BUUUUUUUUUUUUUUUUUUUUUUUUG
                            if ($("#data").data("playeruserid") !== $("#data").data("creatorid")) {html = html + "#";}
                            html = html + nbHere + "</span></p></div>";
                            if ($("#data").data("playeruserid") === $("#data").data("creatorid")) {html = html + "<div class='col-auto d-inline align-middle m-0 p-0 pt-1 pl-1'><a class='btn btn-danger btn-sm fas fa-ban p-1' href='/player/kick/" + player.id + "'></a></div>";}
                            html = html + "</div></div>";


                            // Pseudo Joueur
                            html = html + "<div class='col-1 text-center m-0 p-0  font150'><p class='playerNameP m-0 p-0 pl-";
                            if ($("#data").data("playeruserid") === $("#data").data("creatorid")) {html = html + "2";}
                            else {html = html + "1";}
                            html = html + "' style='min-width:100%; max-width:100%'><span class='playerNameSpan m-0 p-0' style='line-height:1.5rem; font-size:1.5rem; visibility:hidden;'>" + player.name + "</span></p> </div>";


                            // Label Monstre
                            html = html + "<div class='col-1 m-0 p-0 text-center'><label class='col-form-label'><span class='p-2 align-middle bg-purple lbl text-yellow text-center'>Monstre</span> </label> </div>";


                            // Bloc Monstre
                            html = html + "<div class='col-3 m-0 p-0'>";
                            html = html + "<div class='row align-items-center m-0 p-0'>";
                            html = html + "<div class='col-10 m-0 p-0 01'>";
                            html = html + "<input type='text' id='monster_" + player.id + "' name='monster_' readonly='readonly' disabled='disabled' value='" + player.monster + "' class='form-control'>";
                            html = html + "</div>";
                            html = html + "<div class='col-2 m-0 p-0 pl-2'>";
                            html = html + "<img class='img-fluid rounded-circle border border-dark bg-white' id='imgMonster_" + player.id + "' style='max-height:3.7rem;' src='/img/monsters/to-right/regular/" + player.monsterImg + "'>";
                            html = html + "</div>";
                            html = html + "</div>";
                            html = html + "</div>";


                            // Bloc Ready or Not
                            html = html + "<div class='col-1 m-0 p-0 ml-0 pl-2 text-center align-middle'>";
                            html = html + "<span id='ready_" + player.id + "' class='badge badge-";
                            if (player.ready === true) {
                                html = html + "success";
                            } else {
                                html = html + "danger";
                            }
                            html = html + " p-0' style='display:block; min-height:2.5rem; line-height:2.5rem; font-size: 1rem;'>";
                            if (player.ready === true) {
                                html = html + "Prêt";
                            } else {
                                html = html + "Attente";
                            }
                            html = html + "</span></div>";



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

    $("#lobby_monster_self").change(function ()
    {
        selectMonster();

        let imgName = "/img/monsters/";
        if($("#lobby_monster_self").val() == 0) {
            imgName = imgName + "to-right/regular/none.png";

        } else {
            imgName = imgName + "to-right/regular/" + $('#opt_monster_'+$("#lobby_monster_self").val()).data("imgname");
        }

        $("#imgMonster_self").attr("src",imgName);
    });

    function selectMonster()
    {
        $.ajax({
            url: "/lobby/select/monster",
            method: "post",
            async: false,
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

    $("#lobby_ready_self").change(function ()
    {
        if (document.getElementById('lobby_ready_self').checked && $("#lobby_monster_self").val() === "0") {
            $('#lobby_ready_self').bootstrapToggle('off');
            $('#modal_msg').html("Vous devez d'abord choisir un monstre.");
            $('#modal').modal();
        } else {
            readyState();
        }
    });

    function readyState()
    {
        let isReady = document.getElementById('lobby_ready_self').checked;

        $.ajax({
            url: "/lobby/ready",
            method: "post",
            async: false,
            data: {
                idPlayer: $("#data").data("playerid"),
                ready: isReady
            }
        }).done(function (msg) {

        })
    }

    $("#launchgame").click(function ()
    {
       /* $.ajax({
            url: "/lobby/islaunchready",
            method: "post",
            async: false,
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
        })*/

        launchGame();
    });

    function launchGame()
    {
        $.ajax({
            url: "/lobby/launch",
            method: "post",
            async: false,
            data: {
                idGame: $("#data").data("gameid")
            }
        }).done(function (msg) {
            if(msg === 'OK') {
                window.location.replace("/play/"+$("#data").data("gameid"));
            }
        })
    }
});