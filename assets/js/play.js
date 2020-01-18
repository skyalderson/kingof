$(document).ready(function () {

    $(document).on('click', '#refresh1', function(){
        getNewData();
    });

    $(document).on('click', '#refresh2', function(){
        getNewData();
    });

    //  Initialisation Page :
    // * Afficher les images dans les logs
    // * Redimensionner Div Logs
    // * Activer la boucle d'update si joueur passif

    $('.log_detail').each(function(){
        $(this).html(displayImg($(this).html(), 1.2));
        $(this).html(displayNameInLog($(this).html()));
    });

    $('#playBody').html(displayImg($('#playBody').html(), 1.5));

    resizeLogAction();
    resizeButtons();
    resizeLogDiv();
    moveCursorLogDiv();

    displayActionToDoCustom($('#nameAction').val());


    setInterval(loopGetNewData, 3000);
    let activeLoop = true;
    if($('#hasAction').val() == '1') {
        activeLoop = false;
    }

    // -----------------------------------------------------------------------------------------------------------------

    // Interroge le serveur pour récupérer les derniers évènements si Joueur Passif
    function loopGetNewData()
    {
        if(activeLoop === true){
            getNewData();
        }
    }

    function updateDisplay(data)
    {
        if(data.logs !== null) {
            displayGameData(data.gameData);
            displayActionToDo(data.action);
            displayLogs(data.logs);
        }

        if(data.hasActionToDo === true) {
            activeLoop = false;
        }
        else {
            activeLoop = true;
        }

        resizeLogDiv();
        resizeLogAction();
        resizeButtons();
    }



    // Récupère les évènmments depuis la dernière actualisation
    function getNewData()
    {
        let timeOut = 0;
        $.ajax({
            url: "/play/getNewData",
            method: "post",
            async: false,

        }).done(function (response) {
            let data = JSON.parse(response);
            updateDisplay(data);
        })
    }

    // valid Action
    $(document).on('click', '.playBtn', function(){
        let data = {};

        switch($('#nameAction').val()) {
            case 'throw_dices':
                $('.dice_checkbox').each(function() {
                    data[$(this).data('position')] = $(this).prop('checked');
                });
                break;

            case 'resolve_order_dices':
                for (let i = 1; i <= 4; i++) {
                    data[i] = $('#resolve_1_' + i + ' img:first').data('typedice');
                }
                break;

            case 'ask_to_leave_tokyo':
                data = $(this).val();
                break;
        }

        $('#playBody').html('');
        $('#playBtn').html('');
        let htmlBody ='<div class="spinner-grow spinner-grow-xl text-dark" role="status" style="min-height:100%"><span class="sr-only "></span></div>';
        $('#playBody').html(htmlBody);
        setTimeout(function(){ validAction(data); }, 1000);
    });

    function validAction(data)
    {
        $.ajax({
            url: "/play/validAction",
            method: "post",
            async: false,
            data: {data: data},

        }).done(function (response) {
            let data = JSON.parse(response);
            updateDisplay(data);
        })
    }

    function displayGameData(gameDataLogs)
    {
        let value = null;
        let value2 = null;

        $.each(gameDataLogs, function (i, gameDatas) {
            $.each(gameDatas, function (k, gameData) {
                switch(gameData.type) {
                    case 'vp':
                        value =  parseInt(gameData.value);
                        $("#vp_"+gameData.idPlayer).css("width", (100 * value / 20)+"%");
                        $("#vp_"+gameData.idPlayer).attr("aria-valuenow", value);
                        $("#vp_"+gameData.idPlayer).html(value);
                        break;

                    case 'hp':
                        value =  parseInt(gameData.value);
                        value2 =  parseInt(gameData.value2);
                        $("#hp_"+gameData.idPlayer).css("width", (100 * value / value2)+"%");
                        $("#hp_"+gameData.idPlayer).attr("aria-valuenow", value);
                        $("#hp_"+gameData.idPlayer).html(value);
                        break;

                    case 'mana':
                        value =  parseInt(gameData.value);
                        $("#nbMana_"+gameData.idPlayer).html(value);
                        break;

                    case 'interrupting_action':
                        $("#isplaying_label").html("ACTION DE");
                        $("#isplaying_name").html(gameData.value);
                        $("#isplaying_img").html('<img style="max-width:2.5rem;" src="'+gameData.value2+'">');
                        if(parseInt($("#idPlayerSession").val())=== gameData.idPlayer) {
                            $('#playGlobal').addClass('bg-orange');
                        }
                        else {
                            $('#playGlobal').removeClass('bg-orange');
                        }
                        break;

                    case 'resuming_turn':
                        $("#isplaying_label").html("TOUR DE");
                        $("#isplaying_name").html(gameData.value);
                        $("#isplaying_img").html('<img style="max-width:2.5rem;" src="'+gameData.value2+'">');
                        if(parseInt($("#idPlayerSession").val())=== gameData.idPlayer) {
                            $('#playGlobal').addClass('bg-orange');
                        }
                        else {
                            $('#playGlobal').removeClass('bg-orange');
                        }
                        break;

                    case 'end_turn':
                        if(parseInt($("#idPlayerSession").val())=== gameData.idPlayer) $('#playGlobal').removeClass('bg-orange');
                        $('#display_'+gameData.idPlayer).removeClass('bg-orange');
                        $('#display_'+gameData.idPlayer).addClass('bg-purple');
                        break;

                    case 'start_turn':
                        $("#isplaying_label").html("TOUR DE");
                        $("#isplaying_name").html(gameData.value);
                        $("#isplaying_img").html('<img style="max-width:2.5rem;" src="'+gameData.value2+'">');
                        if(parseInt($("#idPlayerSession").val())=== gameData.idPlayer) $('#playGlobal').addClass('bg-orange');
                        $('#display_'+gameData.idPlayer).removeClass('bg-purple');
                        $('#display_'+gameData.idPlayer).addClass('bg-orange');
                        break;

                    case 'out_of_tokyo':
                        value =  parseInt(gameData.value);
                        $("#inTokyo"+value).html('');
                        $("#badgeTokyo_"+gameData.idPlayer).html('');
                        $("#monsterImg_"+gameData.idPlayer).removeClass('img-in-city');
                        break;

                    case 'in_tokyo':
                        value =  parseInt(gameData.value);
                        $("#inTokyo"+value).html('<img class="img-fluid" src="/img/monsters/to-right/in-tokyo-'+value+'/'+gameData.value2+'"/>');
                        $("#badgeTokyo_"+gameData.idPlayer).html('<img class="img-fluid p-1" src="/img/symbols/tokyo'+value+'.png" >');
                        $("#monsterImg_"+gameData.idPlayer).addClass('img-in-city');
                        break;

                    case 'dead':
                        $("#playerDead_"+gameData.idPlayer).html('<img class="img-fluid p-1 img-dead" src="/img/symbols/dead.png" >');

                        $("#display_"+gameData.idPlayer).removeClass('bg-purple');
                        $("#display_"+gameData.idPlayer).removeClass('bg-orange');
                        $("#display_"+gameData.idPlayer).addClass('bg-gray');

                        $("#playerName_"+gameData.idPlayer).removeClass('text-yellow');
                        $("#playerName_"+gameData.idPlayer).addClass('text-gray');

                        $("#monsterImg_"+gameData.idPlayer).addClass('bg-gray img-dead');

                        $("#playerHeart_"+gameData.idPlayer).addClass('img-dead');
                        $("#hp_"+gameData.idPlayer).removeClass('bg-danger');
                        $("#hp_"+gameData.idPlayer).addClass('bg-gray2');
                        $("#hp_"+gameData.idPlayer).removeClass('text-white');
                        $("#playerName_"+gameData.idPlayer).addClass('text-gray');

                        $("#playerStar_"+gameData.idPlayer).addClass('img-dead');
                        $("#vp_"+gameData.idPlayer).removeClass('bg-primary');
                        $("#vp_"+gameData.idPlayer).addClass('bg-gray2');
                        $("#vp_"+gameData.idPlayer).removeClass('text-white');
                        $("#playerName_"+gameData.idPlayer).addClass('text-gray');

                        $("#playerFlash_"+gameData.idPlayer).addClass('img-dead');
                        $("#playerMana_"+gameData.idPlayer).removeClass('bg-green');
                        $("#playerMana_"+gameData.idPlayer).addClass('bg-gray2');
                        $("#nbMana_"+gameData.idPlayer).removeClass('text-white');
                        $("#playerName_"+gameData.idPlayer).addClass('text-gray');

                        $('.playerCard_'+gameData.idPlayer).each(function() {
                            $(this).addClass('img-dead');
                        });
                        break;

                    case 'has_won_by_victory_points':
                        $('#modal_msg').html(gameData.value+ "a gagné en obtenant 20 points de victoire.");
                        $('#modal').modal();
                        break;

                    case 'has_won_by_kills':
                        $('#modal_msg').html(gameData.value+ "en étant le dernier survivant.");
                        $('#modal').modal();
                        break;
                }
            });
        });
    }

    function displayLogs(logs)
    {
        let idLastLog = 0;
        $.each(logs, function (i, log) {
            idLastLog = i;
            displayNewLog(i, log.htmlContentLog);
        });
        moveCursorLogDiv();
        updateLastLogSeen(idLastLog);
    }

    function updateLastLogSeen(idLog)
    {
        $.ajax({
            url: "/play/updateLastSeenLog",
            method: "post",
            async: false,
            data: {
                idLog: idLog,
            }
        }).done(function (msg) {
            console.log("Last Log Updated");
        })
    }

    function displayActionToDo(action)
    {
        $('#playBody').html(displayImg(action.playBody, 1.5));

        if(action.playBtn !== null) {
            let htmlPlayBtn2 = '';
            let htmlPlayBtn = "<button id='playBtnContent' class='btn btn-danger playBtn' value='1'>"+action.playBtn+"</button>";
            if(action.playBtn2 !== null) {
                htmlPlayBtn2 = "&nbsp;&nbsp;<button id='playBtnContent2' class='btn btn-danger playBtn' value='2'>"+action.playBtn2+"</button>";
            }
            $('#playBtn').html(htmlPlayBtn+htmlPlayBtn2);
            resizeButtons();

        }
        else {
            $('#playBtn').html("");
        }
        displayActionToDoCustom(action.nameAction);
        $('#nameAction').val(action.nameAction);
    }

    function displayActionToDoCustom(nameAction)
    {
        switch(nameAction)
        {
            case 'throw_dices':
                $('#playBtn').hide();

                let timeOut = 0;
                for(let i = 1 ; i<= 8 ; i++) {
                    if($("#diceGif_"+i).length) {
                        if($("#diceGifImg_"+i).length) {
                            timeOut = timeOut + 500;
                        }
                        setTimeout(function(){
                            $("#diceGif_"+i).hide();

                            if($("#diceGif_"+(i+1)).length == 0) {

                                for(let j = 1 ; j<= 8 ; j++) {
                                    if($("#diceGif_"+j).length) $("#diceCheckbox_"+j).show(0);
                                }
                                $('#playBtn').show();
                            }
                        }, timeOut);
                    }
                }
                break;
        }
    }

    function displayNewLog(idLog, htmlContentLog)
    {
        htmlContentLog = displayImg(htmlContentLog, 1.2);
        htmlContentLog = displayNameInLog(htmlContentLog);
        $('#divLog').append("<div class='log_detail border border-dark bg-purple-medium p-1 pl-2 pr-2 m-0 text-left'>"+htmlContentLog+"</div>");
    }

    function displayImg(str, nbRem)
    {

        str = str.replace( /##/g, '<span class="rounded bg-white" style="width:'+(nbRem * 1.2)+'rem; height:'+(nbRem * 1.2)+'rem"><img src="/img/');
        str = str.replace(/££/g, '" style="max-height:'+nbRem+'rem;"></span>');
        str = str.replace( /#/g, "<img src='/img/");
        str = str.replace(/£/g, "' style='max-height:"+nbRem+"rem;'>");

        return str;
    }

    function displayNameInLog(str)
    {
        str = str.replace( /¤/g, "<span class='text-yellow'>");
        str = str.replace(/µ/g, "</span>");
        return str;
    }

    function resizeButtons()
    {
        if($('#playBtnContent2').length) {
            $('#playBtnContent').css('min-width', $('#playBtnContent2').css('width'));
            $('#playBtnContent2').css('min-width', $('#playBtnContent').css('width'));
        }
    }

    function resizeLogDiv()
    {
        let heightToDisplay = 0;

        if (parseInt($('#nbPlayers').val()) <= 3) {
            //let pbPiocheDiv = $('#piocheDiv').css('padding-bottom');
            //pbPiocheDiv = parseFloat(pbPiocheDiv);

            heightToDisplay = $('#centralRow').outerHeight();
        }
        else {
            //let mbDivLogContainer = $('#divLogContainer').css('margin-bottom');
            //mbDivLogContainer = parseFloat(mbDivLogContainer);
            heightToDisplay = ($('#centralRow').outerHeight() / 2);
        }

        heightToDisplay = heightToDisplay - $('#divLogTitle').outerHeight();
        $('#divLog').css("min-height", heightToDisplay+'px');
        $('#divLog').css("max-height", heightToDisplay+'px');
        $('#divLog').show();


    }

    function moveCursorLogDiv()
    {
        let divLog = document.getElementById('divLog');
        divLog.scrollTop = divLog.scrollHeight;
    }

    function resizeLogAction()
    {
        let heightToDisplay = 0;

        if(parseInt($('#nbPlayers').val()) <= 3) {
            //let pbPiocheDiv = $('#piocheDiv').css('padding-bottom');
            //pbPiocheDiv = parseFloat(pbPiocheDiv);

            //let mtDivPlayGlobal = $('#divPlayGlobal').css('margin-top');
            //mtDivPlayGlobal = parseFloat(mtDivPlayGlobal);

            let heightDisplayPlayer = 0;
            $('.display_player').each(function() {
                heightDisplayPlayer = $(this).outerHeight()
            });

            heightToDisplay = $('#centralRow').outerHeight() - heightDisplayPlayer;
        }
        else {
            let mbDivPlayGlobal = $('#divPlayContainer').css('margin-bottom');
            mbDivPlayGlobal = parseFloat(mbDivPlayGlobal);
            heightToDisplay = ($('#centralRow').outerHeight() / 2);
        }

        heightToDisplay = heightToDisplay-2;

        $('#divPlayContainer').css("min-height", heightToDisplay+'px');
        $('#divPlayContainer').css("max-height", heightToDisplay+'px');
        $('#divPlayContainer').show();
    }



    $(document).on('click', '.btn_arrow_top', function(){
        let pos = $(this).data("position");
        let posReplaced = pos-1;
        if(pos != 1) {

            for (let i = 1; i <= 9; i++) {
                if ($('#resolve_' + i + '_' + pos).length) {
                    let htmlPos = $('#resolve_' + i + '_' + pos).html();
                    let htmlPosReplaced = $('#resolve_' + i + '_' + posReplaced).html();
                    $('#resolve_' + i + '_' + pos).html(htmlPosReplaced);
                    $('#resolve_' + i + '_' + posReplaced).html(htmlPos);
                }
            }
        }
    });
    $(document).on('click', '.btn_arrow_bottom', function(){
        let pos = $(this).data("position");
        let posReplaced = pos+1;
        if(pos != 4) {

            for (let i = 1; i <= 9; i++) {
                if ($('#resolve_' + i + '_' + pos).length) {
                    let htmlPos = $('#resolve_' + i + '_' + pos).html();
                    let htmlPosReplaced = $('#resolve_' + i + '_' + posReplaced).html();

                    $('#resolve_' + i + '_' + pos).html(htmlPosReplaced);
                    $('#resolve_' + i + '_' + posReplaced).html(htmlPos);
                }
            }
        }
    });

    $(document).on('change', '.dice_checkbox', function(){
        let position = $(this).data("position");
        if($(this).prop('checked') === true) {
            $('#lock_'+position).show(0);
        }
        else {
            $('#lock_'+position).hide(0);
        }
        let allChecked = true;
        $('.dice_checkbox').each(function() {
            if($(this).prop('checked') === false) allChecked = false;
        });

        let playBtnContent = $('#playBtnContent').html();

        if(allChecked || $('#throwsLeft').html() == '0')
            $('#playBtnContent').html("Résoudre les dés");
        else {
            $('#playBtnContent').html("Relancer les dés");
        }
    });


});