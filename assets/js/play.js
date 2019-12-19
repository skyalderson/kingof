$(document).ready(function () {

    let element = document.getElementById('div_log');
    element.scrollTop = element.scrollHeight;

    setInterval(loop, 5000);
    let activeLoop = true;
    if($('#hasAction').val() == '1') activeLoop = false;

    function loop()
    {
        if(activeLoop === true){
            getNewLogs(true, true);
        }
    }

    function hasActionToDo()
    {
        $.ajax({
            url: "/play/hasActionToDo",
            method: "post",
            async: false,
        }).done(function (msg) {
            let hasAction = JSON.parse(msg);
            if(hasAction === true){
                displayActionToDo('active', 0);
                activeLoop = false;
            } else {
                activeLoop = true;
            }
        })
    }

    function getNewLogs(checkNextAction, displayActionBool)
    {
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
                    displayNewLog(i, log.htmlContentLog, log.bgClassLog);
                    element = document.getElementById('div_log');
                    element.scrollTop = element.scrollHeight;
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
                if(displayActionBool === true && last_log.action != 'end_turn') displayActionToDo(mode, log_to_ask);
            }

            if(checkNextAction === true) {
                hasActionToDo();
            }
        })
    }

    function displayActionToDo(mode, idLog)
    {
        $.ajax({
            url: "/play/displayActionToDo",
            method: "post",
            async: false,
            data: {
                idLog: idLog,
                mode: mode
            }

        }).done(function (msg) {

            let response = JSON.parse(msg);
            let htmlBody =  response.playBody;
            let htmlBtn = '';
            if(response.playBtn !== null && mode === 'active') htmlBtn = "<div class='col-xl-12'><button class='btn btn-danger' >"+response.playBtn+"</button></div>";

            $('#playBody').html(htmlBody);
            $('#playBtn').html(htmlBtn);
        })
    }

    function validAction()
    {
        $.ajax({
            url: "/play/validAction",
            method: "post",
            async: false,

        }).done(function (msg) {
            let log = JSON.parse(msg);
            let checkAction = false;
            let displayActionBool= true;
            if(log.action == 'end_turn') {
                checkAction = true;
                displayActionBool = false;
                displayActionToDo('passive', log.idLog + 1);
            }
            getNewLogs(checkAction, displayActionBool);
        })
    }

    // valid Action
    $('#playBtn').click(function() {

        $('#playBody').html('');
        htmlBody ='<div class="spinner-grow spinner-grow-xl text-dark" role="status"><span class="sr-only "></span> </div>';
        $('#playBody').html(htmlBody);
        $('#playBtn').html('');

        setTimeout(validAction, 1000);
    });


    function displayNewLog(idLog, htmlContentLog, bgClassLog)
    {
        $.ajax({
            url: "/play/updateLastSeenLog",
            method: "post",
            async: false,
            data: {
                idLog: idLog,
            }
        }).done(function (msg) {
            bgClassLog = "bg-purple-light";
            $('#div_log').append("<p class='border border-dark p-1 m-0 mb-1 "+bgClassLog+"'>"+htmlContentLog+"</p>");
        })
    }

    function updateData(log)
    {
        switch (log.action)
        {
            case "start_turn":
                break;

            case "end_turn":
                $('#isplaying_name').html(log.isplaying_name);
                $('.card').removeClass('bg-warning').addClass('bg-purple');
                $('#card_'+log.isplaying_id).removeClass('bg-purple').addClass('bg-warning');
                break;
        }
    }

});