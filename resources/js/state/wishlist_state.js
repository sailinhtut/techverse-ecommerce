// utils/cartState.js
const STORAGE_KEY = "wishlist_state";

function loadFromStorage() {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(decodeBase64Unicode(stored)) : [];
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

export const wishlistState = {
    wishlist_ids: loadFromStorage(),

    isWishlist(productId) {
        return this.wishlist_ids.includes(productId);
    },

    async syncWishlistProducts() {
        try {
            const response = await axios.get("/wishlist/api", {
                headers: { Accept: "application/json" },
            });

            if (response.data.success) {
                this.wishlist_ids = response.data.data.wishlist_ids;
                saveToStorage(this.wishlist_ids);
                // Toast.show("Synced Wishlist Products");
            }
        } catch (error) {
            const msg =
                error.response?.data?.message ||
                "Failed to add product to wishlist.";
        }
    },

    async addWishlist(productId) {
        try {
            const response = await axios.post(
                "/wishlist",
                {
                    product_id: productId,
                    note: `Added on ${new Date().toLocaleString()}`,
                },
                {
                    headers: { Accept: "application/json" },
                }
            );

            if (response.data.success) {
                this.wishlist_ids = response.data.data.wishlist_ids;
                saveToStorage(this.wishlist_ids);
                Toast.show(response.data.message);
            } else {
                Toast.show(
                    response.data.message ||
                        "Failed to remove wishlist product",
                    {
                        type: "error",
                    }
                );
            }
        } catch (error) {
            console.log(error);
            const msg =
                error.response?.data?.message ||
                "Failed to add product to wishlist.";
            Toast.show(msg, { type: "error" });
        }
    },

    async removeWishlist(productId, variant_id = null) {
        try {
            const response = await axios.delete(`/wishlist/${productId}`, {
                headers: { Accept: "application/json" },
            });

            if (response.data.success) {
                this.wishlist_ids = response.data.data.wishlist_ids;
                saveToStorage(this.wishlist_ids);
                Toast.show(response.data.message);
            } else {
                Toast.show(
                    response.data.message ||
                        "Failed to remove wishlist product",
                    {
                        type: "error",
                    }
                );
            }
        } catch (error) {
            const msg =
                error.response?.data?.message ||
                "Failed to add product to wishlist.";

            Toast.show(msg, { type: "error" });
        }
    },
};
