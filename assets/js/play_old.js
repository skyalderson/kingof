$(document).ready(function () {

    //  Initialisation Page :
    // * Afficher les images dans les logs
    // * Redimensionner Div Logs
    // * Activer la boucle d'update si joueur passif

    $('.log_detail').each(function(){
        $(this).html(displayImg($(this).html()));
    });

    $('#playBody').html(displayImg($('#playBody').html()));
    $('#divPlay').show();


    resizeLogDiv();

    setInterval(loop, 5000);
    let activeLoop = true;
    if($('#hasAction').val() == '1') activeLoop = false;

    // -----------------------------------------------------------------------------------------------------------------

    // Interroge le serveur pour récupérer les derniers évènements si Joueur Passif
    function loop()
    {
        if(activeLoop === true){
            getNewLogs(true, true);
        }
    }

    // Récupère les évènmments depuis la dernière actualisation
    function getNewLogs(checkNextAction, displayActionBool)
    {
        let timeOut = 0;
        $.ajax({
            url: "/play/getNewLogs",
            method: "post",
            async: false,

        }).done(function (msg) {

            let last_log = null;
            let logs = JSON.parse(msg);
            if(logs !== null){
                $.each(logs, function (i, log) {
                    last_log = log;
                })
            }
            let mode = '';
            if(last_log !== null){
                let log_to_ask = last_log.idLog
                updateData(last_log);

                if (checkNextAction === true){
                    if(last_log.action == 'end_turn') log_to_ask = log_to_ask +1;
                    mode = 'passive';
                }
                else {
                    mode = 'active';
                }
                if(displayActionBool === true && last_log.action != 'end_turn') timeOut = displayActionToDo(mode, log_to_ask);
            }

            setTimeout(function(){
                if(logs !== null){
                    $.each(logs, function (i, log) {
                        displayNewLog(i, log.htmlContentLog, log.bgClassLog);
                        resizeLogDiv();
                    })
                }
            }, timeOut);

            if(checkNextAction === true) {
                hasActionToDo();
            }
        })
    }

    function updateData(log)
    {
        switch (log.action)
        {
            case "start_turn":
                break;

            case "is_starting_in_city":
                $("#gp_"+log.wasplaying_id).css("width", (100 * log.new_gp / 20)+"%");
                $("#gp_"+log.wasplaying_id).attr("aria-valuenow", log.new_gp);
                $("#gp_"+log.wasplaying_id).html(log.new_gp);
                break;

            case "resolve_victory":
                $("#gp_"+log.wasplaying_id).css("width", (100 * log.new_gp / 20)+"%");
                $("#gp_"+log.wasplaying_id).attr("aria-valuenow", log.new_gp);
                $("#gp_"+log.wasplaying_id).html(log.new_gp);
                break;

            case "end_turn":
                $('#isplaying_name').html(log.isplaying_name);
                $('.card').removeClass('bg-orange').addClass('bg-purple');
                $('#card_'+log.isplaying_id).removeClass('bg-purple').addClass('bg-orange');
                break;
        }
    }















































});