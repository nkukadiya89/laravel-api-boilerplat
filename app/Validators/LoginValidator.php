<?php

namespace App\Validators;

class LoginValidator extends ModelValidator
{
    protected $languageArray = 'validation.login';

    private $inputRules = [
        'email' => 'required',
        'password' => 'required',
    ];

    private $inputPassword = [
        'old_password' => 'required',
        'password' => 'required',
        'password_confirm' => 'required|same:password',
    ];

    private $registerRules = [
        'email' => 'required|string|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,NULL,id',
        'password' => 'required|min:6',
    ];

    private $inputRulesForgotPassword = [
        'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
    ];

    public function validateLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->inputRules);
    }

    public function validateRegister($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->registerRules);
    }

    public function validateForgot($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->inputRulesForgotPassword);
    }

    public function validateChangePassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->inputPassword);
    }
}
