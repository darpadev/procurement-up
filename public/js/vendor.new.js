$(document).ready(function(){
    $('#bank_account').keypress(() => {
        if ( ($('#bank_account').val().length + 1) % 5 == 0 ){
            $('#bank_account').val( $('#bank_account').val() + '-' )
        }
    })
})