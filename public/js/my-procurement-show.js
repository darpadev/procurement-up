$(document).ready(function(){
    $("#uploadDoc").on('click', function(event){
        $(this).fadeToggle(function(){
            $("#uploadDocCancel").fadeToggle("slow")
        })
        $("#uploadDocField").fadeToggle("slow")
        event.preventDefault()
    })
    
    $("#uploadDocCancel").on('click', function(event){
        $(this).fadeToggle(function(){
            $("#uploadDoc").fadeToggle("slow")
        })
        $("#uploadDocField").fadeToggle("slow")
        event.preventDefault()
    })

    $("#newVendor").on('click', function(event){
        $(this).fadeToggle(function(){
            $("#newVendorCancel").fadeToggle("slow")
        })
        $("#newVendorField").fadeToggle("slow")
        event.preventDefault()
    })
    
    $("#newVendorCancel").on('click', function(event){
        $(this).fadeToggle(function(){
            $("#newVendor").fadeToggle("slow")
        })
        $("#newVendorField").fadeToggle("slow")
        event.preventDefault()
    })
})