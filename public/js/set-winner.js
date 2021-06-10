$(document).ready(function(){
    let item_checkbox   = $('input:checkbox')
    let bidder_price    = $('.bidder-price') 
    let final_price     = $('.final-price')
    let discount_price     = $('.discount-price')

    $(bidder_price).each(index => {
        $(bidder_price[index]).change(function(){
            if($(final_price[index]).val()){
                let discount = new Number($(bidder_price[index]).val() - $(final_price[index]).val()).toLocaleString('id-ID')
                $(discount_price[index]).html(discount)
            }
        })
    })

    $(final_price).each(index => {
        $(final_price[index]).change(function(){
            if($(bidder_price[index]).val()){
                let discount = new Number($(bidder_price[index]).val() - $(final_price[index]).val()).toLocaleString('id-ID')
                $(discount_price[index]).html(discount)
            }
        })
    })
})