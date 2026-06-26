<div class="divide-y divide-gray-200 dark:divide-gray-700">
    @forelse($media as $item)
        <div class="media-select-item flex items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer relative group"
            id="mediaSelect-{{ $item->id }}"
            onclick="window.mediaManager?.selectMedia('{{ $item->saveUrl }}', '{{ asset('/') }}', '{{ $item->size }}', '{{ $inputNameId ?? '' }}', 'mediaSelect-{{ $item->id }}')"
            data-id="{{ $item->id }}" data-url="{{ $item->saveUrl }}" data-name="{{ $item->name }}"
            data-type="{{ $item->type }}" data-size="{{ $item->size }}">

            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mr-4">
                @if($item->type === 'image')
                    <img src="{{ $item->url ?? $item->url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        @php
                            $icon = match($item->extension) {
                                'pdf' => 'fa-file-pdf text-red-500 text-xl',
                                'doc', 'docx' => 'fa-file-word text-blue-500 text-xl',
                                'xls', 'xlsx', 'csv' => 'fa-file-excel text-green-600 text-xl',
                                'mp4', 'avi', 'mov' => 'fa-video text-purple-500 text-xl',
                                default => 'fa-file text-gray-400 text-xl'
                            };
                        @endphp
                        <i class="fas {{ $icon }}"></i>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $item->name }}">
                    {{ Str::limit($item->name, 40) }}
                </p>
                {{-- <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $item->human_size }} • {{ $item->created_at->diffForHumans() }}
                </p> --}}
            </div>

            
        </div>
    @empty
        <div class="text-center py-12">
            <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500">No media found</p>
        </div>
    @endforelse
</div>