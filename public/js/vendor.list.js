$(document).ready(function(){
    category_child = $('div.category-content')

    $(category_child).each((index) => {
        $(category_child[index]).click(event => {
            $(category_child[element]).children('.sub-content').slideToggle()
            event.preventDefault()
        })
    })
})