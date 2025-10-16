export const Toast = {
    show(message, options = {}) {
        const { type = "info", delay = 3000 } = options;

        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className =
                "toast-container fixed bottom-4 right-4 flex flex-col gap-2 z-[9999]";
            document.body.appendChild(container);
        }

        // Choose style based on type
        const colors = {
            success: "alert-success",
            error: "alert-error",
            warning: "alert-warning",
            info: "alert-info",
        };

        const toast = document.createElement("div");
        toast.className = `alert ${colors[type] || colors.info} shadow-lg flex justify-between items-center`;
        toast.innerHTML = `
            <span class="flex-1">${message}</span>
            <button class="btn btn-sm btn-ghost ml-2">✕</button>
        `;

        // Add toast
        container.appendChild(toast);

        // Close button
        toast.querySelector("button").addEventListener("click", () => {
            toast.remove();
        });

        // Auto-remove
        if (delay > 0) {
            setTimeout(() => toast.remove(), delay);
        }
    },
};
