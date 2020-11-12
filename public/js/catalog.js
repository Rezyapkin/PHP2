class ProductItems extends ItemDynamicList {
    renderTemplate() {
        return `
        <a class="product-item__link" href="/catalog/${this.id}">
             <img class="product_item__img" src="/images/products/${this.image}" alt="${this.name}" >
             <p>${this.name}</p>
        </a>
        <div class="product_item__price">${this.price} &#8381;</div><a href="#" class="black-button btn-buy" data-id="${this.name}">В корзину</a>
        `;
    }

}

class Products extends DynamicList {
    constructor(idList, pageSize=10, urlApi='/api/products', itemClassName='product-item') {
        super(idList, pageSize, urlApi, itemClassName);
    }

    newItem(id, data) {
        return new ProductItems(this.elList, id, data, this.itemClassName);
    }    

}
