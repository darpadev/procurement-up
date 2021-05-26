$(document).ready(function(){
    $('#item-tab').on('click', function(event){
        $('.tab-active').removeClass('tab-active active')
        $('#item-tab').addClass('tab-active active')
        $('.content-active').fadeToggle(() => {
            $('#item-content').fadeToggle()
            $('.content-active').removeClass('content-active')
            $('#item-content').addClass('content-active')
        })

        event.preventDefault()
    })

    $('#bidder-tab').on('click', function(event){
        $('.tab-active').removeClass('tab-active active')
        $('#bidder-tab').addClass('tab-active active')
        $('.content-active').fadeToggle(() => {
            $('#bidder-content').fadeToggle()
            $('.content-active').removeClass('content-active')
            $('#bidder-content').addClass('content-active')
        })

        event.preventDefault()
    })

    $('#log-tab').on('click', function(event){
        $('.tab-active').removeClass('tab-active active')
        $('#log-tab').addClass('tab-active active')
        $('.content-active').fadeToggle(() => {
            $('#log-content').fadeToggle()
            $('.content-active').removeClass('content-active')
            $('#log-content').addClass('content-active')
        })

        event.preventDefault()
    })

    expand_unit = $('.more-info')
    expand_unit_btn = $('.more-info-btn')

    $(expand_unit_btn).each(index => {
        $(expand_unit_btn[index]).on('click', function(event){
            $(expand_unit[index]).slideToggle()
            if($(expand_unit_btn[index]).hasClass('text-danger')){
                $(expand_unit_btn[index]).removeClass('text-danger')
            }else{
                $(expand_unit_btn[index]).addClass('text-danger')
            }
            event.preventDefault()
        })
    })

    add_vendor_btn = $('.add-vendor-btn')
    add_vendor_close = $('.add-vendor-close')
    add_vendor = $('.add-vendor')

    $(add_vendor_btn).each(index => {
        $(add_vendor_btn[index]).on('click', function(event){
            $(add_vendor[index]).slideDown()
            
            event.preventDefault()
        })
    })

    $(add_vendor_close).each(index => {
        $(add_vendor_close[index]).on('click', function(event){
            $(add_vendor[index]).slideUp()
            
            event.preventDefault()
        })
    })
})