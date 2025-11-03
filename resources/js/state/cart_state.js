// utils/cartState.js
const STORAGE_KEY = "cart_state";

function loadFromStorage() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
    } catch (e) {
        return {};
    }
}

function saveToStorage(items) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

export const cartState = {
    items: loadFromStorage(),

    get count() {
        return Object.values(this.items).reduce(
            (acc, item) => acc + item.quantity,
            0
        );
    },

    addItem(product, reset = false) {
        if (reset) {
            this.deleteItem(product.id);
        }

        if (this.items[product.id]) {
            let selectedItem = this.items[product.id];
            selectedItem.quantity++;
        } else {
            this.items[product.id] = {
                ...product,
                quantity: 1,
            };
        }
        saveToStorage(this.items);
    },

    removeItem(productId) {
        if (this.items[productId]) {
            this.items[productId].quantity--;
            if (this.items[productId].quantity <= 0) {
                delete this.items[productId];
            }
        }
        saveToStorage(this.items);
    },

    deleteItem(productId) {
        delete this.items[productId];
        saveToStorage(this.items);
    },

    clearCart() {
        this.items = {};
        saveToStorage(this.items);
    },

    totalCost() {
        return Object.values(this.items).reduce(
            (acc, i) => acc + i.price * i.quantity,
            0
        );
    },

    totalItems() {
        return Object.keys(this.items).length;
    },
};
