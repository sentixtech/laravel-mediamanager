<div id="uploadMediaModal" class="fixed inset-0 z-[1000]  hidden overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-all duration-300 -z-10" aria-hidden="true">
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                    <div class="flex space-x-4">
                        <button class="media-tab px-4 py-2 bg-blue-600 text-white rounded-t-lg" data-tab="upload">
                            <i class="fas fa-upload mr-2"></i>Upload
                        </button>
                        <button
                            class="media-tab px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-t-lg"
                            data-tab="select">
                            <i class="fas fa-check-circle mr-2"></i>Select
                        </button>
                    </div>
                </div>

                <div id="uploadTab" class="media-tab-content">
                    <div id="dropZone"
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-blue-500 transition-colors cursor-pointer">
                        <div class="space-y-4">
                            <div class="flex justify-center">
                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400"></i>
                            </div>
                            <div>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300">
                                    Drag & drop files here or
                                    <button type="button" id="browseBtn"
                                        class="text-blue-600 hover:text-blue-700 font-semibold focus:outline-none">
                                        browse
                                    </button>
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Support: Images, Videos, PDF, Excel (Max:
                                    {{ config('media.upload.max_total_size') / 1024 }} MB per file)
                                </p>
                            </div>
                            <input type="file" id="fileInput" multiple class="hidden">
                        </div>
                    </div>

                    <div id="uploadProgress" class="mt-4 hidden">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Uploading...</span>
                            <span id="progressPercentage" class="text-sm text-gray-600 dark:text-gray-400">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                        <p id="uploadStatus" class="text-sm text-gray-500 dark:text-gray-400 mt-2"></p>
                    </div>

                    <div id="filePreviewList" class="mt-4 max-h-96 overflow-y-auto hidden">
                        <div id="fileItems" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4"></div>
                    </div>
                </div>

                <div id="selectTab" class="media-tab-content hidden">
                    <div class="mb-4">
                        <div class="relative">
                            <div class="mb-2">
                                @foreach($filters as $key => $filter)
                                    @if($filter['enabled'] ?? false)
                                        <button
                                            class="filter-btn-selection px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                                                                {{ $loop->first ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                            data-filter="{{ $key }}">
                                            <i class="fas {{ $filter['icon'] }} mr-1"></i>{{ $filter['label'] }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>

                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                            <div class="relative flex-1 sm:flex-none">
                                <input type="text" id="searchSelectMedia" placeholder="Search media..."
                                    class="w-full sm:w-48 md:w-56 lg:w-64 pl-10 pr-4 py-2 border dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>

                            <select id="sortBySelectMedia"
                                class="flex-1 sm:flex-none border dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                @foreach(config('media.sorting.options', []) as $key => $option)
                                    <option value="{{ $key }}" {{ config('media.sorting.default') == $key ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            <div
                                class="flex border dark:border-gray-600 rounded-lg overflow-hidden self-start sm:self-auto">
                                <button
                                    class="view-mode-btnSelect p-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                                    data-mode="grid">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button
                                    class="view-mode-btnSelect p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                                    data-mode="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="mediaSelectionContainer" class="min-h-[250px] max-h-[300px] overflow-y-auto">
                        <button id="loadMediaBtn"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2">
                            <div id="loadSpinner" class="loader hidden"></div>
                            <i id="defaultIcon" class="fas fa-cloud-download-alt"></i>
                            <span id="btnText">Load Media</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="insertBtn" type="button"
                    class="w-full  justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Insert
                </button>
                <button id="uploadBtn" type="button"
                    class="w-full justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Upload
                </button>
                <button type="button" onclick="closeModal('uploadMediaModal')"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div id="mediaPreviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-all duration-300 -z-10" aria-hidden="true">
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Media Preview</h3>
                    <button onclick="closeModal('mediaPreviewModal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="mediaPreviewContent" class="min-h-[400px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>
</div>