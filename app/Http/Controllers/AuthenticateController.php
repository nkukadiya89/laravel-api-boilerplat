<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\User;
use App\Validators\LoginValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class User
 */
/**
 * @SWG\Tag(
 *   name="Authenticate-Controller",
 *   description="Authenticate Controller API List",
 * )
 */
class AuthenticateController extends Controller
{
    private $loginValidator;
    private $errorMessage;

    public function __construct()
    {
        $this->loginValidator = new LoginValidator();
        $this->errorMessage = Lang::get('messages.general.laravel_error');
    }

    /**
     * @SWG\Post(
     *   path="/register",
     *   tags={"Authenticate-Controller"},
     *   summary="App user Register API",
     *   description="Register API",
     *   operationId="register",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Full Name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Email Address",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="Password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="200",description="Register Successfully."),
     *   @SWG\Response(response="400",description="Sorry Registration is not successfully."),
     *   @SWG\Response(response="422",description="Validation Error"),
     *   @SWG\Response(response="500",description="Please contact support team.")
     * )
     */
    /**
     * User Registration
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        Log::info('Start code for the user register');
        DB::beginTransaction();
        try {
            $inputs = $request->all();
            $validator = $this->loginValidator->validateRegister($inputs);

            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            $userData = [
                'email' => $inputs['email'],
                'name' => $inputs['name'],
                'password' => Hash::make($inputs['password']),
            ];

            $user = User::create($userData);

            if (!$user) {
                DB::rollBack();
                $error = "Sorry! Registration is not successfully.";
                return $this->sendFailedResponse($error, 400);
            }
            DB::commit();

            $credentials = ['email' => $inputs['email'], 'password' => $inputs['password']];
            $token = JWTAuth::attempt($credentials);
            $token = 'Bearer ' . $token;
            return $this->sendSuccessResponse("Registration successfully.", 200, compact('token', 'user'));
        } catch (\Exception $ex) {
            Log::info('Exception in the user Register');
            Log::error($ex);
            DB::rollBack();
            return $this->sendFailedResponse($this->errorMessage, 500);
        }
    }

    /**
     * @SWG\Post(
     *   path="/login",
     *   tags={"Authenticate-Controller"},
     *   summary="App user Login API",
     *   description="Login API",
     *   operationId="login",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Email Address",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="Password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="200",description="Login Successfully."),
     *   @SWG\Response(response="422",description="Validation Error"),
     *   @SWG\Response(response="500",description="Please contact support team.")
     * )
     */
    /**
     * User Login
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        Log::info('Start code for the user login');
        try {
            $inputs = $request->all();

            $validation = $this->loginValidator->validateLogin($inputs);

            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }

            $credentials = ['email' => $inputs['email'], 'password' => $inputs['password']];

            if (!$token = JWTAuth::attempt($credentials)) {
                Log::info('End code for validation Error.');
                return $this->sendFailedResponse(Lang::get('messages.authenticate.cred_invalid'), 401);
            }

            $userData = Auth::id();

            if (!$userData) {
                return $this->sendFailedResponse(Lang::get('messages.authenticate.account_active'), 422);
            }

            $token = 'Bearer ' . $token;

            $user = User::find($userData);
            return $this->sendSuccessResponse(Lang::get('messages.authenticate.success'), 200, compact('token', 'user'));
        } catch (JWTException $ex) {
            Log::info('Exception in the user Login');
            Log::error($ex);
            return $this->sendFailedResponse($this->errorMessage, 500);
        }
    }

    /**
     * @SWG\Post(
     *   path="/logout",
     *   summary="APP Logout API",
     *   tags={"Authenticate-Controller"},
     *   description="APP Logout API",
     *   operationId="logout",
     *   produces={"application/json"},
     *   @SWG\Response(response="200",description="User Logout Successfully"),
     *   @SWG\Response(response="500",description="Please contact support team."),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    /**
     * User Logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Log::info('start code for user Logout:');
        try {
            if (!$token = JWTAuth::getToken()) {
                return $this->sendFailedResponse(Lang::get('messages.general.unauthenticated'), 401);
            }

            JWTAuth::invalidate($token);

            Log::info('end code for user Logout:');
            return $this->sendSuccessResponse(Lang::get('messages.authenticate.logout'), 200);
        } catch (\Exception $ex) {
            Log::info('Exception in the user logout:');
            Log::error($ex);
            return $this->sendFailedResponse($this->errorMessage, 500);
        }
    }

    /**
     * @SWG\Post(
     *   path="/forgot-password",
     *   tags={"Authenticate-Controller"},
     *   summary="APP Forgot Password API",
     *   description="APP Forgot Password API",
     *   operationId="forgotPassword",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Email Address",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="200",description="Password Change successfully. Please check your registered email address."),
     *   @SWG\Response(response="422",description="Validation Error"),
     *   @SWG\Response(response="500",description="Please contact support team.")
     * )
     */
    /**
     * Forgot Password
     *
     * @param Request $request
     * @return void
     */
    public function forgotPassword(Request $request)
    {
        try {
            Log::info('start code for user forgot password send mail');
            $inputs = $request->all();

            $validation = $this->loginValidator->validateForgot($inputs);

            if ($validation->fails()) {
                Log::info('End code for validation Error.');
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }

            $email = $inputs['email'];
            $user = User::where('email', $email)->first();
            if (empty($user)) {
                return $this->sendFailedResponse(Lang::get('messages.forgot-password.email'), 422);
            }

            $password = Str::random(10);
            User::where('email', $email)->update(['password' => Hash::make($password)]);

            $user = $user->fresh();

            Mail::to($user->email)->send(new ForgotPassword($user, $password));

            Log::info('End code for user forgot password send mail');
            return $this->sendSuccessResponse(Lang::get('messages.forgot-password.success'), 200);
        } catch (\Exception $e) {
            Log::info('Exception in forgotpassword mail');
            Log::info($e);
            return $this->sendFailedResponse($this->errorMessage, 500);
        }
    }

    /**
     * @SWG\Post(
     *   path="/change-password",
     *   tags={"Authenticate-Controller"},
     *   summary="APP Change Password API",
     *   description="",
     *   operationId="changePassword",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="old_password",
     *     in="formData",
     *     description="Old Password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="Password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password_confirm",
     *     in="formData",
     *     description="Password Confirm",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="200",description="Password changed successfully."),
     *   @SWG\Response(response="422",description="Validation Error"),
     *   @SWG\Response(response="500",description="Please contact support team."),
     *   security={
     *       {"api_key": {}}
     *   }
     * )
     */
    /**
     * Change Password
     *
     * @param Request $request
     * @return void
     */
    public function changePassword(Request $request)
    {
        Log::info('start code for change password');
        try {
            $inputs = $request->all();
            $validation = $this->loginValidator->validateChangePassword($inputs);

            if ($validation->fails()) {
                Log::info('End code for validation Error.');
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }

            $password = $inputs['password'];
            $userId = $request->user()->id;

            if (!$request->user()->token()) {
                return $this->sendFailedResponse(Lang::get('messages.change-password.not-found'), 404);
            }

            $old_password = Auth::User()->password;
            if (Hash::check($request['old_password'], $old_password)) {
                $user = User::where('id', $userId)->update([
                    'password' => Hash::make($password)
                ]);
                // mail code end
                Log::info('End code for change password');
                return $this->sendSuccessResponse(Lang::get('messages.change-password.success'), 200);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.change-password.old_password_wrong'), 404);
            }
        } catch (\Exception $e) {
            Log::info('Exception in change password');
            Log::info($e);
            return $this->sendFailedResponse($this->errorMessage, 400);
        }
    }
}
