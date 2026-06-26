<div class="list-view ">
    @forelse($media as $item)
        <div class="media-item group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all duration-200 cursor-pointer mb-3"
            data-id="{{ $item->id }}" data-url="{{ $item->url }}" data-name="{{ $item->name }}"
            data-type="{{ $item->type }}" data-size="{{ $item->size }}">

            <div class="flex items-center p-3 gap-4">
                <!-- Checkbox -->
                <div class="flex-shrink-0">
                    <input type="checkbox"
                        class="media-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                        data-id="{{ $item->id }}" data-url="{{ $item->url }}">
                </div>

                <!-- Media Thumbnail -->
                <div class="flex-shrink-0 w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-lg overflow-hidden relative">
                    @if($item->type === 'image')
                        <img src="{{ $item->url ?? $item->url }}" alt="{{ $item->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                            loading="lazy">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center">
                            @php
                                $icon = match ($item->extension) {
                                    'pdf' => 'fa-file-pdf text-red-500',
                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                    'xls', 'xlsx', 'csv' => 'fa-file-excel text-green-600',
                                    'mp4', 'avi', 'mov' => 'fa-video text-purple-500',
                                    'zip', 'rar' => 'fa-file-archive text-yellow-600',
                                    'mp3', 'wav' => 'fa-file-audio text-pink-500',
                                    default => 'fa-file text-gray-400'
                                };
                            @endphp
                            <i class="fas {{ $icon }} text-2xl"></i>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase mt-0.5 font-medium">{{ $item->extension ?? 'file' }}</span>
                        </div>
                    @endif
                </div>

                <!-- Media Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $item->name }}">
                        {{ $item->name }}
                    </p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $item->getHumanSize() }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}
                        </span>
                        @if($item->type !== 'image')
                            <span class="text-xs text-gray-500 dark:text-gray-400">•</span>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">
                                {{ $item->extension ?? 'file' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <!-- Preview Button -->
                    <button type="button"
                        onclick="event.stopPropagation(); window.mediaManager?.previewMedia({{ $item->id }}, '{{ $item->type }}', '{{ $item->url }}')"
                        class="text-gray-400 hover:text-blue-500 dark:text-gray-500 dark:hover:text-blue-400 p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200"
                        title="Preview">
                        <i class="fas fa-eye text-sm"></i>
                    </button>

                    <!-- Copy Button -->
                    <button type="button" onclick="event.stopPropagation(); window.mediaManager?.copyLink('{{ $item->url }}')"
                        class="text-gray-400 hover:text-blue-500 dark:text-gray-500 dark:hover:text-blue-400 p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200"
                        title="Copy link">
                        <i class="fas fa-link text-sm"></i>
                    </button>

                    <!-- Delete Button -->
                    <button type="button" onclick="event.stopPropagation(); window.mediaManager?.deleteMedia({{ $item->id }})"
                        class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                        title="Delete">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No media found</p>
        </div>
    @endforelse
</div>