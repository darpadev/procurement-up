$(document).ready(function(){
    vendor_header = $('a.vendor-link')
    vendor_detail = $('div.vendor-detail')

    $(vendor_header).each(index => {
        $(vendor_header[index]).click(function(event){
            $(vendor_detail[index]).slideToggle()
            event.preventDefault()
        })
    })
    
    sub_content = $('.sub-content')
    sub_name = $('.sub-name')

    $(sub_name).each(index => {
        $(sub_name[index]).click(function(event){
            $(sub_content[index]).find($('div.vendor-container')).slideToggle(() => {
                $(sub_content[index]).find($('div.vendor-detail')).slideUp()
            })
            event.preventDefault()
        })
    })
})