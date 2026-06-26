<div class="media-gallery grid grid-cols-{{ $options['columns'] }} gap-4">
    @forelse($media as $item)
        <div class="media-item group relative border rounded-lg overflow-hidden hover:shadow-lg transition-all">
            <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                @if($item->type === 'image')
                    <img src="{{ $item->url ?? $item->url }}" alt="{{ $item->name }}" 
                        class="w-full h-32 object-cover">
                @else
                    <div class="w-full h-32 flex items-center justify-center">
                        <i class="fas {{ $item->extension === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-file text-gray-400' }} text-4xl"></i>
                    </div>
                @endif
            </div>
            
            <div class="p-2 bg-white">
                <p class="text-xs truncate">{{ Str::limit($item->name, 20) }}</p>
                <p class="text-xs text-gray-500">{{ $item->getHumanReadableSize() }}</p>
            </div>
            
            @if($options['showSelect'])
            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <button onclick="window.mediaManager?.selectMedia('{{ $item->url }}', '{{ asset("/") }}', '{{ $item->size }}', '', '')"
                    class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">
                    Select
                </button>
            </div>
            @endif
            
            @if($options['showDelete'])
            <button onclick="window.mediaManager?.deleteMedia({{ $item->id }})"
                class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-trash-alt text-xs"></i>
            </button>
            @endif
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500">No media found</p>
        </div>
    @endforelse
</div>