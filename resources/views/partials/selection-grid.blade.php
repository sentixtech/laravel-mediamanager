<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-2">
    @forelse($media as $item)
        <div class="media-select-item group relative border rounded-lg overflow-hidden hover:shadow-lg transition-all cursor-pointer dark:border-gray-700 dark:hover:shadow-gray-800 "
            id="mediaSelect-{{ $item->id }}"
            onclick="window.mediaManager?.selectMedia('{{ $item->saveUrl }}', '{{ asset('') }}', '{{ $item->size }}', '{{ $inputNameId ?? '' }}', 'mediaSelect-{{ $item->id }}')"
            data-id="{{ $item->id }}" data-url="{{ $item->saveUrl }}" data-name="{{ $item->name }}"
            data-type="{{ $item->type }}" data-size="{{ $item->size }}">

            <div class="aspect-w-1 aspect-h-1 bg-gray-100 dark:bg-gray-800 min-h-[70px] max-h-[100px]">
                @if($item->type === 'image')
                    <img src="{{ $item->url ?? $item->url }}" alt="{{ $item->name }}"
                        class="w-full h-[70px] object-cover group-hover:scale-105 transition-transform duration-200"
                        loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        @php
                            $icon = match($item->extension) {
                                'pdf' => 'fa-file-pdf text-red-500 text-3xl',
                                'doc', 'docx' => 'fa-file-word text-blue-500 text-3xl',
                                'xls', 'xlsx', 'csv' => 'fa-file-excel text-green-600 text-3xl',
                                'mp4', 'avi', 'mov' => 'fa-video text-purple-500 text-3xl',
                                default => 'fa-file text-gray-400 text-3xl'
                            };
                        @endphp
                        <i class="fas {{ $icon }} mt-5"></i>
                    </div>
                @endif
            </div>

            <div class="p-2 bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                <p class="text-xs truncate dark:text-gray-200" title="{{ $item->name }}">{{ Str::limit($item->name, 20) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->getHumanSize() }}</p>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500">No media found</p>
        </div>
    @endforelse
</div>

<style>
    .media-select-item.selected {
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2), 0 4px 6px -2px rgba(16, 185, 129, 0.1);
    }
    .dark .media-select-item.selected {
        border-color: #34d399 !important;
        box-shadow: 0 10px 15px -3px rgba(52, 211, 153, 0.2), 0 4px 6px -2px rgba(52, 211, 153, 0.1);
    }
</style>