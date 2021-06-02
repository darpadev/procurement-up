$(document).ready(function(){
    let checkbox = $(':checkbox')
    let item_id = $('.item_id')
    let item_note = $(':text.item-note')
    let item_unit = $(':text.item-unit')

    $(checkbox).each(index => {
        $(checkbox[index]).change(function(){
            if(this.checked){
                $(item_id[index]).prop('disabled', false)
                console.log($(item_id[index]))
                $(item_unit[index]).prop('required', true)
                $(item_note[index]).slideToggle()
                $(item_unit[index]).slideToggle()
            }else{
                $(item_id[index]).prop('disabled', true)
                $(item_unit[index]).prop('required', false)
                $(item_note[index]).slideToggle(() => {
                    $(item_note[index]).val(() => '')
                })
                $(item_unit[index]).slideToggle(() => {
                    $(item_unit[index]).val(() => '')
                })
            }
        })
    })
})