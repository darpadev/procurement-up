$(document).ready(function(){
    checkbox = $(':checkbox')
    priceInput = $('.price-input')
    itemId = $('.item-id')

    $(checkbox).each(index => {
        $(checkbox[index]).change(function(){
            if(this.checked){
                $(itemId[index]).prop('disabled', false)
                $(priceInput[index*2]).prop('required', true)
                $(priceInput[index*2]).prop('disabled', false)
                $(priceInput[(index*2) + 1]).prop('required', true)
                $(priceInput[(index*2) + 1]).prop('disabled', false)
            }else{
                $(itemId[index]).prop('disabled', true)
                $(priceInput[index*2]).prop('required', false)
                $(priceInput[index*2]).prop('disabled', true)
                $(priceInput[index*2]).val(() => '')
                $(priceInput[(index*2) + 1]).prop('required', false)
                $(priceInput[(index*2) + 1]).prop('disabled', true)
                $(priceInput[(index*2) + 1]).val(() => '')
            }
        })
    })
})