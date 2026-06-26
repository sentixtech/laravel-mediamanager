class MediaSelection {
    constructor(manager) {
        this.manager = manager;
        this.ui = new window.MediaUI();
    }

    selectMedia(url, assetUrl, size, showPreviewInId, elementId) {
        const ext = url.split(".").pop().toLowerCase();
        const fileType = this.getFileTypeFromUrl(url);
        if (!this.isAllowed(fileType, ext)) {
            this.ui.showNotification(
                `Only ${this.manager.acceptRules} files allowed`,
                "error",
            );
            return;
        }
        const maxSelect = this.manager.maxLimit;
        const hasLimit =
            maxSelect !== undefined && maxSelect !== null && maxSelect !== 0;
        this.manager.asset = assetUrl;
        if (!Array.isArray(this.manager.selectedMediaUrls)) {
            this.manager.selectedMediaUrls = this.manager.selectedMediaUrls
                ? [this.manager.selectedMediaUrls]
                : [];
        }

        if (!Array.isArray(this.manager.selectedMediaIds)) {
            this.manager.selectedMediaIds = this.manager.selectedMediaIds
                ? [this.manager.selectedMediaIds]
                : [];
        }

        const index = this.manager.selectedMediaUrls.indexOf(url);
        const isSelected = index !== -1;

        // Multiple select
        if (this.manager.multiple) {
            if (
                !isSelected &&
                hasLimit &&
                this.manager.selectedMediaUrls.length >= maxSelect
            ) {
                this.ui.showNotification(
                    `You can select maximum ${maxSelect} items`,
                    "error",
                );
                return;
            }
            if (!isSelected) {
                this.manager.selectedMediaUrls.push(url);
                this.manager.selectedMediaIds.push(this.extractIdFromUrl(url));
                this.updateMediaItemSelectedStateById(elementId, true);
            } else {
                this.manager.selectedMediaUrls.splice(index, 1);
                this.manager.selectedMediaIds.splice(index, 1);
                this.updateMediaItemSelectedStateById(elementId, false);
            }
            this.updatePreviewFromSelectedUrls();
            return;
        }

        // Single select
        if (isSelected) {
            this.manager.clearAllSelections();
            this.manager.selectedMediaUrls = [];
            this.manager.selectedMediaIds = [];
        } else {
            this.manager.clearAllSelections();
            this.manager.selectedMediaUrls = [url];
            this.manager.selectedMediaIds = [this.extractIdFromUrl(url)];
            this.updateMediaItemSelectedStateById(elementId, true);
        }

        this.updatePreviewFromSelectedUrls();
    }

    updateMediaItemSelectedState(elementId, isSelected) {
        const element = $(`#${elementId}`);
        if (element.length) {
            if (isSelected) {
                element.addClass("border-2 border-green-500 selected");
                if (!element.find(".selected-checkmark").length) {
                    element.append(
                        '<div class="selected-checkmark absolute top-1 right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs z-10">✓</div>',
                    );
                }
                const checkbox = element.find(".media-checkbox");
                if (checkbox.length) {
                    checkbox.prop("checked", true);
                }
            } else {
                element.removeClass("border-2 border-green-500 selected");
                element.find(".selected-checkmark").remove();
                const checkbox = element.find(".media-checkbox");
                if (checkbox.length) {
                    checkbox.prop("checked", false);
                }
            }
        }
    }

    updateMediaItemSelectedStateById(elementId, isSelected) {
        const element = $(`#${elementId}`);
        if (element.length) {
            if (isSelected) {
                element.addClass("selected border-2 border-green-500");
                if (!element.find(".selected-checkmark").length) {
                    element.append(
                        '<div class="selected-checkmark absolute top-1 right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs z-10">✓</div>',
                    );
                }
                const checkbox = element.find(".media-checkbox");
                if (checkbox.length) {
                    checkbox.prop("checked", true);
                }
            } else {
                element.removeClass("selected border-2 border-green-500");
                element.find(".selected-checkmark").remove();
                const checkbox = element.find(".media-checkbox");
                if (checkbox.length) {
                    checkbox.prop("checked", false);
                }
            }
        }
    }

    extractIdFromUrl(url) {
        const matches = url.match(/\/(\d+)\//);
        return matches ? matches[1] : null;
    }

    updatePreviewFromSelectedUrls() {
        if (this.manager.inputNameId) {
            const previewId = this.manager.inputNameId + "_preview";
            const previewContainer = $(`#${previewId}`);
            const hiddenInput = $(`#media-${this.manager.inputNameId}`);

            if (previewContainer.length) {
                previewContainer.empty();
                if (this.manager.selectedMediaUrls.length > 0) {
                    if (this.manager.multiple) {
                        this.manager.selectedMediaUrls.forEach((url, index) => {
                            this.addPreviewItem(previewId, url, index);
                        });
                    } else {
                        this.addPreviewItem(
                            previewId,
                            this.manager.selectedMediaUrls[0],
                            0,
                        );
                    }
                }
            }

            if (hiddenInput.length) {
                hiddenInput.val(this.manager.selectedMediaUrls.join(","));
            }
        }
    }

    addPreviewItem(containerId, url, index) {
        const container = $(`#${containerId}`);
        if (!container.length) return;

        const fullUrl = url.startsWith("http")
            ? url
            : this.manager.asset.replace(/\/$/, "") + url;

        const fileType = this.getFileTypeFromUrl(url); // image, video, document...

        const previewHtml = `
        <div class="relative inline-block m-1 preview-item group"
             data-url="${url}" data-index="${index}">

            ${
                fileType === "image"
                    ? `<img src="${fullUrl}" class="w-16 h-16 object-cover rounded border">`
                    : fileType === "video"
                      ? `<div class="w-16 h-16 flex items-center justify-center bg-blue-50 rounded border">
                            <i class="fas fa-play text-blue-500 text-xl"></i>
                       </div>`
                      : fileType === "document"
                        ? `<div class="w-16 h-16 flex items-center justify-center bg-red-50 rounded border">
                            <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                       </div>`
                        : fileType === "spreadsheet"
                          ? `<div class="w-16 h-16 flex items-center justify-center bg-green-50 rounded border">
                            <i class="fas fa-file-excel text-green-600 text-xl"></i>
                       </div>`
                          : `<div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border">
                        <i class="fas fa-file text-gray-500 text-xl"></i>
                   </div>`
            }

            <button type="button"
                class="remove-preview-item absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                ×
            </button>
        </div>
    `;

        container.append(previewHtml);

        container
            .find(`.preview-item[data-url="${url}"] .remove-preview-item`)
            .off("click")
            .on("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.removeSelectedMediaItem(url);
            });
    }

    removeSelectedMediaItem(url) {
        const index = this.manager.selectedMediaUrls.indexOf(url);
        if (index !== -1) {
            this.manager.selectedMediaUrls.splice(index, 1);
            if (this.manager.selectedMediaIds[index]) {
                this.manager.selectedMediaIds.splice(index, 1);
            }

            const mediaItem = $(`.media-item[data-url="${url}"]`);
            if (mediaItem.length) {
                this.updateMediaItemSelectedStateById(
                    mediaItem.attr("id"),
                    false,
                );
            }

            this.updatePreviewFromSelectedUrls();
            //this.ui.showNotification("Item removed from selection", "info");
        }
    }

    getFileTypeFromUrl(url) {
        const ext = url.split(".").pop().toLowerCase();

        const config = this.manager.config;
        const type = "";
        for (const [type, settings] of Object.entries(config)) {
            if (!settings?.extensions) continue;

            if (settings.extensions.includes(ext)) {
                console.log(type);
                return type;
            }
        }
        return "file";
    }
    isAllowed(fileType, ext = null) {
        const accept = this.manager.acceptRules || "*";

        if (accept === "*") return true;

        const allowed = accept.split(",").map((t) => t.trim().toLowerCase());

        return allowed.includes(fileType) || (ext && allowed.includes(ext));
    }

    renderSelectorPreview() {
        if (!this.manager.inputNameId) return;

        const previewId = `${this.manager.inputNameId}_preview`;
        const preview = $(`#${previewId}`);
        const input = $(`#media-${this.manager.inputNameId}`);

        if (!preview.length || !input.length) return;

        preview.empty();

        this.manager.selectedMediaUrls.forEach((url) => {
            const fullUrl = this.manager.asset.replace(/\/$/, "") + url;
            const ext = url.split(".").pop().toLowerCase();
            const isImage = [
                "jpg",
                "jpeg",
                "png",
                "webp",
                "gif",
                "svg",
            ].includes(ext);

            if (isImage) {
                preview.append(`
                <div class="relative w-16 h-16 rounded-lg overflow-hidden border bg-white shadow-sm">
                    <img src="${fullUrl}" class="w-full h-full object-cover">
                    <button type="button" class="remove-preview absolute top-0 right-0 bg-red-500 text-white text-xs px-1">×</button>
                </div>
                `);
            } else {
                preview.append(`
                <div class="relative w-16 h-16 flex items-center justify-center border rounded-lg bg-gray-100">
                    <i class="fas fa-file text-gray-500"></i>
                    <button type="button" class="remove-preview absolute top-0 right-0 bg-red-500 text-white text-xs px-1">×</button>
                </div>
                `);
            }
        });

        input.val(this.manager.selectedMediaUrls.join(","));

        $(".remove-preview")
            .off("click")
            .on("click", (e) => {
                const index = $(e.currentTarget).parent().index();
                this.manager.selectedMediaUrls.splice(index, 1);
                this.renderSelectorPreview();
            });
    }

    insertSelectedMedia() {
        if (this.manager.selectedMediaUrls.length === 0) {
            this.ui.showNotification("No media selected", "warning");
            return;
        }

        const selectedMedia = this.manager.selectedMediaUrls.map((url) => ({
            url: this.manager.asset.replace(/\/$/, "") + url,
        }));

        if (this.manager.callback) {
            if (this.manager.multiple) {
                this.manager.callback(selectedMedia);
            } else {
                this.manager.callback(selectedMedia[0]);
            }
        } else if (window.onMediaSelected) {
            if (this.manager.multiple) {
                window.onMediaSelected(selectedMedia);
            } else {
                window.onMediaSelected(selectedMedia[0]);
            }
        }

        closeModal("uploadMediaModal");
    }

    clearSelection() {
        $(".media-item.selected").each((index, element) => {
            this.updateMediaItemSelectedStateById($(element).attr("id"), false);
        });
        this.manager.selectedMediaUrls = [];
        this.manager.selectedMediaIds = [];
        this.updatePreviewFromSelectedUrls();
    }

    resetSelection() {
        this.manager.selectedMediaUrls = [];
        this.manager.selectedMediaIds = [];
    }

    applySelectedStateToMediaItems() {
        if (
            !this.manager.selectedMediaUrls ||
            this.manager.selectedMediaUrls.length === 0
        )
            return;

        $(".media-select-item").each((index, element) => {
            const $element = $(element);
            const url = $element.data("url");
            const mediaId = $element.data("id");

            let isSelected = false;
            if (url && this.manager.selectedMediaUrls.includes(url)) {
                isSelected = true;
            } else if (
                mediaId &&
                this.manager.selectedMediaIds &&
                this.manager.selectedMediaIds.includes(mediaId)
            ) {
                isSelected = true;
            }

            this.updateMediaItemSelectedStateById(
                $element.attr("id"),
                isSelected,
            );
        });
    }
}

window.MediaSelection = MediaSelection;
