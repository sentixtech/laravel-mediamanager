@php
    $selectedUrls = is_array($urls) ? $urls : json_decode($urls, true) ?? [];
    
    foreach($media as $item) {
        $item->selected = in_array($item->saveUrl ?? '', $selectedUrls);
    }
@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-2">
    @forelse($media as $item)
        <div class="group relative border rounded-lg overflow-hidden hover:shadow-lg transition-all cursor-pointer dark:border-gray-700 dark:hover:shadow-gray-800 {{ $item->selected ? 'border-2 border-green-500 selected' : '' }} media-select-item" id="mediaSelect-{{ $item->id}}"
            onclick="mediaManager.selectMedia('{{ $item->saveUrl ?? '' }}','{{ asset('/') }}','{{ $item->size }}','{{ $inputNameId }}', 'mediaSelect-{{ $item->id }}')"
            data-id="{{ $item->id }}" data-url="{{ $item->url }}" data-name="{{ $item->name }}"
            data-type="{{ $item->type }}" data-size="{{ $item->size }}">

            <div class="aspect-w-1 aspect-h-1 bg-gray-100 dark:bg-gray-800 min-h-[70px] max-h-[100px]">
                @if (in_array($item->type, ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/svg+xml', 'image/webp']))
                    <img src="{{ $item->url }}" alt="{{ $item->name }}"
                        class="w-full h-[70px] object-cover group-hover:scale-105 transition-transform duration-200"
                        loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        @php
                            $extension = strtolower(pathinfo($item->name, PATHINFO_EXTENSION));
                        @endphp
                        @switch($extension)
                            @case('pdf')
                                <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                            @break

                            @case('doc')
                            @case('docx')
                                <i class="fas fa-file-word text-4xl text-blue-500"></i>
                            @break

                            @case('xls')
                            @case('xlsx')
                                <i class="fas fa-file-excel text-4xl text-green-600"></i>
                            @break

                            @case('txt')
                                <i class="fas fa-file-alt text-4xl text-gray-500"></i>
                            @break

                            @default
                                <i class="fas fa-file text-4xl text-gray-400 dark:text-gray-600"></i>
                        @endswitch
                    </div>
                @endif
            </div>

            <div class="p-2 bg-white border-t dark:bg-gray-800 dark:border-gray-700">
                <p class="text-xs truncate dark:text-gray-200" title="{{ $item->name }}">{{ $item->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->getHumanSize() }}</p>
            </div>

            @if ($item->selected)
                <div
                    class="absolute top-1 right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                    ✓
                </div>
            @endif
        </div>
        @empty
            <div class="col-span-full text-center py-12 dark:bg-gray-900">
                <i class="fas fa-folder-open text-gray-400 dark:text-gray-600 text-5xl mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">No media found</p>
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