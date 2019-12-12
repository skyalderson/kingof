$(document).ready(function () {
    setInterval(verifList, 5000);

    function verifList() {
        $.ajax({
            url: "/list/data",
            method: "post",
        }).done(function (msg) {
            updateData(msg);
        })
    }

    function updateData(msg) {

        let gameStillHere = [];
        $.each(JSON.parse(msg['games']), function (i, game) {

            gameStillHere.push(game.id);
            if ($("#game_" + game.id).length) {
                $('#nb_players_' + game.id).html(game.nb_players);
            }
            else {

                let html = "<div class='row text-center row-game' id='game_"+game.id+"' data-id='"+game.id+"'>";

                html =  html + "<div class='col-xl-1' id='img_"+game.id+"'><img class='img-fluid' src='/img/logos/"+game.img_board+"'' style='max-height:30px;'></div>";
                html = html + "<div class='col-xl-3' id='name_"+game.id+"'>"+game.name+" </div>";
                html = html + "<div class='col-xl-2' id='select_monster_"+game.id+"'>"+game.monster_select+"</div>";
                html = html + "<div class='col-xl-1' id='mode_"+game.id+"'>"+game.mode+"</div>";
                html = html + "<div class='col-xl-1' id='rules_"+game.id+"'></div>";
                html = html + "<div class='col-xl-1' id='nb_players_t_"+game.id+"'><p><span id='nb_players_"+game.id+"'>"+game.nb_players+"</span> sur "+game.max_players+"</p></div>";
                html = html + "<div class='col-xl-3' id='btn_"+game.id+"'><button type='button' class='btn btn-danger btn-join' data-id='"+game.id+"'>Rejoindre</button> </div>";

                html =  html + "</div>";
                $("#list").append(html);
            }
        });

        let rows = $(".row-game").each(function () {
            if (gameStillHere.indexOf($(this).data("id")) === -1) {
                $(this).remove();
            }

        });
    }

    $(".btn-join").click(function () {
        isSlotAvailable($(this).data("id"));
    });

    function isSlotAvailable(idGame) {
        $.ajax({
            url: "/game/slot_available",
            method: "post",
            data: {
                idGame: idGame,
                idUser: $("#data").data("userid")
            }
        }).done(function (msg) {
            if(msg == "ok"){
                window.location.replace("/game/lobby/"+idGame);
            }
            else {
                $('#modal_msg').html("Désolé, la partie est complète !");
                $('#modal').modal();
            }
        })
    }
});
