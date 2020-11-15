class CartItem extends ItemDynamicList {
    renderTemplate() {
        let total = this.quantity * this.product.price;
        return `
            <div>
                <a href="/catalog/${this.product_id}">${this.product.name}</a>
            </div>    
            <div class="cart__item_right">    
                <div>${this.quantity} X ${this.product.price} = ${total} &#8381;</div>
                <div class="cart__item_buttons">
                    <a href="" data-cart_id='${this.id}' data-action='subItem' class='cart-item-edit black-button black-button_sm'>-</a>
                    <a href="" data-cart_id='${this.id}' data-action='addItem' class='cart-item-edit black-button black-button_sm'>+</a>
                    <a href="" data-cart_id='${this.id}' data-action='deleteItem' class='cart-item-edit black-button black-button_sm'>X</a>
                </div>
            </div>
        `;
    }

}

class Cart extends DynamicList {
    constructor(idList, pageSize=10, urlApi='/api/cart', itemClassName='cart__item') {
        super(idList, pageSize, urlApi, itemClassName);
    }

    newItem(id, data) {
        return new CartItem(this.elList, id, data, this.itemClassName);
    }    

}
