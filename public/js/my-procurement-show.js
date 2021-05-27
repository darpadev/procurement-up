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

    $('form.add-vendor-form').submit(function(event){
        let data = $('form.add-vendor-form').serializeArray()
        let table_position
        $.each(data, (index, field) => {
            if (field.name == 'item_id'){
                table_position = field.value
            }
        })

        $.ajax({
            type: "POST",
            url: '/item/add-vendor',
            data: $('form.add-vendor-form').serializeArray(),
            success: response => {
                let table = $(`table.quotation-${table_position} > tbody`)
                let table_length = $(`table.quotation-${table_position} > tbody > tr`).length + 1
                table.append(
                    `
                    <tr>
                        <th>${table_length}</th>
                        <td>${response['vendor_name']}</td>
                        <td>${table_length}</td>
                        <td>${table_length}</td>
                    </tr>
                    `
                )
            }
        })

        event.preventDefault()
    })
})