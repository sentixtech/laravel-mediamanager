class MediaUI {
    constructor() {
        window.openModal =
            window.openModal ||
            function (id) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.remove(
                    "hidden",
                    "opacity-0",
                    "scale-95",
                    "pointer-events-none",
                );
                modal.classList.add(
                    "opacity-100",
                    "scale-100",
                    "pointer-events-auto",
                );
                modal.setAttribute("aria-hidden", "false");
            };

        window.closeModal =
            window.closeModal ||
            function (id) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.add(
                    "hidden",
                    "opacity-0",
                    "scale-95",
                    "pointer-events-none",
                );
                modal.classList.remove(
                    "opacity-100",
                    "scale-100",
                    "pointer-events-auto",
                );
                modal.setAttribute("aria-hidden", "true");
            };
        this.initNotificationContainer();
    }

    initNotificationContainer() {
        if (!document.getElementById("notification-container")) {
            const container = document.createElement("div");
            container.id = "notification-container";
            container.className =
                "fixed z-[9999] flex flex-col gap-3 pointer-events-none";
            container.style.cssText = `
                bottom: 24px;
                right: 24px;
                max-width: 400px;
                width: 100%;
                z-index: 9999;
            `;
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = "info", options = {}) {
        const {
            duration = 4000,
            title = "",
            icon = true,
            dismissible = true,
            onClose = null,
            position = "bottom-right",
        } = options;

        const container = this.getNotificationContainer(position);

        const notification = document.createElement("div");
        notification.className =
            "notification-item pointer-events-auto transform transition-all duration-300 ease-out";

        const colors = this.getNotificationColors(type);

        const iconHtml = icon ? this.getNotificationIcon(type) : "";

        notification.innerHTML = `
            <div class="relative overflow-hidden rounded-2xl shadow-2xl border ${colors.border} bg-white dark:bg-gray-800 backdrop-blur-sm" 
                 style="box-shadow: 0 20px 60px -15px rgba(0,0,0,0.3);">
                <!-- Progress bar -->
                <div class="absolute bottom-0 left-0 h-1 ${colors.progress} transition-all duration-${duration} ease-linear" 
                     style="width: 100%; z-index: 1;"></div>
                
                <div class="flex items-start p-4 gap-3">
                    ${iconHtml ? `<div class="flex-shrink-0 mt-0.5">${iconHtml}</div>` : ""}
                    
                    <div class="flex-1 min-w-0">
                        ${title ? `<h4 class="text-sm font-semibold ${colors.text} mb-0.5">${title}</h4>` : ""}
                        <p class="text-sm ${colors.text} opacity-90 break-words leading-relaxed">${message}</p>
                    </div>
                    
                    ${
                        dismissible
                            ? `
                        <button class="flex-shrink-0 ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-all duration-200 hover:scale-110">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    `
                            : ""
                    }
                </div>
            </div>
        `;

        container.appendChild(notification);

        requestAnimationFrame(() => {
            notification.style.opacity = "1";
            notification.style.transform = "translateY(0)";
        });

        if (dismissible) {
            const dismissBtn = notification.querySelector("button");
            dismissBtn.addEventListener("click", () => {
                this.dismissNotification(notification, onClose);
            });
        }

        notification.addEventListener("click", (e) => {
            if (e.target === notification || e.target.closest(".flex-1")) {
                this.dismissNotification(notification, onClose);
            }
        });

        let timeoutId = setTimeout(() => {
            this.dismissNotification(notification, onClose);
        }, duration);

        notification.addEventListener("mouseenter", () => {
            clearTimeout(timeoutId);
            const progressBar =
                notification.querySelector(".absolute.bottom-0");
            if (progressBar) {
                progressBar.style.animationPlayState = "paused";
                progressBar.style.width = progressBar.style.width || "100%";
            }
            notification.style.transform = "scale(1.02)";
            notification.style.boxShadow = "0 25px 70px -15px rgba(0,0,0,0.4)";
        });

        notification.addEventListener("mouseleave", () => {
            const progressBar =
                notification.querySelector(".absolute.bottom-0");
            if (progressBar) {
                progressBar.style.animationPlayState = "running";
            }
            notification.style.transform = "scale(1)";
            notification.style.boxShadow = "0 20px 60px -15px rgba(0,0,0,0.3)";

            timeoutId = setTimeout(() => {
                this.dismissNotification(notification, onClose);
            }, duration);
        });

        return notification;
    }

    getNotificationContainer(position) {
        const positionMap = {
            "bottom-right": {
                bottom: "24px",
                right: "24px",
                top: "auto",
                left: "auto",
            },
            "bottom-left": {
                bottom: "24px",
                right: "auto",
                top: "auto",
                left: "24px",
            },
            "top-right": {
                bottom: "auto",
                right: "24px",
                top: "24px",
                left: "auto",
            },
            "top-left": {
                bottom: "auto",
                right: "auto",
                top: "24px",
                left: "24px",
            },
            "bottom-center": {
                bottom: "24px",
                right: "auto",
                top: "auto",
                left: "50%",
                transform: "translateX(-50%)",
            },
            "top-center": {
                bottom: "auto",
                right: "auto",
                top: "24px",
                left: "50%",
                transform: "translateX(-50%)",
            },
        };

        const pos = positionMap[position] || positionMap["bottom-right"];
        const containerId = `notification-container-${position}`;
        let container = document.getElementById(containerId);

        if (!container) {
            container = document.createElement("div");
            container.id = containerId;
            container.className =
                "fixed z-[9999] flex flex-col gap-3 pointer-events-none";
            container.style.cssText = `
                bottom: ${pos.bottom};
                right: ${pos.right};
                top: ${pos.top};
                left: ${pos.left};
                ${pos.transform ? `transform: ${pos.transform};` : ""}
                max-width: 420px;
                width: 100%;
                z-index: 9999;
            `;
            document.body.appendChild(container);
        }

        return container;
    }

    getNotificationColors(type) {
        const colors = {
            success: {
                border: "border-emerald-500/30",
                text: "text-emerald-800 dark:text-emerald-100",
                progress: "bg-emerald-500",
                bg: "bg-emerald-50/95 dark:bg-emerald-900/40",
            },
            error: {
                border: "border-red-500/30",
                text: "text-red-800 dark:text-red-100",
                progress: "bg-red-500",
                bg: "bg-red-50/95 dark:bg-red-900/40",
            },
            warning: {
                border: "border-amber-500/30",
                text: "text-amber-800 dark:text-amber-100",
                progress: "bg-amber-500",
                bg: "bg-amber-50/95 dark:bg-amber-900/40",
            },
            info: {
                border: "border-blue-500/30",
                text: "text-blue-800 dark:text-blue-100",
                progress: "bg-blue-500",
                bg: "bg-blue-50/95 dark:bg-blue-900/40",
            },
        };
        return colors[type] || colors.info;
    }

    getNotificationIcon(type) {
        const icons = {
            success: `<div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>`,
            error: `<div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>`,
            warning: `<div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>`,
            info: `<div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>`,
        };
        return icons[type] || icons.info;
    }

    dismissNotification(notification, onClose) {
        if (!notification || !notification.parentNode) return;

        notification.style.opacity = "0";
        notification.style.transform = "translateY(20px) scale(0.95)";
        notification.style.transition = "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)";

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
                if (onClose && typeof onClose === "function") {
                    onClose();
                }
            }
        }, 300);
    }

    clearAllNotifications() {
        const containers = document.querySelectorAll(
            '[id^="notification-container-"]',
        );
        containers.forEach((container) => {
            const notifications =
                container.querySelectorAll(".notification-item");
            notifications.forEach((notification) => {
                this.dismissNotification(notification);
            });
        });
    }

    previewMedia(id, type, url) {
        $("#mediaPreviewContent").html(`
        <div class="flex justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    `);

        openModal("mediaPreviewModal");

        setTimeout(() => {
            const cleanUrl = url.split("?")[0];
            const ext = cleanUrl.split(".").pop().toLowerCase();

            const config = window.MediaManagerConfig?.config || {};

            let detectedType = "file";
            for (const [t, settings] of Object.entries(config)) {
                if (settings?.extensions?.includes(ext)) {
                    detectedType = t;
                    break;
                }
            }

            const viewer = this.getViewerByType(detectedType, url);

            $("#mediaPreviewContent").html(viewer);
        }, 400);
    }
    getViewerByType(type, url) {
        switch (type) {
            case "image":
                return `
                <img src="${url}" class="max-w-full max-h-[70vh] mx-auto object-contain rounded-lg">
            `;

            case "video":
                return `
                <video controls class="max-w-full max-h-[70vh] mx-auto rounded-lg">
                    <source src="${url}">
                </video>
            `;

            case "document":
                return `
                <iframe src="${url}" class="w-full h-[70vh] rounded-lg"></iframe>
            `;

            case "spreadsheet":
            case "presentation":
                const viewerUrl = `https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true`;
                return `
                <iframe src="${viewerUrl}" class="w-full h-[70vh] rounded-lg"></iframe>
            `;

            default:
                return `
                <div class="text-center py-12">
                    <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-4">Preview not available</p>
                    <a href="${url}" download 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>
                </div>
            `;
        }
    }

    getFileTypeFromUrl(url) {
        const ext = url.split(".").pop().toLowerCase();

        const config = window.MediaManagerConfig?.config || {};

        for (const [type, settings] of Object.entries(config)) {
            if (!settings?.extensions) continue;

            if (settings.extensions.includes(ext)) {
                return type;
            }
        }

        return "file";
    }

    copyLink(url) {
        navigator.clipboard
            .writeText(url)
            .then(() => {
                this.showNotification("Link copied to clipboard!", "success");
            })
            .catch(() => {
                this.showNotification("Failed to copy link", "error");
            });
    }

    formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }

    truncateFilename(filename, maxLength) {
        if (filename.length <= maxLength) return filename;
        const ext = filename.split(".").pop();
        const name = filename.substring(0, filename.lastIndexOf("."));
        return name.substring(0, maxLength - ext.length - 3) + "..." + ext;
    }
}

window.MediaUI = MediaUI;
