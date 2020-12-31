<?php

namespace App\Validators;

class ProfileValidator extends ModelValidator
{
    protected $languageArray = 'validation.profile';

    private $inputRules = [
        'name' => 'required'
    ];

    public function insertValidation($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->inputRules);
    }
}
