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

    $(expandBtn).each((index, element) => {
        $(expandBtn[index]).on('click', function(event){
            $(this).fadeToggle(() => {
                $(expandedBtn[index]).fadeToggle()
            })
            $(unitContent[index]).fadeToggle()
            
            event.preventDefault()
        })
    })

    $(closeBtn).each((index, element) => {
        $(closeBtn[index]).on('click', function(event){
            $(expandedBtn[index]).fadeToggle(() =>{
                $(expandBtn[index]).fadeToggle()
            })
            $(unitContent[index]).fadeToggle()

            event.preventDefault()
        })
    })
})