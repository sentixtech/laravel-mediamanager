class MediaUpload {
    constructor(manager) {
        this.manager = manager;
        this.ui = new window.MediaUI();
        this.config = window.MediaManagerConfig?.config?.upload || {};
        this.maxFileSize = this.config.max_total_size
            ? this.config.max_total_size * 1024 
            : 100 * 1024 * 1024;
    }

    initializeFileUpload() {
        const dropZone = $("#dropZone");
        const fileInput = $("#fileInput");
        const browseBtn = $("#browseBtn");

        dropZone.off();
        fileInput.off();
        browseBtn.off();

        browseBtn.on("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.on("change", (e) => {
            const files = e.target.files;
            if (files.length > 0) {
                this.handleFileSelect(Array.from(files));
            }
            $(e.target).val("");
        });

        dropZone.on("dragover", (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.addClass("dragover");
        });

        dropZone.on("dragleave", (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass("dragover");
        });

        dropZone.on("drop", (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass("dragover");
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileSelect(Array.from(files));
            }
        });

        $("#uploadBtn").on("click", () => this.uploadFiles());
        $("#loadMediaBtn").on("click", () =>
            this.manager.list.loadMediaForSelection(),
        );
        $(document).on("click", ".remove-file", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const index = $(e.currentTarget).data("index");
            this.removeFile(index);
        });
    }

    handleFileSelect(files) {
        let addedCount = 0;
        let rejectedCount = 0;

        files.forEach((file) => {
            if (this.validateFile(file)) {
                this.manager.selectedFiles.push(file);
                addedCount++;
            } else {
                rejectedCount++;
            }
        });

        this.updateFilePreview();
        this.updateUploadButtonState();

        if (addedCount > 0) {
            this.ui.showNotification(
                `${addedCount} file(s) added successfully`,
                "success",
            );
        }
        if (rejectedCount > 0) {
            this.ui.showNotification(
                `${rejectedCount} file(s) were rejected`,
                "error",
            );
        }
    }

    validateFile(file) {
        if (!file) return false;

        const maxSize = this.maxFileSize;
        if (file.size > maxSize) {
            this.ui.showNotification(
                `File ${file.name} is too large. Max size: ${maxSize}MB`,
                "error",
            );
            return false;
        }

        if (file.size === 0) {
            this.ui.showNotification(`File ${file.name} is empty`, "error");
            return false;
        }

        const extension = file.name.split(".").pop().toLowerCase();
        const allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "gif",
            "webp",
            "bmp",
            "svg",
            "mp4",
            "webm",
            "pdf",
            "doc",
            "docx",
            "txt",
            "xlsx",
            "xls",
            "csv",
        ];

        if (!allowedExtensions.includes(extension)) {
            this.ui.showNotification(
                `File ${file.name} type not supported`,
                "error",
            );
            return false;
        }

        return true;
    }

    updateFilePreview() {
        if (this.manager.selectedFiles.length > 0) {
            $("#filePreviewList").removeClass("hidden");
            const previewContainer = $("#fileItems");
            previewContainer.empty();

            this.manager.selectedFiles.forEach((file, index) => {
                const fileSize = this.ui.formatFileSize(file.size);
                const gridItem = $(`
                    <div class="file-grid-item relative group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 transition-all overflow-hidden">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-50 dark:bg-gray-900 relative">
                            ${this.getFilePreviewHtml(file)}
                            <span class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full">
                                ${file.name.split(".").pop().toUpperCase()}
                            </span>
                        </div>
                        <div class="p-2">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" title="${file.name}">
                                        ${this.ui.truncateFilename(file.name, 20)}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">${fileSize}</p>
                                </div>
                                <button class="remove-file p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg" data-index="${index}">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                previewContainer.append(gridItem);
                gridItem
                    .find(".remove-file")
                    .on("click", () => this.removeFile(index));
            });
        } else {
            $("#filePreviewList").addClass("hidden");
            $("#fileItems").empty();
        }
    }

    getFilePreviewHtml(file) {
        if (file.type && file.type.startsWith("image/")) {
            const imageUrl = URL.createObjectURL(file);
            setTimeout(() => URL.revokeObjectURL(imageUrl), 1000);
            return `<img src="${imageUrl}" class="object-cover h-[100px] w-full">`;
        } else if (file.type && file.type.startsWith("video/")) {
            return `<div class="h-[100px] flex items-center justify-center"><i class="fas fa-video text-4xl text-purple-500"></i></div>`;
        } else if (file.name.toLowerCase().endsWith(".pdf")) {
            return `<div class="h-[100px] flex items-center justify-center"><i class="fas fa-file-pdf text-4xl text-red-500"></i></div>`;
        } else if (file.name.match(/\.(xlsx?|csv)$/i)) {
            return `<div class="h-[100px] flex items-center justify-center"><i class="fas fa-file-excel text-4xl text-green-600"></i></div>`;
        }
        return `<div class="h-[100px] flex items-center justify-center"><i class="fas fa-file text-4xl text-gray-500"></i></div>`;
    }

    removeFile(index) {
        this.manager.selectedFiles.splice(index, 1);
        this.updateFilePreview();
        this.updateUploadButtonState();
    }

    updateUploadButtonState() {
        const hasFiles = this.manager.selectedFiles.length > 0;
        $("#uploadBtn").prop("disabled", !hasFiles);
        if (hasFiles) {
            $("#uploadBtn").removeClass("opacity-50 cursor-not-allowed");
        } else {
            $("#uploadBtn").addClass("opacity-50 cursor-not-allowed");
        }
    }

    async uploadFiles() {
        const formData = new FormData();
        this.manager.selectedFiles.forEach((file) =>
            formData.append("files[]", file),
        );
        formData.append("_token", this.manager.csrfToken);

        $("#uploadProgress").removeClass("hidden");
        $("#uploadBtn").prop("disabled", true).addClass("opacity-50");

        try {
            const response = await $.ajax({
                url: this.manager.uploadUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                xhr: () => this.createUploadXhr(),
            });
            this.handleUploadSuccess(response);
        } catch (error) {
            this.handleUploadError(error);
        }
    }

    createUploadXhr() {
        const xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", (e) => {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                $("#progressBar").css("width", percent + "%");
                $("#progressPercentage").text(Math.round(percent) + "%");
            }
        });
        return xhr;
    }

    handleUploadSuccess(response) {
        this.ui.showNotification("Files uploaded successfully!", "success");
        this.resetUploadForm();
        this.manager.list.loadMedia();
        $("#uploadBtn").prop("disabled", false).removeClass("opacity-50");
        $("#uploadProgress").addClass("hidden");
    }

    handleUploadError(error) {
        console.error("Upload Error Details:", error);

        let errorMessage = "Upload failed. Please try again.";

        if (error.responseJSON) {
            if (error.responseJSON.errors) {
                const errors = Object.values(error.responseJSON.errors).flat();
                errorMessage = errors.join(", ");
            } else if (error.responseJSON.message) {
                errorMessage = error.responseJSON.message;
            }
        } else if (error.responseText) {
            errorMessage = error.responseText;
        } else if (error.statusText) {
            errorMessage = `${error.status}: ${error.statusText}`;
        } else if (error.message) {
            errorMessage = error.message;
        }

        console.table({
            Status: error.status || "N/A",
            "Status Text": error.statusText || "N/A",
            Message: error.message || "N/A",
            Response: error.responseText || "N/A",
        });

        this.ui.showNotification(errorMessage, "error");
        $("#uploadBtn").prop("disabled", false).removeClass("opacity-50");
        $("#uploadProgress").addClass("hidden");
    }

    resetUploadForm() {
        this.manager.selectedFiles = [];
        this.updateFilePreview();
        $("#fileInput").val("");
        this.updateUploadButtonState();
    }

    uploadSelectedMedia() {
        if (this.manager.selectedMediaUrls.length === 0) return;
        $(document).trigger("mediaSelected", [this.manager.selectedMediaUrls]);
        this.uploadFiles();
        closeModal("uploadMediaModal");
    }
}

window.MediaUpload = MediaUpload;
