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

    // 'cart_items' => 'required|array|min:1',
    // 'cart_items.*.id' => 'required|integer|exists:products,id',
    // 'cart_items.*.name' => 'required|string|max:150',
    // 'cart_items.*.slug' => 'required|string|max:150',
    // 'cart_items.*.price' => 'required|numeric|min:0',
    // 'cart_items.*.quantity' => 'required|integer|min:1',
    // 'cart_items.*.tax' => 'nullable|numeric|min:0',
    // 'cart_items.*.shipping_cost' => 'nullable|numeric|min:0',
    // 'cart_items.*.discount' => 'nullable|numeric|min:0',

    addItem(product) {
        if (this.items[product.id]) {
            let selectedItem = this.items[product.id];
            selectedItem.quantity++;
        } else {
            this.items[product.id] = {
                ...product,
                tax: 20,
                shipping_cost: 40,
                discount: 20,
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
