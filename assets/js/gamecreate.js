$(document).ready(function(){
    $('.KoN').css('display', 'none');
    $('.KoT').css('display', 'block');

    $("#game_board").change(function(){
        if($("#game_board").val() == 1)
        {
            $('.KoN').css('display', 'none');
            $('.KoT').css('display', 'block');
        }
        else
        {
            $('.KoT').css('display', 'none');
            $('.KoN').css('display', 'block')
        }
    }); 

    $(".group_monsters").change(function(){
        if(this.checked)
            $("."+$(this).attr('name')).prop('checked', true);
        else
            $("."+$(this).attr('name')).prop('checked', false);      
    }); 

    $("#game_rules_1").change(function(){
        if(this.checked) 
        {
            $(".monstres_bonus").prop('checked', false); 
            $("#monstres_bonus").prop('checked', false); 
            $(".monstres_bonus").prop('disabled', true); 
            $("#monstres_bonus").prop('disabled', true);
            $("#game_mode").val(1);
            $("#mode_2").prop('disabled', false);
            
            
        }
        else{
            $(".monstres_bonus").prop('checked', true); 
            $("#monstres_bonus").prop('checked', true); 
            $(".monstres_bonus").prop('disabled', false); 
            $("#monstres_bonus").prop('disabled', false); 
            $("#game_mode").val(1);
            $("#mode_2").prop('disabled', true);

        }
             
    });
});