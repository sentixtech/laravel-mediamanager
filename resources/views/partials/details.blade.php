<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($media as $item)
        <div class="media-item group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-200 cursor-pointer overflow-hidden"
            data-id="{{ $item->id }}" data-url="{{ $item->url }}" data-name="{{ $item->name }}"
            data-type="{{ $item->type }}" data-size="{{ $item->size }}">

            <div class="absolute top-3 left-3 z-10">
                <input type="checkbox"
                    class="media-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                    data-id="{{ $item->id }}" data-url="{{ $item->url }}">
            </div>

            <div class="bg-gray-50 dark:bg-gray-900 relative overflow-hidden bg-gray-100 dark:bg-gray-800 h-[200px]">
                @if($item->type === 'image')
                    <img src="{{ $item->url }}" alt="{{ $item->name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200 h-[400px]"
                        loading="lazy">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center">
                        @php
                            $icon = match ($item->extension) {
                                'pdf' => 'fa-file-pdf text-red-500',
                                'doc', 'docx' => 'fa-file-word text-blue-500',
                                'xls', 'xlsx' => 'fa-file-excel text-green-600',
                                'mp4', 'avi', 'mov' => 'fa-video text-purple-500',
                                'zip', 'rar' => 'fa-file-archive text-yellow-600',
                                'mp3', 'wav' => 'fa-file-audio text-pink-500',
                                default => 'fa-file text-gray-400'
                            };
                        @endphp
                        <i class="fas {{ $icon }} text-6xl"></i>
                        <span
                            class="text-sm text-gray-500 dark:text-gray-400 uppercase mt-3 font-medium">{{ $item->extension ?? 'file' }}</span>
                    </div>
                @endif

                <!-- Type Badge -->
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                            {{ $item->type === 'image' ? 'bg-blue-500/80 text-white' : 'bg-purple-500/80 text-white' }}">
                        {{ ucfirst($item->type) }}
                    </span>
                </div>

                <!-- File Extension Badge -->
                @if($item->extension && $item->type !== 'image')
                    <div class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-3 py-1 rounded-full">
                        {{ strtoupper($item->extension) }}
                    </div>
                @endif
            </div>

            <!-- Detailed Info -->
            <div class="p-4">
                <p class="text-base font-semibold text-gray-900 dark:text-white truncate" title="{{ $item->name }}">
                    {{ $item->name }}
                </p>

                <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Size:</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium ml-1">{{ $item->getHumanSize() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                        <span
                            class="text-gray-700 dark:text-gray-300 font-medium ml-1">{{ $item->extension ?? 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500 dark:text-gray-400">Uploaded:</span>
                        <span
                            class="text-gray-700 dark:text-gray-300 font-medium ml-1">{{ $item->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500 dark:text-gray-400">Dimensions:</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium ml-1">
                            @if($item->type === 'image' && isset($item->metadata['width']))
                                {{ $item->metadata['width'] }} x {{ $item->metadata['height'] }} px
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Full Action Buttons -->
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <button type="button"
                            onclick="event.stopPropagation(); window.mediaManager?.previewMedia({{ $item->id }}, '{{ $item->type }}', '{{ $item->url }}')"
                            class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-md transition-colors">
                            <i class="fas fa-eye mr-1"></i> Preview
                        </button>
                        <button type="button"
                            onclick="event.stopPropagation(); window.mediaManager?.copyLink('{{ $item->url }}')"
                            class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-md transition-colors">
                            <i class="fas fa-link mr-1"></i> Copy
                        </button>
                    </div>
                    <button type="button"
                        onclick="event.stopPropagation(); window.mediaManager?.deleteMedia({{ $item->id }})"
                        class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-md transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                </div>
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