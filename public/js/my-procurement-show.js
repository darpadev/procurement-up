$(document).ready(function(){
    $('#unitLists_btn').on('click', function(event){
        $('#logs_btn').removeClass('active')
        $('#logs').fadeToggle(() => {
            $('#unitLists').fadeToggle()
        })
        
        $('#unitLists_btn').addClass('active')

        event.preventDefault()
    })

    $('#logs_btn').on('click', function(event){
        $('#unitLists_btn').removeClass('active')
        $('#unitLists').fadeToggle(() => {
            $('#logs').fadeToggle()
        })

        $('#logs_btn').addClass('active')

        event.preventDefault()
    })

    expandBtn = $('.expand-unit-btn')
    expandedBtn = $('.expanded-unit-btn')
    closeBtn = $('.close-unit-btn')
    unitContent = $('.unit-content')

    $(expandBtn).each(index => {
        $(expandBtn[index]).click(function(event){
            $(this).fadeToggle(() => {
                $(expandedBtn[index]).fadeToggle()
            })
            $(unitContent[index]).slideToggle()
            
            event.preventDefault()
        })
    })

    $(closeBtn).each(index => {
        $(closeBtn[index]).click(function(event){
            $(expandedBtn[index]).fadeToggle(() =>{
                $(expandBtn[index]).fadeToggle()
            })
            $(unitContent[index]).slideToggle()

            event.preventDefault()
        })
    })

    quotationBtn = $('.quotation-btn')
    quotationNum = []
    closeQuotationBtn = $('.close-quotation-btn')
    quotationContent = $('.quotation-content')

    quotationBtn.each(index => {
        quotationNum.push($(quotationBtn[index]).find($('span.badge')).text())

        $(quotationBtn[index]).mouseenter(function(event){
            $(this).html('Lihat Penawaran')
            event.preventDefault()
        })

        $(quotationBtn[index]).mouseleave(function(event){
            $(this).html(`Quotation Available: <span class="badge badge-light">${quotationNum[index]}</span>`)
            event.preventDefault()
        })

        $(quotationBtn[index]).click(event => {
            $(quotationContent[index]).slideToggle()
            $(quotationBtn[index]).fadeToggle()
            event.preventDefault()
        })
    })

    console.log($(quotationNum))

    closeQuotationBtn.each(index => {
        $(closeQuotationBtn[index]).click(event => {
            $(quotationContent[index]).slideToggle()
            $(quotationBtn[index]).fadeToggle()
            event.preventDefault()
        })
    })
})