<div class="media-selector group" data-input-name="{{ $inputName }}">

    <div class="media-selector-trigger flex items-center justify-between px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50/5 dark:hover:bg-blue-900/10 transition-all cursor-pointer"
        data-input="{{ $inputName }}" data-multiple="{{ $options['multiple'] ? 'true' : 'false' }}"
        data-accept="{{ $options['accept'] }}">
        <div class="flex items-center gap-3">
            <i class="fas fa-image text-gray-400 dark:text-gray-500 text-lg"></i>
            <span class="text-sm text-gray-600 dark:text-gray-300">
                {{ $options['buttonText'] }}
            </span>
            @if($options['multiple'])
                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    Multiple
                </span>
            @endif
        </div>
        {{-- Preview Section - Minimal --}}
        @if($options['preview'])
            <div class="mt-3">

                {{-- Preview Grid --}}
                <div id="{{ $inputName }}_preview" class="flex flex-wrap gap-2">
                    @php
                        $value = $options['value'];
                        if (is_string($value)) {
                            $value = strlen($value) ? explode(',', $value) : [];
                        }
                        if (!is_array($value)) {
                            $value = [];
                        }
                    @endphp

                    @foreach($value as $index => $url)
                        @if($url)
                            <div class="preview-item-wrapper relative group" data-url="{{ $url }}">
                                <div
                                    class="relative w-12 h-12 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    @if(preg_match('/\.(jpg|jpeg|png|webp|gif|svg)$/i', $url))
                                        <img src="{{ $url }}" class="w-full h-full object-cover">
                                    @elseif(preg_match('/\.(mp4|webm|mov)$/i', $url))
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-video text-gray-500 text-lg"></i>
                                        </div>
                                    @elseif(preg_match('/\.pdf$/i', $url))
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-gray-500 text-lg"></i>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-file text-gray-500 text-lg"></i>
                                        </div>
                                    @endif

                                    {{-- Remove Button --}}
                                    <button type="button" class="remove-preview absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white 
                                                       flex items-center justify-center opacity-0 group-hover:opacity-100 
                                                       hover:bg-red-600 transition-all duration-200" data-url="{{ $url }}">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        @endif
        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500 text-xs"></i>
    </div>



    {{-- Hidden Input --}}
    <input type="hidden" name="{{ $inputName }}" id="media-{{ $inputName }}"
        value="{{ is_array($value) ? implode(',', $value) : $value }}">
</div>

<style>
    .preview-item-wrapper {
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .media-selector-trigger {
        transition: all 0.2s ease;
    }
</style>

<script>
    $(document).ready(function () {
        $('.clear-all-preview').on('click', function () {
            const inputName = $(this).data('input');
            if (window.mediaManager) {
                window.mediaManager.selectedMediaUrls = [];
                window.mediaManager.renderSelectorPreview();
            }
        });
    });
</script>