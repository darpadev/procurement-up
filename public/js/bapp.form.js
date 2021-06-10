$(document).ready(function(){
    let no_quotation = $('input:checkbox.no-quotation')
    let not_suitable = $('input:checkbox.not-suitable')

    let checked_info = 0
    let generate_btn = $('#generate-bapp')

    $(no_quotation).each(index => {
        $(no_quotation[index]).change(function(){
            if(this.checked){
                checked_info += 1
                if(checked_info == no_quotation.length){
                    $(generate_btn).prop('disabled', false)
                }
                
                $(not_suitable[index]).prop('disabled', true)
            }else{
                checked_info -= 1
                $(generate_btn).prop('disabled', true)
                
                $(not_suitable[index]).prop('disabled', false)
            }
        })
    })

    $(not_suitable).each(index => {
        $(not_suitable[index]).change(function(){
            if(this.checked){
                checked_info += 1
                if(checked_info == no_quotation.length){
                    $(generate_btn).prop('disabled', false)
                }

                $(no_quotation[index]).prop('disabled', true)
            }else{
                checked_info -= 1
                $(generate_btn).prop('disabled', true)
                
                $(no_quotation[index]).prop('disabled', false)
            }
        })
    })
})