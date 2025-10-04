//     export const Toast = {
//         show(message, options = {}) {
//         
//             const { delay = 3000 } = options;
// 
//             let container = document.querySelector(".toast-container");
//             if (!container) {
//                 container = document.createElement("div");
//                 container.className =
//                     "toast-container position-fixed bottom-0 end-0 p-3 mt-0";
//                 document.body.appendChild(container);
//             }
// 
//             const toast = document.createElement("div");
//             toast.className = `toast bg-white !my-2`;
//             toast.role = "alert";
//             toast.ariaLive = "assertive";
//             toast.ariaAtomic = "true";
//             toast.innerHTML = `
//                 <div class="flex align-items-start justify-content-between">
//                     <div class="toast-body">${message}</div>
//                     <button type="button" class="btn-close m-2 shrink-0" style="margin-top:12px !important;" data-bs-dismiss="toast" aria-label="Close"></button>
//                 </div>
//             `;
//             container.appendChild(toast);
// 
//             // Initialize and show bootstrap toast
//             const bsToast = new bootstrap.Toast(toast, { delay });
//             bsToast.show();
// 
//             // Remove toast from DOM after hidden
//             toast.addEventListener("hidden.bs.toast", () => toast.remove());
//         },
//     };

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
