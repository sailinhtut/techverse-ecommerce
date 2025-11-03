export const Toast = {
    show(message, options = {}) {
        const { type = "success", delay = 3000 } = options;

        // Toast container
        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className =
                "toast-container fixed bottom-4 right-4 flex flex-col gap-2 z-[9999]";
            document.body.appendChild(container);
        }

        // Remove any existing toast (only one visible)
        container.innerHTML = "";

        // Colors / icons per type
        const colors = {
            success: {
                bg: "bg-black",
                text: "text-white",
                icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="h-6 w-6 shrink-0 stroke-success">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`,
            },
            error: {
                bg: "bg-black",
                text: "text-red-500",
                icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                    class="h-6 w-6 shrink-0 stroke-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 
                        1.948 3.374h14.71c1.73 0 2.813-1.874 
                        1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 
                        0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>`,
            },
            warning: {
                bg: "bg-black",
                text: "text-amber-500",
                icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" class="h-6 w-6 shrink-0 stroke-amber-500">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 
                        11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 
                        9 11.622 5.176-1.332 9-6.03 9-11.622 
                        0-1.31-.21-2.57-.598-3.75h-.152c-3.196 
                        0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                </svg>`,
            },
            info: {
                bg: "bg-black",
                text: "text-blue-500",
                icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" class="h-6 w-6 shrink-0 stroke-blue-500">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 
                        0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>`,
            },
        };

        const c = colors[type] || colors.info;

        // Create toast
        const toast = document.createElement("div");
        toast.className = `alert flex justify-between items-center shadow-lg border rounded-lg 
            ${c.bg} ${c.text} transition-all`;
        toast.innerHTML = `
            <div class="flex items-center gap-2 flex-1">
                ${c.icon}
                <span>${message}</span>
            </div>
            <button type="button" class="btn btn-xs btn-circle ml-2">✕</button>
        `;

        // Add toast
        container.appendChild(toast);

        // Close button
        toast.querySelector("button").addEventListener("click", () => toast.remove());

        // Auto-remove
        if (delay > 0) {
            setTimeout(() => toast.remove(), delay);
        }
    },
};
