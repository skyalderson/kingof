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

                    
                    var playerStillHere = new Array();
                    $.each(JSON.parse(msg['data']), function(i, player){

                        playerStillHere.push(player.id);
                        if ( $("#row_"+player.id ).length ) {
                            $("#monster_"+player.id).val(player.monster);
            
                            
                            if(player.ready == "0") 
                                $('#ready_'+player.id).html("<span class='badge badge-danger mt-1'>Attente</span>");  
                            else
                                $('#ready_'+player.id).html("<span class='badge badge-success mt-1'>Prêt</span>");
                        }   
                        else 
                        { 
                            var html = "<div id='row_"+player.id+"' class='row justify-content-center row_player' data-playerid='"+player.id+"'>";
                            // OCCURENCE JOUEUR
                            html = html + "<div class='col-xl-1 mt-2 mb-2'><span class='align-middle bg-purple lbl text-yellow text-center col-form-label p-2'>Joueur ?</span></div>";
                            // SI CREATEUR, SINON BOUTON QUITTER
                            html = html + "<div class='col-xl-1 mt-2 mb-2 text-center'><a class='btn btn-danger btn-sm mr-2 mt-0' href='/player/kick/"+player.id+"'>Kick</a></div>";
                            html = html + "<div class='col-xl-1 mt-2 mb-2 text-center'><span>"+player.name+"</span></div>";
                            html = html + "<div class='col-auto'><label class='col-form-label  required' for='lobby_monster'><span class='align-middle bg-purple lbl text-yellow text-center p-2'>Monstre :</span></label></div>";
                            html = html + "<div class='col-xl-3'><input type='text' id='monster_"+player.id+"' name='monster_"+player.id+"' readonly='readonly' disabled='disabled' value='"+player.monster+"' class='form-control'></div>";
                            html = html + "<div class='col-xl-1 pl-0'><h3 id='ready_'"+player.id+">";
                            if(player.ready == "1") {html = html + "<span class='badge badge-success mt-1'>Prêt</span>";} else {html = html + "<span class='badge badge-danger mt-1'>Attente</span>";}
                            html = html + "</h3></div>";
                            html = html + "</div>";
                            $("#list").append(html);
                        }     
                    })

                    var rows = $(".row_player").each(function(){
                        if(playerStillHere .indexOf($(this).data("playerid")) == -1 && $(this).data("playerid") != $("#data").data("playerid") ){
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
            if(msg == "taken"){
                $('#modal_msg').html("Ce monstre a déjà été sélectionné par un autre joueur.");
                $('#modal').modal();
                $("#lobby_monster_self").val("0");
                $('#lobby_ready_self').bootstrapToggle('off');   
            }
        })
    }
    
    $("#lobby_ready_self").change(function (){ 
        if(document.getElementById('lobby_ready_self').checked && $("#lobby_monster_self").val() == "0"){
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