const STORAGE_KEY = "unread_notification_count";

function saveToStorage(count) {
    localStorage.setItem(STORAGE_KEY, count);
}

function loadFromStorage() {
    const stored = localStorage.getItem(STORAGE_KEY);
    return stored ? parseInt(stored, 10) : 0;
}

export const notificationState = {
    unread_count: loadFromStorage(),

    async init() {
        try {
            const res = await axios.get("/notification/unread-count", {
                headers: { Accept: "application/json" },
            });

            if (res.data.success) {
                this.unread_count = res.data.data.unread_count;
                saveToStorage(this.unread_count);
            }
        } catch (e) {
            console.error("Failed to load unread count");
        }
    },

    async markAsRead(ids) {
        try {
            // If empty, mark all unread
            const payload = { ids: ids || [] };

            const res = await axios.post("/notification/mark-read", payload, {
                headers: { Accept: "application/json" },
            });

            if (res.data.success) {
                const updatedCount = res.data.data.updated_count || 0;

                // Decrement or set to zero if marking all
                if (!ids || ids.length === 0) {
                    this.unread_count = 0;
                } else {
                    this.unread_count -= updatedCount;
                    if (this.unread_count < 0) this.unread_count = 0;
                }

                saveToStorage(this.unread_count);

            }
        } catch (e) {
            console.error("Could not mark notifications as read");
        }
    },
};
