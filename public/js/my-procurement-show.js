$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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

    let expand_unit = $('.more-info')
    let expand_unit_btn = $('.more-info-btn')

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

    let add_vendor_btn = $('.add-vendor-btn')
    let add_vendor_close = $('.add-vendor-close')
    let add_vendor = $('.add-vendor')

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

    let add_item_category = $('.add-item-category')
    let add_item_sub_category = $('.add-item-sub-category')

    $(add_item_category).each(index => {
        $(add_item_category[index]).on('change', function(){
            $(add_item_sub_category[index]).html('<option value="">Mengambil data . . .</option>')
            $(add_item_sub_category[index]).prop('disabled', true)

            $.ajax({
                method: "POST",
                url: "/get-sub-category",
                data: {
                    category: $(add_item_category[index]).val()
                },
                success: function(response){
                    option = `<option value="">Pilih Sub Kategori</option>`
                    for (let i = 0; i < response.length; i++) {
                        option += `<option value="${response[i]['id']}">${response[i]['name']}</option>`
                    }

                    $(add_item_sub_category[index]).html(option)
                    $(add_item_sub_category[index]).prop('disabled', false)
                }
            });
        })
    })

    let upload_spph = $('.upload-spph')
    let spph_form = $('.spph-form')

    $(upload_spph).each(index => {
        $(upload_spph[index]).on('click', function(event){
            $(spph_form[index]).slideToggle()

            event.preventDefault()
        })
    })

    let upload_quotation = $('.upload-quotation')
    let quotation_form = $('.quotation-form')

    $(upload_quotation).each(index => {
        $(upload_quotation[index]).on('click', function(event){
            $(quotation_form[index]).slideToggle()

            event.preventDefault()
        })
    })

    let more_action_btn = $('.more-action-btn')
    let document_action = $('.document-action')
    let more_document = $('.more-document')

    $(more_action_btn).each(index => {
        $(more_action_btn[index]).on('click', function(event){
            if($(more_action_btn[index]).hasClass('btn-danger')){
                $(more_action_btn[index]).removeClass('btn-danger')
                $(more_action_btn[index]).addClass('btn-primary')
            }else{
                $(more_action_btn[index]).removeClass('btn-primary')
                $(more_action_btn[index]).addClass('btn-danger')
            }
            console.log($(document_action[index]))
            $(document_action[index]).slideToggle()
            $(more_document[index]).slideToggle()

            event.preventDefault()
        })
    })
})