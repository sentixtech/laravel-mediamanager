<div class="container mx-auto ">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1
                class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent dark:from-blue-400 dark:to-purple-400">
                Media Manager
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage and organize your media files efficiently
            </p>
        </div>
        <button onclick="openModal('uploadMediaModal')"
            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Upload Media
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                    <i class="fas fa-file text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Files</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalFiles ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                    <i class="fas fa-image text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Images</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $imageCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                    <i class="fas fa-video text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Videos</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $videoCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                    <i class="fas fa-file-pdf text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Documents</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $documentCount ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                    <i class="fas fa-database text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Storage Used</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $storageUsed ?? '0 MB' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
        <div
            class="overflow-x-auto pb-2 mb-3 [&::-webkit-scrollbar]:h-1 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-600 [&::-webkit-scrollbar-track]:bg-transparent">
            <div class="flex items-center space-x-2 min-w-max">
                @foreach($filters as $key => $filter)
                    @if($filter['enabled'] ?? false)
                        <button
                            class="filter-btn px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-lg transition-colors whitespace-nowrap
                                            {{ $loop->first ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            data-filter="{{ $key }}">
                            <i class="fas {{ $filter['icon'] }} mr-1"></i>
                            <span class="hidden sm:inline">{{ $filter['label'] }}</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
            <div class="relative flex-1 sm:flex-none">
                <input type="text" id="searchMedia" placeholder="Search media..."
                    class="w-full sm:w-48 md:w-56 lg:w-64 pl-10 pr-4 py-2 border dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <select id="sortBy"
                class="flex-1 sm:flex-none border dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                @foreach(config('media.sorting.options', []) as $key => $option)
                    <option value="{{ $key }}" {{ config('media.sorting.default') == $key ? 'selected' : '' }}>
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>

            <div class="flex border dark:border-gray-600 rounded-lg overflow-hidden self-start sm:self-auto">
                <button class="view-mode-btn p-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                    data-mode="grid">
                    <i class="fas fa-th-large"></i>
                </button>
                <button
                    class="view-mode-btn p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                    data-mode="list">
                    <i class="fas fa-list"></i>
                </button>
                
            </div>

            <div class="hidden lg:block w-px h-8 bg-gray-300 dark:bg-gray-600"></div>

            <label class="flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <input type="checkbox" id="selectAllMedia" class="w-4 h-4 accent-blue-600">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Select All
                </span>
            </label>

            <button id="bulkDeleteBtn"
                class="hidden flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition whitespace-nowrap">
                <i class="fas fa-trash mr-2"></i>
                <span>Delete</span>
                <span id="selectedCount" class="ml-2 bg-white text-red-600 px-2 rounded text-xs">0</span>
            </button>
        </div>
    </div>



    <!-- Media Container -->
    <div id="mediaContainer" class="transition-all duration-300"></div>
</div>

<style>
    .media-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .media-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .media-item.selected {
        border: 2px solid #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    .drop-zone.dragover {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
    }

    .file-item {
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>