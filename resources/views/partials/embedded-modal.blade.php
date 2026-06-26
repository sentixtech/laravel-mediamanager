<!-- Embedded Media Selector Modal -->
<div id="{{ $options['modalId'] }}" class="media-modal fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select Media</h3>
                <button onclick="closeModal('{{ $options['modalId'] }}')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-4 overflow-y-auto" style="max-height: calc(90vh - 120px)">
                <div id="media-selector-container"></div>
            </div>
            
            <div class="p-4 border-t dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeModal('{{ $options['modalId'] }}')" class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button onclick="window.mediaManager?.confirmSelection()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Insert
                </button>
            </div>
        </div>
    </div>
        {{-- <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div> --}}

</div>

@if($options['triggerButton'])
<button type="button" onclick="openMediaModal('{{ $options['modalId'] }}', '{{ $options['inputName'] }}', {{ $options['multiple'] ? 'true' : 'false' }})" 
    class="{{ $options['buttonClass'] }}">
    <i class="fas fa-image mr-2"></i>{{ $options['buttonText'] }}
</button>
@endif

@if($options['preview'])
<div id="{{ $options['previewId'] }}" class="media-preview mt-3 flex flex-wrap gap-2"></div>
@endif

<input type="hidden" name="{{ $options['inputName'] }}" id="media-{{ $options['inputName'] }}" value="{{ $options['value'] ?? '' }}">

<script>
function openMediaModal(modalId, inputName, multiple) {
    if (window.mediaManager) {
        window.mediaManager.inputNameId = inputName;
        window.mediaManager.multiple = multiple;
        window.mediaManager.selectedMediaUrls = [];
        
        var existingValue = $('#media-' + inputName).val();
        if (existingValue) {
            window.mediaManager.selectedMediaUrls = existingValue.split(',');
            window.mediaManager.updatePreviewFromSelectedUrls();
        }
        
        window.mediaManager.selectMode = 'select';
        window.mediaManager.loadMediaForSelection();
        openModal(modalId);
    }
}
</script>