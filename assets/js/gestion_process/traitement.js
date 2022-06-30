const $ = require('jquery');
global.$ = global.jQuery = $;

let stateTrt = 0;
$("#txt_raisons").hide();
$("#btnEnregistrer").attr('disabled', 'disabled');

$("#validerBtn").click(function(){
    $("#rejeterBtn").removeAttr('disabled');
    $(this).attr('disabled', 'disabled');
    $("#txt_raisons").show();
    $("#yesRd").click();
});

$("#rejeterBtn").click(function(){
    $("#validerBtn").removeAttr('disabled');
    $(this).attr('disabled', 'disabled');
    $("#txt_raisons").show();
    $("#noRd").click();
});

$("#txt_raisons2").keyup(function(){
    if($(this).val().length > 5){
        $("#btnEnregistrer").removeAttr('disabled');
    }else{
        $("#btnEnregistrer").attr('disabled', 'disabled');
    }
});