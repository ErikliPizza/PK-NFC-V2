<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;
use Illuminate\Support\Facades\App;
class Translations extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View|Closure|string
    {
        $locale = App::getLocale();
        $jsonTranslations = [];
        $phpTranslations = [];

        // Check if PHP translation files exist for the current locale
        if (File::exists(base_path("lang/$locale"))) {
            // Collect PHP translations
            $phpTranslations = collect(File::allFiles(base_path("lang/$locale")))
                ->filter(function ($file) {
                    return $file->getExtension() === "php";
                })->flatMap(function ($file) {
                    return Arr::dot(File::getRequire($file->getRealPath()));
                })->toArray();
        }

        // Check if JSON translation file exists for the current locale
        if (File::exists(base_path("lang/$locale.json"))) {
            // Parse JSON translations
            $jsonTranslations = json_decode(File::get(base_path("lang/$locale.json")), true);
        }

        // Merge PHP and JSON translations
        $translations = array_merge($phpTranslations, $jsonTranslations);

        // Return the view with the translations
        return view('components.translations', [
            'translations' => $translations
        ]);
    }

}
