<?php

return [
    'delete' => 'Are you sure',
    'authenticate' => [
        'logout' => 'You have been successfully logged out.',
        'success' => 'You have been successfully logged in.',
        'account_active' => "Your account is inactive. Please contact to administrator."
    ],
    'general' => [
        '200' => "success",
        'laravel_error' => 'Please contact support team.',
        'success' => 'Get List Successfully.',
        'unauthenticated' => 'Not authenticated.',
        'access_error' => 'You are not authenticated.',
        'not_found' => 'Data not found',
        'success-show' => 'Get Data Successfully.',
    ],
    'user' => [
        'auth_token_not_found' => 'Authorization token not found',
        'token_invalid' => 'Invalid token',
        'token_expired' => 'Token expired',
        'store' => 'User registered successfully.',
        'success' => 'Successfully get user details.',
        'not-found' => 'User not found'
    ],
    'authenticate' => [
        'validate' => 'Email and password are required.',
        'cred_invalid' => 'Credentials are invalid',
        'account_active' => 'Please contact the admin for your account activation',
        'exception' => 'Could not create token',
        'success' => 'User Login Successfully',
        'logout' => 'User Logout Successfully',
        'register' => "Registration successfully"
    ],
    'forgot-password' => [
        'email' => "Email Address doesn't exists",
        'success' => 'Password Change successfully, Please check your registered email address.',
    ],
    'change-password' => [
        'email' => "Email Address doesn't exists",
        'success' => 'Password changed successfully',
        'password_not_match' => 'Old password does not correct.',
        'not-found' => 'Record Not Found.',
        'old_password_wrong' => 'Old password is wrong'
    ],
];
