class ItemDynamicList {

    constructor(elList, id, data = {}, className = '') 
    {
        Object.assign(this, data);
        this.className = className;
        this.elList = elList;
        this.listId = elList.id;
        this.id = id;
    }

    renderTemplate() 
    {
        return '';
    }

    getElementId() 
    {
        return `${this.listId}_${this.id}`;
    }

    getElement() 
    {
        return document.getElementById(this.getElementId());
    }

    render(template='') 
    {
        if (template === '') {
            template = this.renderTemplate();
        }    
        return `<div class="${this.className}" id = "${this.getElementId()}" data-id="${this.id}">${template}</div>`;       
    };

    updateElement(template='') {
        if (!template) {
            template = this.renderTemplate();
        }    
        let el = this.getElement();
        if (el) {
            el.innerHTML = template;
        }

    }
}

class DynamicList {

    constructor(idList, pageSize = 5, urlApi = '/api/list', itemClassName='dl__item') 
    {
        this.elList = document.getElementById(idList);
        this.items = {};
        this.messageId = idList + '_message';
        this.pageSize = pageSize;
        this.countItems = -1;
        this.itemClassName = itemClassName;
        this.mayBeLoad = true;
        this.doUpload = false;
        this.urlApi = urlApi;
        this.addEventListeners();
    }

    addEventListeners() {
        document.addEventListener("scroll", event => {
            this.scrollEvent(event);
        });     
    }

    scrollEvent() {
        if (window.pageYOffset + window.innerHeight + 20 > this.elList.offsetHeight + this.elList.offsetTop) {
            this.getNewPage();
        }
    }

    newItem(id, data) {
        return new ItemDynamicList(this.elList, id, data, this.itemClassName);
    }

    getURLApi(action='getItems') {
        return this.urlApi + '/' + action;
    }

    addItem(id, data) 
    {
        if (!(id in this.items)) {
            this.items[id] = this.newItem(id, data);   
        } else {
            Object.assign(this.items[id], data);      
        }

        return this.items[id];
    }

    deleteItem(id) {
        this.items[id].remove();
        delete this.items[id];
    }


    clearList() 
    {
        this.elList.innerHTML = '';
        this.items = {};
    }
    
    deleteMessage() 
    {
        let el = document.getElementById(this.messageId);
        if (el) {
            el.remove();
        }
    }

    showMessage(message, bottom = true, error = false) 
    {
        this.deleteMessage();
        let style = error ? 'dl__message' : 'dl__message dl__error_message';
        this.elList.insertAdjacentHTML(bottom ? 'beforeend' : 'afterbegin',
        `<p class="${style}" id="${this.messageId}">${message}</p>`);
    }

    async showItems(count, offset) {
        if (!this.doUpload) {
            this.doUpload = true;
            this.showMessage('Загрузка ...');
            let answer = await application.postJson(this.getURLApi(), {'count': count, 'offset': offset});
            if (!answer || answer.totalCount === undefined) {
                this.showMessage('Не удалось загрузить элементы!');
            } else  {
                this.deleteMessage();
                for (let index in answer.items) {
                    let item = answer.items[index];
                    let itemDL = this.addItem(item.id, item);
                    if (itemDL.getElement()) {
                        await itemDL.updateElement();
                    } else {
                        await this.elList.insertAdjacentHTML('beforeend',itemDL.render());
                    }    
                }
                this.countItems = answer.totalCount;
            }
            this.doUpload = false;
        }    
    }

    async getNewPage() {
        let oldCount = Object.keys(this.items).length;
        if (this.mayBeLoad && (this.countItems == -1 || oldCount < this.countItems)) {
            this.mayBeLoad = false;
            await this.showItems(this.pageSize, oldCount);
            if (Object.keys(this.items).length > oldCount) {
                this.mayBeLoad = true;
            }
        }
    } 

    async fillVisible() {
        while ((this.elList.offsetTop + this.elList.offsetHeight < window.innerHeight) && 
            this.mayBeLoad && 
            (this.countItems == -1 || Object.keys(this.items).length < this.countItems)) {
            await this.getNewPage();
        } 
    }
}