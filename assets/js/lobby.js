$(document).ready(function(){

    setInterval(verifGame, 5000);
    
    function verifGame(){ 
        $.ajax({
            url:"/lobby/data",
            method: "post",
            data: { 
                idGame: $("#data").data("gameid"),   
                idPlayer: $("#data").data("playerid")    
            }
        }).done(function(msg){         
            updateData(msg);
        })     
    }

    function updateData(msg)
    {
        var exists = JSON.parse(msg['exists']);
        var kicked = JSON.parse(msg['kicked']);
        var waiting = JSON.parse(msg['waiting']); 

        if(exists === true){
            
            if(kicked === false){
                
                if(waiting === true){

                    var playerStillHere = [];
                    $.each(JSON.parse(msg['data']), function(i, player){

                        playerStillHere.push(player.id);
                        var nbHere = playerStillHere.length + 1;

                        if ( $("#row_"+player.id ).length ) {
                            $("#monster_"+player.id).val(player.monster);
            
                            
                            if(player.ready === false)
                                $('#ready_'+player.id).html("<span class='badge badge-danger mt-1'>Attente</span>");  
                            else
                                $('#ready_'+player.id).html("<span class='badge badge-success mt-1'>Prêt</span>");
                        }   
                        else 
                        {
                            var html = "<div id='row_"+player.id+"' class='row justify-content-center row_player mb-2' data-playerid='"+player.id+"'>";
                            // OCCURENCE JOUEUR
                            html = html + "<div class='col-auto mt-2 mb-2'><span class='align-middle bg-purple lbl text-yellow text-center col-form-label'>Joueur "+nbHere+" :</span></div>";
                            html = html + "<div class='col-xl-1 col-lg-1 col-md-1 text-center'><span>"+player.name+"</span></div>";
                            html = html + "<div class=\"col-auto\"><label class=\"col-form-label  required\" for=\"lobby_monster\"><span class=\"align-middle bg-purple lbl text-yellow text-center\">Monstre :</span></label> </div>";
                            html = html + "<div class='col-xl-3 col-lg-3 col-md-3'><input type='text' id='monster_"+player.id+"' name='monster_"+player.id+"' readonly='readonly' disabled='disabled' value='"+player.monster+"' class='form-control'></div>";
                            html = html + "<div class='col-xl-1 col-lg-1 col-md-1text-center'><span id='ready_"+player.id+"' style='font-size:1.5rem;'>";
                            if(player.ready === true) {html = html + "<span class=\"badge badge-success\">Prêt</span>";} else {html = html + "<span class=\"badge badge-danger\">Attente</span>";}
                            html = html + "</span></div>";

                            if ($("#data").data("playerid") === $("#data").data("creatorid")){
                                html = html + "<div class='col-xl-1 col-lg-1 col-md-1 text-right'><a class='btn btn-danger btn-sm' href='/player/kick/"+player.id+"'>Kick</a></div>";
                            }
                            html = html + "</div>";

                            $("#list").append(html);

                            nbHere = +nbHere +1;

                        }     
                    })

                    var rows = $(".row_player").each(function(){
                        if(playerStillHere .indexOf($(this).data("playerid")) === -1 && $(this).data("playerid") !== $("#data").data("playerid") ){
                            $(this).remove();    
                        }
                        
                      }); 
                }   
                else{
                    alert("La partie a commencée !"); 
                }
            }
            else{
                $('#modal_msg').html("Vous avez été exclu de la partie.");          
                $('#modal').modal();
                setTimeout(function() {window.location.replace("/game/list");}, 2000);    
            }     
        }
        else{
            $('#modal_msg').html("La partie a été annulée par son créateur.");
            $('#modal').modal();
            setTimeout(function() {window.location.replace("/game/list");}, 2000); 
        }  
    }

    $("#lobby_monster_self").change(function (){ 
        selectMonster();
    });
    
    function selectMonster(){       
        $.ajax({
            url:"/lobby/select/monster",
            method: "post",
            data: { 
                idGame: $("#data").data("gameid"),
                idPlayer: $("#data").data("playerid"),
                idMonster: $("#lobby_monster_self").val()
         }
        }).done(function(msg){
            if(msg === "taken"){
                $('#modal_msg').html("Ce monstre a déjà été sélectionné par un autre joueur.");
                $('#modal').modal();
                $("#lobby_monster_self").val("0");
                $('#lobby_ready_self').bootstrapToggle('off');   
            }
        })
    }
    
    $("#lobby_ready_self").change(function (){ 
        if(document.getElementById('lobby_ready_self').checked && $("#lobby_monster_self").val() === "0"){
            $('#lobby_ready_self').bootstrapToggle('off');  
            $('#modal_msg').html("Vous devez d'abord choisir un monstre.");
            $('#modal').modal();         
        }
        else{
            readyState();
        }    
    });

    function readyState()
    {
        var isReady = document.getElementById('lobby_ready_self').checked;

        $.ajax({
            url:"/lobby/ready",
            method: "post",
            data: { 
                idPlayer: $("#data").data("playerid"),    
                ready: isReady 
            }
        }).done(function(msg){
        })
    }
});