<?php

return [
     'default_tab' => env('MEDIA_MANAGER_DEFAULT_TAB', 'select'),
    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */
    'permission' => false,
    'permissions' => [
        'upload' => env('MEDIA_MANAGER_PERMISSION_UPLOAD', 'media'),
        'delete' => env('MEDIA_MANAGER_PERMISSION_DELETE', 'media'),
        'view_all' => env('MEDIA_MANAGER_PERMISSION_VIEW_ALL', 'media'),
        'bulk_delete' => env('MEDIA_MANAGER_PERMISSION_BULK_DELETE', 'media')
    ],

  
    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => env('MEDIA_MANAGER_DISK', 'public'),
        'directory' => env('MEDIA_MANAGER_DIRECTORY', 'media'),
        'thumbnails_directory' => env('MEDIA_MANAGER_THUMBNAILS_DIRECTORY', 'media/thumbnails'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    */
    'allowed_types' => [
        'image' => [
            'enabled' => true,
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'],
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/svg+xml', 'image/webp'],
            'max_size' => 5120,
            'thumbnail' => true,
        ],
        'video' => [
            'enabled' => true,
            'extensions' => ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'mpeg'],
            'mime_types' => ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-matroska', 'video/webm'],
            'max_size' => 102400,
            'thumbnail' => false,
        ],
        'audio' => [
            'enabled' => false,
            'extensions' => ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'],
            'mime_types' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/flac'],
            'max_size' => 20480,
            'thumbnail' => false,
        ],
        'document' => [
            'enabled' => true,
            'extensions' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
            'mime_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'],
            'max_size' => 10240,
            'thumbnail' => false,
        ],
        'spreadsheet' => [
            'enabled' => true,
            'extensions' => ['xls', 'xlsx', 'csv'],
            'mime_types' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'],
            'max_size' => 10240,
            'thumbnail' => false,
        ],
        'presentation' => [
            'enabled' => false,
            'extensions' => ['ppt', 'pptx'],
            'mime_types' => ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'max_size' => 20480,
            'thumbnail' => false,
        ],
        'archive' => [
            'enabled' => false,
            'extensions' => ['zip', 'rar', '7z', 'tar', 'gz'],
            'mime_types' => ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'],
            'max_size' => 51200,
            'thumbnail' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters Configuration
    |--------------------------------------------------------------------------
    */
    'filters' => [
        'all' => [
            'label' => 'All Files',
            'icon' => 'fa-th',
            'color' => 'gray',
            'enabled' => true,
        ],
        'image' => [
            'label' => 'Images',
            'icon' => 'fa-image',
            'color' => 'blue',
            'enabled' => true,
            'type' => 'image',
        ],
        'video' => [
            'label' => 'Videos',
            'icon' => 'fa-video',
            'color' => 'purple',
            'enabled' => true,
            'type' => 'video',
        ],
        'document' => [
            'label' => 'Documents',
            'icon' => 'fa-file-alt',
            'color' => 'red',
            'enabled' => true,
            'type' => 'document',
        ],
        'spreadsheet' => [
            'label' => 'Spreadsheets',
            'icon' => 'fa-file-excel',
            'color' => 'green',
            'enabled' => true,
            'type' => 'spreadsheet',
        ],
        'pdf' => [
            'label' => 'PDF Files',
            'icon' => 'fa-file-pdf',
            'color' => 'red',
            'enabled' => true,
            'type' => 'document',
            'extensions' => ['pdf'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    */
    'upload' => [
        'max_files' => 20,
        'max_total_size' => 51200,
        'chunk_size' => 1048576,
        'enable_chunking' => false,
        'parallel_uploads' => 3,
        'auto_process' => true,
    ],

  
    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'per_page' => 12,
        'per_page_options' => [12, 24, 48, 96],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sorting Options
    |--------------------------------------------------------------------------
    */
    'sorting' => [
        'default' => 'newest',
        'options' => [
            'newest' => ['label' => 'Newest First', 'field' => 'created_at', 'direction' => 'desc'],
            'oldest' => ['label' => 'Oldest First', 'field' => 'created_at', 'direction' => 'asc'],
            'name_asc' => ['label' => 'Name (A-Z)', 'field' => 'name', 'direction' => 'asc'],
            'name_desc' => ['label' => 'Name (Z-A)', 'field' => 'name', 'direction' => 'desc'],
            'size_desc' => ['label' => 'Largest First', 'field' => 'size', 'direction' => 'desc'],
            'size_asc' => ['label' => 'Smallest First', 'field' => 'size', 'direction' => 'asc'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | View Modes
    |--------------------------------------------------------------------------
    */
    'view_modes' => [
        'default' => 'grid',
        'available' => ['grid', 'list'],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'theme' => 'tailwind',
        'icons' => [
            'library' => 'fontawesome',
            'set' => 'free-solid-svg-icons',
        ],
        'language' => 'en',
        'date_format' => 'Y-m-d H:i:s',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'allowed_urls' => [],
        'validate_mime' => true,
        'scan_virus' => false,
        'sanitize_filenames' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Events & Hooks
    |--------------------------------------------------------------------------
    */
    'events' => [
        'before_upload' => null,
        'after_upload' => null,
        'before_delete' => null,
        'after_delete' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'prefix' => 'media-manager',
        'middleware' => ['admin'],
    ],
];