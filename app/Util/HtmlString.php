<?php

namespace App\Util;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString as LaravelHtmlString;
use InvalidArgumentException;

class HtmlString extends LaravelHtmlString implements Htmlable
{
    public function __construct($html = null) {
        parent::__construct($html);
        if ($html) {
            throw new InvalidArgumentException('HtmlString constructor may not be called with an argument');
        }
    }

    /**
     * Append a safe string (not containing user input) to the HTML.
     * The string will not be escaped.
     *
     * @param  string  $safe
     * @return $this
     */
    public function s($safe) {
        return $this->append($safe);
    }

    /**
     * Append a safe string (not containing user input) to the HTML.
     * The string will not be escaped.
     *
     * @param  string  $safe
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
     * @param  string  $escapable
     * @param  boolean $doubleEncode
     * @return $this
     */
    public function e($escapable, $doubleEncode = false) {
        return $this->appendEscaping($escapable, $doubleEncode);
    }

    /**
     * Append an unsafe string (possibly containing user input) to the HTML.
     * The string will be escaped using htmlspecialchars.
     *
     * @param  string  $escapable
     * @param  boolean $doubleEncode
     * @return $this
     */
    public function appendEscaping($escapable, $doubleEncode = false) {
        $this->html .= htmlspecialchars($escapable, ENT_QUOTES, 'UTF-8', $doubleEncode);
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
}