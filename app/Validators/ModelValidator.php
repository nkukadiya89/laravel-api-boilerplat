<?php

namespace App\Validators;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

abstract class ModelValidator
{
    protected function validateLaravelRules($input = [], $rules = [])
    {
        $langArray = isset($this->languageArray) ? $this->languageArray : 'validation.general';
        return Validator::make($input, $rules, Lang::get($langArray));
    }

    /**
     * Replace first occurrence of the search string with the replacement string.
     *
     * @param string $needle : The value being searched for.
     * @param string $replace : The replacement value that replaces found search  values.
     * @param string $haystack : The string being searched and replaced on.
     * @return string : The (modified) haystack.
     * @author tapken at engter dot de
     * @link http://theserverpages.com/php/manual/en/function.str-replace.php#21735
     */
    protected function str_replace_once($needle, $replace, $haystack)
    {
        if (($pos = strpos($haystack, $needle)) === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
}
