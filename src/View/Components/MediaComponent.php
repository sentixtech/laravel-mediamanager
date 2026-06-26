<?php
namespace Sentix\MediaManager\View\Components;

use Illuminate\View\Component;

class MediaComponent extends Component
{
    public string $inputName;
    public array $options;

    public function __construct(
        string $name,
        bool $multiple = false,
        bool $preview = true,
        string $accept = '*',
        int $max = 10,
        string $buttonText = 'Select Media',
        bool $required = false,
        $value = []
    ) {
        $this->inputName = $name;

        $this->options = [
            'multiple' => $multiple,
            'preview' => $preview,
            'accept' => $accept,
            'buttonText' => $buttonText,
            'value' => $value,
            'max' => $max,
            'required' => $required,
            'config' => config('media.allowed_types')
        ];
    }

    public function render()
    {
        return view('media::components.media-component');
    }
}