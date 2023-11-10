<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Spatie\LaravelIgnition\Support\Composer\ComposerClassMap;

trait Helper
{
    public static function replaceArrayKey(array &$item, $oldKey, $newKey): void
    {
        $item[$newKey] = $item[$oldKey];
        unset($item[$oldKey]);
    }

    public function getAvailableClasses($includedNamespace): array
    {
        $namespaces = array_keys((new ComposerClassMap)->listClasses());

        return array_filter($namespaces, function ($item) use ($includedNamespace) {
            return Str::startsWith($item, $includedNamespace);
        });
    }

    protected function isProduction()
    {
        return app()->environment('production');
    }
}
