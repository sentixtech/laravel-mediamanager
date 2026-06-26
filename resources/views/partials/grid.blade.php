<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
    @forelse($media as $item)
        <div class="media-item group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all duration-200 cursor-pointer"
            data-id="{{ $item->id }}" data-url="{{ $item->url }}" data-name="{{ $item->name }}"
            data-type="{{ $item->type }}" data-size="{{ $item->size }}">

            <!-- Checkbox -->
            <div class="absolute top-2 left-2 z-10">
                <input type="checkbox"
                    class="media-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                    data-id="{{ $item->id }}" data-url="{{ $item->url }}">
            </div>

            <!-- Media Thumbnail -->
            <div
                class="bg-gray-50 dark:bg-gray-900 relative overflow-hidden rounded-t-lg bg-gray-100 dark:bg-gray-800 min-h-[70px] max-h-[100px] aspect-w-1 aspect-h-1">
                @if($item->type === 'image')
                    <img src="{{ $item->url ?? $item->url }}" alt="{{ $item->name }}"
                        class="w-full h-[70px] object-cover group-hover:scale-105 transition-transform duration-200"
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
                        <i class="fas {{ $icon }} text-4xl"></i>
                        <span
                            class="text-xs text-gray-500 dark:text-gray-400 uppercase mt-2 font-medium">{{ $item->extension ?? 'file' }}</span>
                    </div>
                @endif

                <!-- File Type Badge -->
                @if($item->type !== 'image')
                    <div class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                        {{ strtoupper($item->extension ?? 'file') }}
                    </div>
                @endif
            </div>

            <!-- Media Info -->
            <div class="p-3">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $item->name }}">
                    {{ Str::limit($item->name, 20) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $item->getHumanSize() }}
                </p>
            </div>

            <!-- Action Buttons - Appear on card hover -->
            <div
                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center gap-3">
                <!-- Preview Button -->
                <button type="button"
                    onclick="event.stopPropagation(); window.mediaManager?.previewMedia({{ $item->id }}, '{{ $item->type }}', '{{ $item->url }}')"
                    class="bg-white hover:bg-blue-500 text-gray-700 hover:text-white w-8 h-8 rounded-full shadow-lg transition-all duration-200 hover:scale-110 flex items-center justify-center"
                    title="Preview">
                    <i class="fas fa-eye text-sm"></i>
                </button>

                <!-- Copy Button -->
                <button type="button" onclick="event.stopPropagation(); window.mediaManager?.copyLink('{{ $item->url }}')"
                    class="bg-white hover:bg-blue-500 text-gray-700 hover:text-white w-8 h-8 rounded-full shadow-lg transition-all duration-200 hover:scale-110 flex items-center justify-center"
                    title="Copy link">
                    <i class="fas fa-link text-sm"></i>
                </button>

                <!-- Delete Button -->
                <button type="button" onclick="event.stopPropagation(); window.mediaManager?.deleteMedia({{ $item->id }})"
                    class="bg-white hover:bg-red-500 text-gray-700 hover:text-white w-8 h-8 rounded-full shadow-lg transition-all duration-200 hover:scale-110 flex items-center justify-center"
                    title="Delete">
                    <i class="fas fa-trash-alt text-sm"></i>
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No media found</p>
        </div>
    @endforelse
</div>