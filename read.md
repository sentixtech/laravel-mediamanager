# 📁 Sentix Media Manager

> A comprehensive media management package for Laravel with beautiful UI, built with Tailwind CSS.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-9.x%20%7C%2010.x%20%7C%2011.x%20%7C%2012.x%20%7C%2013.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%20%7C%208.1%20%7C%208.2%20%7C%208.3%20%7C%208.4%20%7C%208.5-purple.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-^4.1.13-38B2AC.svg)

## 📋 Table of Contents

- [✨ Features](#-features)
- [🚀 Installation](#-installation)
- [⚙️ Configuration](#️-configuration)
- [🎨 Usage](#-usage)
  - [Blade Directives](#blade-directives)
  - [Blade Components](#blade-components)
  - [Facade Methods](#facade-methods)
- [📁 File Management](#-file-management)
- [🎯 Features Details](#-features-details)
- [🎨 Customization](#-customization)
- [🔒 Security](#-security)
- [📚 API Reference](#-api-reference)
- [🐛 Troubleshooting](#-troubleshooting)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

---

## ✨ Features

- 📤 **Drag & Drop Upload** - Intuitive file upload with progress
- 🖼️ **Multiple File Types** - Images, Videos, Documents, Spreadsheets
- 🎨 **Tailwind CSS UI** - Modern, responsive, and customizable
- 🏷️ **Smart Filters** - Filter files by type, date, and size
- 🔍 **Search & Sort** - Quick file search with multiple sort options
- 📋 **Grid & List Views** - Toggle between viewing modes
- 🗂️ **Bulk Operations** - Select and delete multiple files
- 🔒 **Permission System** - Granular access control
- 📱 **Mobile Responsive** - Works on all devices
- 🔄 **Event System** - Hooks for before/after operations

---

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require sentix/laravel-mediamanager
```

### Step 2: Publish Assets

```bash
# Publish configuration
php artisan vendor:publish --provider="Sentix\MediaManager\MediaManagerServiceProvider" --tag=media-config

# Publish views (optional - for customization)
php artisan vendor:publish --provider="Sentix\MediaManager\MediaManagerServiceProvider" --tag=media-views

# Publish assets (CSS/JS)
php artisan vendor:publish --provider="Sentix\MediaManager\MediaManagerServiceProvider" --tag=media-assets

# Run migrations
php artisan migrate
```

### Step 3: Add Alias (Optional)

Add to `config/app.php`:

```php
'aliases' => [
    // ...
    'Media' => Sentix\MediaManager\Facades\Media::class,
],
```


---

## ⚙️ Configuration

### Basic Configuration (`config/media.php`)

```php
return [
    // Default tab when opening media manager
    'default_tab' => env('MEDIA_MANAGER_DEFAULT_TAB', 'select'),
    
    // Storage settings
    'storage' => [
        'disk' => env('MEDIA_MANAGER_DISK', 'public'),
        'directory' => env('MEDIA_MANAGER_DIRECTORY', 'media'),
        'thumbnails_directory' => env('MEDIA_MANAGER_THUMBNAILS_DIRECTORY', 'media/thumbnails'),
    ],
    
    // Allowed file types
    'allowed_types' => [
        'image' => [
            'enabled' => true,
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'],
            'max_size' => 5120, // 5MB
            'thumbnail' => true,
        ],
        // ... other types
    ],
    
    // Pagination
    'pagination' => [
        'per_page' => 12,
        'per_page_options' => [12, 24, 48, 96],
    ],
];
```

### Environment Variables

```env
# Storage
MEDIA_MANAGER_DISK=public
MEDIA_MANAGER_DIRECTORY=media
MEDIA_MANAGER_THUMBNAILS_DIRECTORY=media/thumbnails

# Permissions (if enabled)
MEDIA_MANAGER_PERMISSION_UPLOAD=media
MEDIA_MANAGER_PERMISSION_DELETE=media
MEDIA_MANAGER_PERMISSION_VIEW_ALL=media
MEDIA_MANAGER_PERMISSION_BULK_DELETE=media

# Default tab
MEDIA_MANAGER_DEFAULT_TAB=select
```

---

## 🎨 Usage

### Basic Usage

```blade
{{-- Include the media manager --}}
@mediaView()

{{-- Or use the Blade Component for Select Media --}}
<x-media name="files" :multiple="true" :preview="true" />

{{-- Include the modal --}}
@mediaModal()

{{-- Include the scripts --}}
@mediaScript()
```

### Blade Directives

| Directive | Description | Example |
|-----------|-------------|---------|
| `@mediaView()` | Renders the main media manager interface | `@mediaView()` |
| `@mediaModal()` | Renders the file selection modal | `@mediaModal()` |
| `@mediaScript()` | Includes required JavaScript | `@mediaScript()` |
| `@mediaSelector()` | Renders a file selector button | `@mediaSelector('myFile'['multiple' => true])` |

### Blade Components

#### Media Component

```blade
<x-media 
    name="files"           <!-- Input name -->
    :multiple="true"        <!-- Allow multiple selection -->
    :preview="true"         <!-- Show preview -->
    :max-files="5"         <!-- Maximum files -->
    :accept="['image/*']"   <!-- Accepted file types -->
    value="{{ old('files') }}" <!-- Default value -->
/>
```

### PHP Usage

```php
use Sentix\MediaManager\Facades\Media;

// Upload a file
$media = Media::upload($file, [
    'directory' => 'posts',
    'resize' => ['width' => 800, 'height' => 600],
]);

// Get media URL
$url = Media::url($media->id);

// Delete media
Media::delete($media->id);

// Get media info
$info = Media::info($media->id);
```

---

## 📁 File Management

### File Types Supported

| Category | Extensions | Max Size (KB) | Thumbnails |
|----------|------------|---------------|------------|
| **Images** | jpg, jpeg, png, gif, bmp, svg, webp, ico | 5120 | ❌ NO |
| **Videos** | mp4, avi, mov, mkv, webm, flv, mpeg | 102400 | ❌ No |
| **Audio** | mp3, wav, ogg, m4a, aac, flac | 20480 | ❌ No |
| **Documents** | pdf, doc, docx, txt, rtf | 10240 | ❌ No |
| **Spreadsheets** | xls, xlsx, csv | 10240 | ❌ No |

### Upload Methods

#### 1. Drag and Drop
Simply drag files from your computer and drop them into the upload area.

#### 2. Click to Upload
Click the upload button or drop zone to open the file picker.

#### 3. URL Upload
Add files directly from a URL (configurable).

---

## 🎯 Features Details

### File Filtering

| Filter | Description | Icon |
|--------|-------------|------|
| **All Files** | Show all uploaded files | 📁 |
| **Images** | Show only image files | 🖼️ |
| **Videos** | Show only video files | 🎬 |
| **Documents** | Show only document files | 📄 |
| **Spreadsheets** | Show only spreadsheet files | 📊 |
| **PDF Files** | Show only PDF files | 📕 |

### Sorting Options

| Option | Sort By | Direction |
|--------|---------|-----------|
| Newest First | Created Date | Descending |
| Oldest First | Created Date | Ascending |
| Name (A-Z) | Name | Ascending |
| Name (Z-A) | Name | Descending |
| Largest First | Size | Descending |
| Smallest First | Size | Ascending |

### View Modes

#### Grid View
- Shows thumbnails in a responsive grid
- Great for image browsing
- Shows file name, size, and type

#### List View
- Shows files in a table format
- Shows detailed information
- Better for file management

---

## 🎨 Customization

### Customizing Views

Publish and modify views:

```bash
php artisan vendor:publish --provider="Sentix\MediaManager\MediaManagerServiceProvider" --tag=media-views
```

Views location: `resources/views/vendor/media/`


### Adding Custom File Types

```php
// config/media.php
'allowed_types' => [
    'custom' => [
        'enabled' => true,
        'extensions' => ['custom'],
        'mime_types' => ['application/custom'],
        'max_size' => 10000,
        'thumbnail' => false,
    ],
],
```

### Custom Filters

```php
// config/media.php
'filters' => [
    'custom' => [
        'label' => 'Custom Files',
        'icon' => 'fa-custom',
        'color' => 'orange',
        'enabled' => true,
        'extensions' => ['custom'],
    ],
],
```

### Custom Events

```php
// config/media.php
'events' => [
    'before_upload' => function($file) {
        // Custom upload logic
        return $file;
    },
    'after_upload' => function($media) {
        // Custom after upload logic
        Log::info('File uploaded: ' . $media->name);
    },
    'before_delete' => function($media) {
        // Custom delete logic
        if ($media->is_protected) {
            throw new Exception('Protected file cannot be deleted');
        }
    },
    'after_delete' => function($media) {
        // Custom after delete logic
    },
],
```

---

## 🔒 Security

### Permission System

Enable permissions in config:

```php
'permission' => true,
'permissions' => [
    'upload' => 'upload-media',
    'delete' => 'delete-media',
    'view_all' => 'view-all-media',
    'bulk_delete' => 'bulk-delete-media',
],
```


### File Sanitization

Automatically sanitizes filenames:

```php
// Before: "My File (1).jpg"
// After: "my-file-1.jpg"
```

---

## 📚 API Reference

### Facade Methods

| Method | Description | Parameters | Return |
|--------|-------------|------------|--------|
| `upload()` | Upload a file | `$file, $options = []` | `Media` |
| `delete()` | Delete a file | `$id` | `bool` |
| `url()` | Get file URL | `$id` | `string` |
| `info()` | Get file info | `$id` | `array` |
| `search()` | Search files | `$query, $options = []` | `Collection` |
| `thumb()` | Get thumbnail URL | `$id, $size = 'medium'` | `string` |
| `download()` | Download file | `$id` | `Response` |
| `bulkDelete()` | Delete multiple files | `$ids` | `bool` |
| `view()` | Render view | `$options = []` | `string` |
| `modal()` | Render modal | `$options = []` | `string` |
| `script()` | Render JavaScript | `$options = []` | `string` |
| `selector()` | Render selector | `$options = []` | `string` |

### Service Methods

```php
use Sentix\MediaManager\Services\MediaService;

$mediaService = app(MediaService::class);

// Get all files
$files = $mediaService->all();

// Get paginated files
$files = $mediaService->paginate(12);

// Get files by type
$images = $mediaService->getByType('image');

// Get recent files
$recent = $mediaService->getRecent(10);

// Get file by ID
$file = $mediaService->find($id);

// Generate thumbnail
$thumb = $mediaService->generateThumbnail($file, 'small');

// Update file metadata
$mediaService->update($id, ['title' => 'New Title']);
```

---

## 🐛 Troubleshooting

### Common Issues

#### 1. Files not showing up
**Solution:** Check storage symlink:
```bash
php artisan storage:link
```

#### 2. Permission denied
**Solution:** Check folder permissions:
```bash
chmod -R 755 storage/app/public/media
```

#### 3. Thumbnails not generating
**Solution:** Ensure GD library is installed:
```bash
php -m | grep gd
```

#### 4. Upload failing
**Solution:** Check upload limits in `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 256M
```

#### 5. JavaScript not working
**Solution:** Ensure assets are published:
```bash
php artisan vendor:publish --provider="Sentix\MediaManager\MediaManagerServiceProvider" --tag=media-assets --force
```

### Debug Mode

Enable debug mode in config:

```php
'debug' => env('MEDIA_MANAGER_DEBUG', false),
```

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/sentix/laravel-mediamanager.git

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit

# Run test coverage
vendor/bin/phpunit --coverage-html coverage
```

---

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🙏 Support

- 📧 Email: shambishnoi75@gmail.com
- 🐛 Issues: [GitHub Issues](https://github.com/sentix/laravel-mediamanager/issues)


## 🎯 Quick Start Example

```blade
{{-- app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Media Manager Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Media Manager</h1>
        
        {{-- Include media manager --}}
        @mediaView()
        @mediaModal()
        @mediaScript()
    </div>
</body>
</html>
```

---

## 📝 Changelog

### v1.0.0 (2024-01-XX)
- ✨ Initial release
- 🎨 Tailwind CSS UI
- 📤 Drag and drop upload
- 🖼️ Thumbnail generation
- 🔍 File filtering and search
- 📋 Grid and list views
- 🔒 Permission system
- 🌍 Multi-language support

---

**Made with ❤️ by Vikash Bishnoi**