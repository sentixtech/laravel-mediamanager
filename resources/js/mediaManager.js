class MediaManager {
    constructor() {
        // Initialize UI utilities first
        this.ui = new window.MediaUI();

        // Core properties
        this.selectedFiles = [];
        this.selectedMediaIds = [];
        this.selectedMediaUrls = [];
        this.inputNameId = "";
        this.currentFilter = "all";
        this.currentSort = "newest";
        this.currentViewMode = "grid";
        this.searchTimeout = null;
        this.uploadUrl = window.uploadUrl || "/media-manager/upload";
        this.fetchUrl = window.fetchUrl || "/media-manager/fetch";
        this.deleteUrl = window.deleteUrl || "/media-manager/delete";
        this.bulkDeleteUrl =
            window.bulkDeleteUrl || "/media-manager/bulk-delete";
        this.csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || token;
        this.callback = null;
        this.multiple = false;
        this.currentPage = 1;
        this.lastPage = 1;
        this.perPage = 12;
        this.total = 0;
        this.searchTerm = "";
        this.selectionCurrentPage = 1;
        this.selectionLastPage = 1;
        this.selectionSearchTerm = "";
        this.asset = "";
        this.selectMode = window.MediaManagerConfig?.defaultTab || "select";
        this.config = window.MediaManagerConfig?.config;

        // Initialize sub-modules
        this.upload = new window.MediaUpload(this);
        this.selection = new window.MediaSelection(this);
        this.list = new window.MediaList(this);

        this.initialize();
        this.registerSelectorTrigger();
    }

    initialize() {
        this.initializeEventListeners();
        this.upload.initializeFileUpload();
        this.applyDefaultTab();
        this.list.loadMedia();
    }

    initializeEventListeners() {
        // Filter buttons
        $(".filter-btn").on("click", (e) => this.handleFilterClick(e));
        $(".filter-btn-selection").on("click", (e) =>
            this.handleFilterClickSelection(e),
        );

        // View mode buttons
        $(".view-mode-btn").on("click", (e) => this.handleViewModeClick(e));
        $(".view-mode-btnSelect").on("click", (e) =>
            this.handleViewModeClickSelection(e),
        );
        $(".view-mode-btn-selection").on("click", (e) =>
            this.handleViewModeClickSelection(e),
        );

        // Sort and search
        $("#sortBy").on("change", (e) => this.handleSortChange(e));
        $("#sortBySelectMedia").on("change", (e) =>
            this.handleSortChangeSelect(e),
        );
        $("#searchMedia").on("input", (e) => this.handleSearchInput(e));

        // Tab clicks
        $(".media-tab").on("click", (e) => this.handleTabClick(e));

        // Action buttons
        $("#insertBtn").on("click", () => this.selection.insertSelectedMedia());
        $("#uploadSelectedBtn").on("click", () =>
            this.upload.uploadSelectedMedia(),
        );
        $("#bulkDeleteBtn").on("click", () => this.bulkDeleteMedia());

        // Search in selection
        $("#searchSelectMedia").on("input", (e) => {
            clearTimeout(this.searchTimeout);
            const searchTerm = $(e.currentTarget).val();
            this.searchTimeout = setTimeout(() => {
                this.selectionCurrentPage = 1;
                this.list.loadMediaForSelection(searchTerm);
            }, 500);
        });

        // Select all
        $(document).on("change", "#selectAllMedia", (e) => {
            const checked = $(e.currentTarget).is(":checked");
            this.selectedMediaIds = [];
            this.selectedMediaUrls = [];

            $(".media-checkbox").each((i, el) => {
                $(el).prop("checked", checked);
                if (checked) {
                    const id = $(el).data("id");
                    const url = $(el).data("url");
                    this.selectedMediaIds.push(id);
                    this.selectedMediaUrls.push(url);
                }
            });

            this.updateBulkUI();
        });

        // Individual checkbox selection
        $(document).on("change", ".media-checkbox", (e) => {
            const id = $(e.currentTarget).data("id");
            const url = $(e.currentTarget).data("url");

            if ($(e.currentTarget).is(":checked")) {
                if (!this.selectedMediaIds.includes(id)) {
                    this.selectedMediaIds.push(id);
                    this.selectedMediaUrls.push(url);
                }
            } else {
                this.selectedMediaIds = this.selectedMediaIds.filter(
                    (i) => i != id,
                );
                this.selectedMediaUrls = this.selectedMediaUrls.filter(
                    (u) => u != url,
                );
            }

            this.updateBulkUI();
        });

        // Copy link
        $(document).on("click", ".copy-link", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const url = $(e.currentTarget).data("url");
            this.copyLink(url);
        });

        // Pagination for selection
        $(document).on("click", ".pagination a", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const url = $(e.currentTarget).attr("href");
            const pageMatch = url.match(/[?&]page=(\d+)/);
            if (pageMatch && pageMatch[1]) {
                const page = parseInt(pageMatch[1]);
                this.selectionCurrentPage = page;
                this.list.loadMediaForSelection(
                    this.selectionSearchTerm,
                    this.selectionCurrentPage,
                );
            }
        });

        // Pagination for main container
        $(document).on("click", "#mediaContainer .pagination a", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const url = $(e.currentTarget).attr("href");
            const pageMatch = url.match(/[?&]page=(\d+)/);
            if (pageMatch && pageMatch[1]) {
                const page = parseInt(pageMatch[1]);
                this.currentPage = page;
                this.list.loadMedia(this.searchTerm, this.currentPage);
            }
        });

        // Escape key
        $(document).on("keydown", (e) => this.handleEscapeKey(e));
    }

    applyDefaultTab() {
        const defaultTab = window.MediaManagerConfig?.defaultTab || "select";

        $(".media-tab")
            .removeClass("bg-blue-600 text-white")
            .addClass("text-gray-600 dark:text-gray-400");

        $(`.media-tab[data-tab="${defaultTab}"]`)
            .removeClass("text-gray-600 dark:text-gray-400")
            .addClass("bg-blue-600 text-white");

        $(".media-tab-content").addClass("hidden");
        $(`#${defaultTab}Tab`).removeClass("hidden");
        this.toggleActionButtons(defaultTab);

        if (defaultTab == "select") {
            this.list.loadMedia();
        }
    }

    toggleActionButtons(tab) {
        if (tab === "upload") {
            $("#uploadBtn").removeClass("hidden");
            $("#insertBtn").addClass("hidden");
        }
        if (tab === "select") {
            $("#uploadBtn").addClass("hidden");
            $("#insertBtn").removeClass("hidden");
        }
    }

    isSelectionMode() {
        return (
            this.selectMode === "select" ||
            $("#selectMediaModal").is(":visible")
        );
    }

    // Filter handlers
    handleFilterClick(e) {
        this.currentFilter = $(e.currentTarget).data("filter");
        this.currentPage = 1;
        $(".filter-btn")
            .removeClass(
                "bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200",
            )
            .addClass("text-gray-600 dark:text-gray-300");
        $(e.currentTarget).addClass(
            "bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200",
        );
        this.list.loadMedia();
    }

    handleFilterClickSelection(e) {
        this.currentFilter = $(e.currentTarget).data("filter");
        this.currentPage = 1;
        $(".filter-btn-selection")
            .removeClass(
                "bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200",
            )
            .addClass("text-gray-600 dark:text-gray-300");
        $(e.currentTarget).addClass(
            "bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200",
        );
        this.list.loadMediaForSelection(this.searchTerm, 1);
    }

    handleViewModeClick(e) {
        this.currentViewMode = $(e.currentTarget).data("mode");
        $(".view-mode-btn").removeClass("bg-gray-100 dark:bg-gray-700");
        $(e.currentTarget).addClass("bg-gray-100 dark:bg-gray-700");
        this.list.loadMedia(this.searchTerm, 1);
    }

    handleViewModeClickSelection(e) {
        this.currentViewMode = $(e.currentTarget).data("mode");
        $(".view-mode-btnSelect").removeClass("bg-gray-100 dark:bg-gray-700");
        $(e.currentTarget).addClass("bg-gray-100 dark:bg-gray-700");
        this.list.loadMediaForSelection(this.searchTerm, 1);
    }

    handleSortChange(e) {
        this.currentSort = $(e.currentTarget).val();
        this.currentPage = 1;
        this.list.loadMedia();
    }

    handleSortChangeSelect(e) {
        this.currentSort = $(e.currentTarget).val();
        this.currentPage = 1;
        this.list.loadMediaForSelection();
    }

    handleSearchInput(e) {
        clearTimeout(this.searchTimeout);
        const searchTerm = $(e.currentTarget).val();
        this.searchTimeout = setTimeout(() => {
            this.currentPage = 1;
            this.list.loadMedia(searchTerm);
        }, 500);
    }

    handleTabClick(e) {
        const tab = $(e.currentTarget).data("tab");
        this.selectMode = tab;

        $(".media-tab")
            .removeClass("bg-blue-600 text-white")
            .addClass("text-gray-600 dark:text-gray-400");
        $(e.currentTarget)
            .removeClass("text-gray-600 dark:text-gray-400")
            .addClass("bg-blue-600 text-white");

        $(".media-tab-content").addClass("hidden");
        $(`#${tab}Tab`).removeClass("hidden");

        this.toggleActionButtons(tab);

        if (tab === "select") {
            this.selectionCurrentPage = 1;
            this.selectionSearchTerm = "";
            this.list.loadMediaForSelection();
        }
    }

    handleEscapeKey(e) {
        if (e.key === "Escape") {
            $(".fixed.inset-0:not(.hidden)").each((index, element) => {
                const modalId = $(element).attr("id");
                if (modalId && window.closeModal) {
                    closeModal(modalId);
                    if (modalId === "uploadMediaModal") {
                        this.selection.resetSelection();
                        this.selectMode = "upload";
                    }
                }
            });
        }
    }

    // Media operations
    previewMedia(id, type, url) {
        this.ui.previewMedia(id, type, url);
    }

    deleteMedia(id) {
        if (!confirm("Are you sure you want to delete this file?")) return;

        $.ajax({
            url: `${this.deleteUrl}/${id}`,
            type: "DELETE",
            headers: { "X-CSRF-TOKEN": this.csrfToken },
            success: () => {
                this.ui.showNotification(
                    "File deleted successfully",
                    "success",
                );
                this.list.loadMedia();
            },
            error: () =>
                this.ui.showNotification("Failed to delete file", "error"),
        });
    }

    copyLink(url) {
        this.ui.copyLink(url);
    }

    bulkDeleteMedia() {
        if (this.selectedMediaIds.length === 0) return;

        if (!confirm(`Delete ${this.selectedMediaIds.length} selected files?`))
            return;

        $.ajax({
            url: this.bulkDeleteUrl,
            type: "POST",
            data: {
                media_ids: this.selectedMediaIds.join(","),
                _token: this.csrfToken,
            },
            success: (res) => {
                this.ui.showNotification(
                    res.message || "Deleted successfully",
                    "success",
                );
                this.selectedMediaIds = [];
                this.selectedMediaUrls = [];
                this.updateBulkUI();
                this.list.loadMedia();
            },
            error: () => {
                this.ui.showNotification("Bulk delete failed", "error");
            },
        });
    }

    updateBulkUI() {
        const count = this.selectedMediaIds.length;
        this.toggleBulkDeleteButton();

        const totalCheckboxes = $(".media-checkbox").length;
        const checkedBoxes = $(".media-checkbox:checked").length;

        if (totalCheckboxes > 0 && checkedBoxes === totalCheckboxes) {
            $("#selectAllMedia").prop("checked", true);
        } else {
            $("#selectAllMedia").prop("checked", false);
        }
    }

    toggleBulkDeleteButton() {
        const count = this.selectedMediaIds.length;
        if (count > 0) {
            $("#bulkDeleteBtn").removeClass("hidden").addClass("flex");
            $("#selectedCount").text(count);
        } else {
            $("#bulkDeleteBtn").addClass("hidden").removeClass("flex");
            $("#selectedCount").text(0);
        }
    }

    // Modal functions
    openModalMedia(modalId, type) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");

        if (!this.multiple) {
            this.selection.clearSelection();
        }

        const defaultTab = type;
        this.selectMode = defaultTab;

        $(".media-tab")
            .removeClass("bg-blue-600 text-white")
            .addClass("text-gray-600 dark:text-gray-400");

        $(`.media-tab[data-tab="${defaultTab}"]`)
            .addClass("bg-blue-600 text-white")
            .removeClass("text-gray-600 dark:text-gray-400");

        $(".media-tab-content").addClass("hidden");
        $(`#${defaultTab}Tab`).removeClass("hidden");

        this.toggleActionButtons(defaultTab);

        if (defaultTab === "select") {
            this.list.loadMediaForSelection();
        } else {
            this.list.loadMedia();
        }
    }

    // Selector binding
    bindSelector(inputName, multiple = false, asset = "") {
        this.inputNameId = inputName;
        this.multiple = multiple;
        this.asset = asset;

        const input = $(`#media-${inputName}`);
        if (!input.length) return;

        let value = input.val();
        if (!value) {
            this.selectedMediaUrls = [];
        } else if (Array.isArray(value)) {
            this.selectedMediaUrls = value;
        } else {
            this.selectedMediaUrls = value.split(",").filter(Boolean);
        }

        this.selection.renderSelectorPreview();
    }

    registerSelectorTrigger() {
        $(document)
            .off("click.mediaSelector")
            .on("click.mediaSelector", ".media-selector-trigger", (e) => {
                const el = $(e.currentTarget);
                const inputName = el.data("input");
                const multiple =
                    el.data("multiple") === true ||
                    el.data("multiple") === "true";
                const accept = (el.data("accept") || "*")
                    .toString()
                    .trim()
                    .toLowerCase();
                const max = parseInt(el.data("max") || 1);
                if (!this) return;

                this.bindSelector(
                    inputName,
                    multiple,
                    window.assetBaseUrl || "",
                );
                this.acceptRules = accept;
                this.maxLimit = max;
                openModal("uploadMediaModal");
                this.list.loadMediaForSelection();
            });
    }

    clearAllSelections() {
        $(".media-select-item.selected").each((i, el) => {
            this.selection.updateMediaItemSelectedStateById(
                $(el).attr("id"),
                false,
            );
        });
    }
    selectMedia(url, assetUrl, size, showPreviewInId, elementId) {
        this.selection.selectMedia(
            url,
            assetUrl,
            size,
            showPreviewInId,
            elementId,
        );
    }
  
}

window.MediaManager = MediaManager;
