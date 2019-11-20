<?php

namespace App\Util;

use Illuminate\Support\Str;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends LaravelTranslator
{

    public function __construct(LaravelTranslator $original) {
        parent::__construct($original->loader, $original->locale);
        $this->setFallback($original->getFallback());
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string  $line
     * @param  array   $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace)
    {
        if (!count(array_filter(array_values($replace), function($value) {
            return $value instanceof \Illuminate\Support\HtmlString;
        }))) {
            return parent::makeReplacements($line, $replace);
        }

        $replace = $this->sortReplacements($replace);

        $line = (new HtmlString())->e($line);

        foreach ($replace as $key => $value) {
            if ($value instanceof \Illuminate\Support\HtmlString) {
                $line->replace(':' . $key, $value);
            } else if (is_string($value)) {
                $line->replace(
                    [':' . $key, ':' . Str::upper($key), ':' . Str::ucfirst($key)],
                    [$value, Str::upper($value), Str::ucfirst($value)],
                    $line
                );
            }
        }

        return $line;
    }
}
