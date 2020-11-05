function templateProductItem(item) {
    return `
    <h3><a href="/?c=product&a=card&id=${item['id']}">${item['name']}</a></h3>
    <p>${item['description']}</p>
    <p>Цена: ${item['price']}</p>
    <hr>
    `;
}

$(document).ready(function() {
   
    let catalog = document.getElementById('catalog');
    let page_size = Number(catalog.dataset['page_size']);
    let start_page = Number(page_size);
    let load_new = false;

    load_product();

    jQuery(window).scroll(function() {
        load_product();	
    });	


    function load_product() {
        if (jQuery(window).scrollTop()+jQuery(window).height() < catalog.offsetHeight + catalog.offsetTop || load_new == true) {
            return;
        } 
           
        load_new = true;
        $('#catalog_message').remove(); 
        if (!$('document').is('catalog_message')) {
            catalog.insertAdjacentHTML("beforeend", "<div id='catalog_message' style='padding: 20px'>Идет загрузка...</div>");
        }    

        $.ajax({
            url: "/?c=product&a=apiCatalog&offset=" + start_page,
            type: "GET",
            error: function() {
                message.innerHTML = 'Не удалось загрузить товары из каталога.'
            },
            success: function(answer) {
                let res = JSON.parse(answer);
                if (!res.error) {  
                    for (item in res) {
                        catalog.insertAdjacentHTML("beforeend", templateProductItem(res[item]));                           
                    }                     
                    start_page += page_size;
                    $('#catalog_message').remove();     
                    if (res.length == page_size) {
                        load_new = false;  
                        if (jQuery(window).height() >= catalog.offsetHeight + catalog.offsetTop && load_new == false) {
                            load_product();
                        }	
                    }                
                }
            }				
        })  
    }
    
});