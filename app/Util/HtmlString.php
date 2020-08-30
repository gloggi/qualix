<?php

namespace App\Util;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString as LaravelHtmlString;
use InvalidArgumentException;

class HtmlString extends LaravelHtmlString implements Htmlable {
    public function __construct($html = null) {
        parent::__construct($html);
        if ($html) {
            throw new InvalidArgumentException('HtmlString constructor may not be called with an argument');
        }
        $this->html = '';
    }

    /**
     * Append a safe string (not containing user input) to the HTML.
     * The string will not be escaped.
     *
     * @param string|Htmlable $safe
     * @return $this
     */
    public function s($safe) {
        return $this->append($safe);
    }

    /**
     * Append a safe string (not containing user input) to the HTML.
     * The string will not be escaped.
     *
     * @param string $safe
     * @return $this
     */
    public function append($safe) {
        $this->html .= $safe;
        return $this;
    }

    /**
     * Append an internationalized safe string (not containing user input) to the HTML.
     * The translated string will not be escaped.
     * This method should only be used when the translation contains HTML code that should be interpreted by the browser.
     * Otherwise, the method __(...) should be used.
     *
     * @param array $arguments
     * @return $this
     */
    public function __s(...$arguments) {
        return $this->append(__(...$arguments));
    }

    /**
     * Append an unsafe string (possibly containing user input) to the HTML.
     * The string will be escaped using htmlspecialchars.
     *
     * @param string|Htmlable $escapable
     * @return $this
     */
    public function e($escapable) {
        return $this->appendEscaping($escapable);
    }

    /**
     * Append an unsafe string (possibly containing user input) to the HTML.
     * The string will be escaped using htmlspecialchars.
     *
     * @param string|Htmlable $escapable
     * @return $this
     */
    public function appendEscaping($escapable) {
        if ($escapable instanceof Htmlable) {
            return $this->append($escapable->toHtml());
        }
        $this->html .= htmlspecialchars($escapable, ENT_QUOTES, 'UTF-8');
        return $this;
    }

    /**
     * Append an internationalized unsafe string (possibly containing user input) to the HTML.
     * The translated string will be escaped using htmlspecialchars.
     *
     * @param array $arguments
     * @return $this
     */
    public function __e(...$arguments) {
        return $this->appendEscaping(__(...$arguments));
    }

    /**
     * Append an internationalized unsafe string (possibly containing user input) to the HTML.
     * The translated string will be escaped using htmlspecialchars.
     *
     * @param array $arguments
     * @return $this
     */
    public function __(...$arguments) {
        return $this->appendEscaping(__(...$arguments));
    }

    /**
     * Append an internationalized pluralized unsafe string (possibly containing user input) to the HTML.
     * The translated string will be escaped using htmlspecialchars.
     *
     * @param array $arguments
     * @return $this
     */
    public function trans_choice_e(...$arguments) {
        return $this->appendEscaping(trans_choice(...$arguments));
    }

    /**
     * Append an internationalized pluralized unsafe string (possibly containing user input) to the HTML.
     * The translated string will be escaped using htmlspecialchars.
     *
     * @param array $arguments
     * @return $this
     */
    public function trans_choice(...$arguments) {
        return $this->appendEscaping(trans_choice(...$arguments));
    }

    /**
     * Replace one or more search strings with corresponding replace strings. The replacements will be escaped
     * (except if they're HtmlStrings themselves).
     *
     * @param $search
     * @param string|Htmlable $replace
     * @return $this
     */
    public function replace($search, $replace) {
        $search = Arr::wrap($search);
        $replace = array_map(function ($r) {
            return e($r);
        }, Arr::wrap($replace));
        $this->html = str_replace($search, $replace, $this->html);
        return $this;
    }
}
