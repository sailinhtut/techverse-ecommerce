// utils/cartState.js
const STORAGE_KEY = "cart_state";

function loadFromStorage() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if(!stored) return [];
        const list = JSON.parse(decodeBase64Unicode(stored)) ?? [];
        return list;
    } catch (e) {
        return {};
    }
}

function saveToStorage(items) {
    const encoded = encodeBase64Unicode(JSON.stringify(items));
    localStorage.setItem(STORAGE_KEY, encoded);
}

function encodeBase64Unicode(str) {
    return btoa(unescape(encodeURIComponent(str)));
}

function decodeBase64Unicode(str) {
    return decodeURIComponent(escape(atob(str)));
}

export const cartState = {
    items: loadFromStorage() ?? [],

    get count() {
        return this.items.reduce((acc, item) => acc + item.quantity, 0);
    },

    getQuantity(productId, variantId = null) {
        const item = this.items.find(
            (i) =>
                i.product_id == productId &&
                (variantId ? i.variant_id == variantId : true)
        );
        return item ? item.quantity : 0;
    },

    getUnitPrice(productId, variantId = null) {
        const item = this.items.find(
            (i) =>
                i.product_id == productId &&
                (variantId ? i.variant_id == variantId : true)
        );
        return item ? item.price : 0;
    },

    getSubtotal(productId, variantId = null) {
        const item = this.items.find(
            (i) =>
                i.product_id == productId &&
                (variantId ? i.variant_id == variantId : true)
        );
        return item ? item.subtotal : 0;
    },

    async syncCartItems() {
        try {
            const response = await axios.get("/cart/items", {
                headers: { Accept: "application/json" },
            });

            if (response.data.success) {
                this.items = response.data.data.cart.items ?? [];
                saveToStorage(this.items);
                // Toast.show("Synced Cart Items");
            }
        } catch (error) {
            const msg =
                error.response?.data?.message || "Failed to add item to cart.";
        }
    },

    async addItem(product) {
        try {
            const response = await axios.post(
                "/cart/add",
                {
                    product_id: product.product_id,
                    variant_id: product.variant_id ?? null,
                    variant_combination: product.variant_combination ?? null,
                    quantity: 1,
                },
                {
                    headers: { Accept: "application/json" },
                }
            );

            if (response.data.success) {
                this.items = response.data.data.cart.items ?? [];
                saveToStorage(this.items);
                Toast.show(response.data.message);
            } else {
                Toast.show(response.data.message || "Failed to remove item", {
                    type: "error",
                });
            }
        } catch (error) {
            const msg =
                error.response?.data?.message || "Failed to add item to cart.";
            Toast.show(msg, { type: "error" });
        }
    },

    async removeByProductId(productId, variantId = null) {
        const item = this.items.find(
            (i) =>
                i.product_id == productId &&
                (variantId ? i.variant_id == variantId : true)
        );
        await this.removeItem(item.id);
    },

    async removeItem(cartItemId) {
        try {
            console.log(cartItemId);
            const response = await axios.post(
                "/cart/remove",
                { item_id: cartItemId, quantity: 1 },
                { headers: { Accept: "application/json" } }
            );

            if (response.data.success) {
                this.items = response.data.data.cart.items ?? [];
                saveToStorage(this.items);
                Toast.show(response.data.message);
            } else {
                Toast.show(response.data.message || "Failed to remove item", {
                    type: "error",
                });
            }
        } catch (error) {
            console.log(error);
            const msg =
                error.response?.data?.message ||
                "Something went wrong while removing item.";
            Toast.show(msg, { type: "error" });
        }
    },

    async deleteItem(productId) {
        try {
            const response = await axios.post(
                "/cart/remove",
                { item_id: productId },
                { headers: { Accept: "application/json" } }
            );

            if (response.data.success) {
                this.items = response.data.data.cart.items ?? [];
                saveToStorage(this.items);
                Toast.show(response.data.message);
            } else {
                Toast.show(response.data.message || "Failed to delete item", {
                    type: "error",
                });
            }
        } catch (error) {
            const msg =
                error.response?.data?.message ||
                "Something went wrong while deleting item.";
            Toast.show(msg, { type: "error" });
        }
    },

    async clearCart() {
        try {
            const response = await axios.post(
                "/cart/clear",
                {},
                { headers: { Accept: "application/json" } }
            );

            if (response.data.success) {
                this.items = [];
                saveToStorage([]);
                Toast.show(response.data.message);
            } else {
                Toast.show(response.data.message || "Failed to clear cart", {
                    type: "error",
                });
            }
        } catch (error) {
            const msg =
                error.response?.data?.message ||
                "Something went wrong while clearing cart.";
            Toast.show(msg, { type: "error" });
        }
    },

    totalCost() {
        return this.items.reduce((acc, i) => acc + i.price * i.quantity, 0);
    },

    totalItems() {
        return this.items.length;
    },
};
