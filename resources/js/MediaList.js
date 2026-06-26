class MediaList {
    constructor(manager) {
        this.manager = manager;
        this.ui = new window.MediaUI();
    }

    loadMedia(search = "", page = 1) {
        this.manager.searchTerm = search;
        this.manager.currentPage = page;
        $("#mediaContainer").html(
            '<div class="flex justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div></div>',
        );

        $.ajax({
            url: this.manager.fetchUrl,
            type: "POST",
            data: {
                filter: this.manager.currentFilter,
                sort: this.manager.currentSort,
                view: this.manager.currentViewMode,
                search: search,
                page: page,
                per_page: this.manager.perPage,
                ajax: true,
                _token: this.manager.csrfToken,
            },
            success: (response) => {
                if (response.status !== false) {
                    this.manager.total = response.data.total;
                    this.manager.lastPage = response.data.last_page;
                    this.manager.currentPage = response.data.current_page;
                    $("#mediaContainer").html(response.data.html);
                    if (response.data) {
                        this.renderMainPagination(response.data);
                    }
                }
            },
            error: () => {
                $("#mediaContainer").html(
                    '<div class="text-center py-12"><i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i><p class="text-gray-600">Failed to load media</p></div>',
                );
            },
        });
    }

    loadMediaForSelection(search = "", page = 1) {
        this.manager.selectionSearchTerm = search;
        this.manager.selectionCurrentPage = page;
        $("#mediaSelectionContainer").html(
            '<div class="flex justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div></div>',
        );

        $.ajax({
            url: this.manager.fetchUrl,
            type: "POST",
            data: {
                filter: this.manager.currentFilter,
                sort: this.manager.currentSort,
                search: search,
                ajax: true,
                page: page,
                per_page: this.manager.perPage,
                selection_mode: true,
                view: this.manager.currentViewMode,
                inputNameId: this.manager.inputNameId,
                urls: JSON.stringify(this.manager.selectedMediaUrls),
                _token: this.manager.csrfToken,
            },
            success: (response) => {
                if (response.status !== false) {
                    this.manager.selectionLastPage = response.data.last_page;
                    $("#mediaSelectionContainer").html(response.data.view);
                    this.manager.selection.applySelectedStateToMediaItems();
                    if (response.data) {
                        this.renderSelectionPagination(response.data);
                    }
                }
            },
            error: () => {
                $("#mediaSelectionContainer").html(
                    '<div class="text-center py-12"><i class="fas fa-exclamation-circle text-red-500 text-5xl mb-4"></i><p class="text-gray-600">Failed to load media</p></div>',
                );
            },
        });
    }

    renderSelectionPagination(paginationData) {
        const { current_page, last_page, from, to, total } = paginationData;
        if (last_page <= 1) return;

        const paginationHtml = `
        <div class="selection-pagination-wrapper flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t dark:border-gray-700 sm:px-6 mt-4 rounded-lg sticky bottom-0 shadow-sm">
            <div class="flex justify-between flex-1 sm:hidden">
                <button type="button" data-page="${current_page - 1}" class="selection-page-btn relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === 1 ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === 1 ? "disabled" : ""}>
                    Previous
                </button>
                <button type="button" data-page="${current_page + 1}" class="selection-page-btn relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === last_page ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === last_page ? "disabled" : ""}>
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">${from || 0}</span>
                        to <span class="font-medium">${to || 0}</span>
                        of <span class="font-medium">${total}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <button type="button" data-page="1" class="selection-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 rounded-l-md border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === 1 ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === 1 ? "disabled" : ""}>
                            <span class="sr-only">First</span>
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <button type="button" data-page="${current_page - 1}" class="selection-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === 1 ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === 1 ? "disabled" : ""}>
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-angle-left"></i>
                        </button>
                        ${this.generateSelectionPageNumbers(current_page, last_page)}
                        <button type="button" data-page="${current_page + 1}" class="selection-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === last_page ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === last_page ? "disabled" : ""}>
                            <span class="sr-only">Next</span>
                            <i class="fas fa-angle-right"></i>
                        </button>
                        <button type="button" data-page="${last_page}" class="selection-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 rounded-r-md border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === last_page ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === last_page ? "disabled" : ""}>
                            <span class="sr-only">Last</span>
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
        `;

        $("#mediaSelectionContainer").append(paginationHtml);

        $(".selection-page-btn")
            .off("click")
            .on("click", (e) => {
                e.preventDefault();
                const page = $(e.currentTarget).data("page");
                if (page && page !== this.manager.selectionCurrentPage) {
                    this.changeSelectionPage(page);
                }
            });
    }

    generateSelectionPageNumbers(current, last) {
        let pages = [];
        let start = Math.max(1, current - 2);
        let end = Math.min(last, current + 2);

        if (start > 1) {
            pages.push(
                `<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border dark:border-gray-600 bg-white dark:bg-gray-800">...</span>`,
            );
        }

        for (let i = start; i <= end; i++) {
            pages.push(`
            <button type="button" data-page="${i}" class="selection-page-btn relative inline-flex items-center px-4 py-2 text-sm font-medium border dark:border-gray-600 
            ${
                current === i
                    ? "z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-300"
                    : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            }">
                ${i}
            </button>
            `);
        }

        if (end < last) {
            pages.push(
                `<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border dark:border-gray-600 bg-white dark:bg-gray-800">...</span>`,
            );
        }

        return pages.join("");
    }

    changeSelectionPage(page) {
        page = parseInt(page);
        if (
            isNaN(page) ||
            page < 1 ||
            page > this.manager.selectionLastPage ||
            page === this.manager.selectionCurrentPage
        ) {
            return;
        }

        $("#mediaSelectionContainer").animate({ scrollTop: 0 }, 300);
        const searchTerm = $("#searchSelectMedia").val();
        this.loadMediaForSelection(searchTerm, page);
    }

    renderMainPagination(paginationData) {
        const { current_page, last_page, from, to, total } = paginationData;
        if (last_page <= 1) return;

        const paginationHtml = `
        <div class="main-pagination-wrapper flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t dark:border-gray-700 sm:px-6 mt-4 rounded-lg">
            <div class="flex justify-between flex-1 sm:hidden">
                <button type="button" data-page="${current_page - 1}" class="main-page-btn relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === 1 ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === 1 ? "disabled" : ""}>
                    Previous
                </button>
                <button type="button" data-page="${current_page + 1}" class="main-page-btn relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === last_page ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === last_page ? "disabled" : ""}>
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">${from || 0}</span>
                        to <span class="font-medium">${to || 0}</span>
                        of <span class="font-medium">${total}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <button type="button" data-page="1" class="main-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 rounded-l-md border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === 1 ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === 1 ? "disabled" : ""}>
                            <span class="sr-only">First</span>
                            <i class="fas fa-angle-double-left"></i>
                        </button>
                        <button type="button" data-page="${current_page - 1}" class="main-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === 1 ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === 1 ? "disabled" : ""}>
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-angle-left"></i>
                        </button>
                        ${this.generateMainPageNumbers(current_page, last_page)}
                        <button type="button" data-page="${current_page + 1}" class="main-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === last_page ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === last_page ? "disabled" : ""}>
                            <span class="sr-only">Next</span>
                            <i class="fas fa-angle-right"></i>
                        </button>
                        <button type="button" data-page="${last_page}" class="main-page-btn relative inline-flex items-center px-2 py-2 text-gray-400 rounded-r-md border dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 ${current_page === last_page ? "opacity-50 cursor-not-allowed" : ""}" ${current_page === last_page ? "disabled" : ""}>
                            <span class="sr-only">Last</span>
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
        `;

        $("#mediaContainer").append(paginationHtml);

        $(".main-page-btn")
            .off("click")
            .on("click", (e) => {
                e.preventDefault();
                const page = $(e.currentTarget).data("page");
                if (page && page !== this.manager.currentPage) {
                    this.changeMainPage(page);
                }
            });
    }

    generateMainPageNumbers(current, last) {
        let pages = [];
        let start = Math.max(1, current - 2);
        let end = Math.min(last, current + 2);

        if (start > 1) {
            pages.push(
                `<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border dark:border-gray-600 bg-white dark:bg-gray-800">...</span>`,
            );
        }

        for (let i = start; i <= end; i++) {
            pages.push(`
            <button type="button" data-page="${i}" class="main-page-btn relative inline-flex items-center px-4 py-2 text-sm font-medium border dark:border-gray-600 
            ${
                current === i
                    ? "z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-300"
                    : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            }">
                ${i}
            </button>
            `);
        }

        if (end < last) {
            pages.push(
                `<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border dark:border-gray-600 bg-white dark:bg-gray-800">...</span>`,
            );
        }

        return pages.join("");
    }

    changeMainPage(page) {
        page = parseInt(page);
        if (
            isNaN(page) ||
            page < 1 ||
            page > this.manager.lastPage ||
            page === this.manager.currentPage
        ) {
            return;
        }

        $("html, body").animate({ scrollTop: 0 }, 300);
        const searchTerm = $("#searchMedia").val();
        this.loadMedia(searchTerm, page);
    }
}

window.MediaList = MediaList;
