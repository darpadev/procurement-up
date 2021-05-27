$(document).ready(function(){
    more_info_btn = $('.more-info-btn')
    more_info = $('.more-info')

    $(more_info_btn).each(index => {
        $(more_info_btn[index]).on('click', function(event){
            if ( $(more_info_btn[index]).hasClass('badge badge-danger') ){
                $(more_info_btn[index]).removeClass('badge badge-danger')
                $(more_info_btn[index]).html(`<i class="fas fa-fw fa-caret-square-down"></i>`)
            }else{
                $(more_info_btn[index]).addClass('badge badge-danger')
                $(more_info_btn[index]).html('&times;')
            }

            $(more_info[index]).slideToggle()

            event.preventDefault()
        })
    })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    add_category = $('.add-category')
    add_sub_category = $('.add-sub-category')

    $(add_category).each(index => {
        $(add_category[index]).on('change', function(){
            $(add_sub_category[index]).html('<option value="">Mengambil data . . .</option>')
            $(add_sub_category[index]).prop('disabled', true)

            $.ajax({
                method: "POST",
                url: "/get-sub-category",
                data: {
                    category: $(add_category[index]).val()
                },
                success: function(response){
                    option = `<option value="">Pilih Sub Kategori</option>`
                    for (let i = 0; i < response.length; i++) {
                        option += `<option value="${response[i]['id']}">${response[i]['name']}</option>`
                    }

                    $(add_sub_category[index]).html(option)
                    $(add_sub_category[index]).prop('disabled', false)
                }
            });
        })
    })
})
