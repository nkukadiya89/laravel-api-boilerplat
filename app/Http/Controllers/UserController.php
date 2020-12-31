<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Validators\ProfileValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * @SWG\Tag(
 *   name="User-Controller",
 *   description="API",
 * )
 */
class UserController extends Controller
{
    protected $profileValidator;

    public function __construct()
    {
        $this->profileValidator = new ProfileValidator();
    }

    /**
     * @SWG\Get(
     *   path="/user",
     *   tags={"User-Controller"},
     *   summary="Get User List",
     *   description="Get User List",
     *   operationId="indexUser",
     *   produces={"application/json"},
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response="200",description="Get User List"),
     *   @SWG\Response(response="500",description="Please contact support team."),
     * )
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexUser(Request $request)
    {
        Log::info('Start Get User List:');
        try {
            $users = User::all();
            Log::info('End Get User Data');
            return $this->sendSuccessResponse(Lang::get('messages.general.success'), 200, $users);
        } catch (\Exception $ex) {
            Log::info('Exception in get User');
            Log::error($ex);
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 500);
        }
    }

    /**
     * @SWG\Get(
     *   path="/profile",
     *   tags={"User-Controller"},
     *   summary="Get User Profile",
     *   description="Get User Data",
     *   operationId="showUser",
     *   produces={"application/json"},
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response="200",description="Get User Data"),
     *   @SWG\Response(response="500",description="Please contact support team."),
     * )
     */
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showUser(Request $request)
    {
        $isUser = $request->user()->token();

        if (!$isUser) {
            return $this->sendFailedResponse(Lang::get('messages.user.auth_token_not_found'), 401);
        }
        $member = $this->getCurrentUser();
        return $this->sendSuccessResponse('Get User Successfully.', 200, $member);
    }

    /**
     * @SWG\Post(
     *   path="/profile",
     *   tags={"User-Controller"},
     *   summary="Update User",
     *   description="Update User Profile API",
     *   operationId="updateUser",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Full Name",
     *     required=true,
     *     type="string"
     *   ),
     *   security={
     *       {"api_key": {}}
     *   },
     *   @SWG\Response(response="200",description="User Profile Updated successfully."),
     *   @SWG\Response(response="422",description="Validation Error"),
     *   @SWG\Response(response="500",description="Please contact support team.")
     * )
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request)
    {
        Log::info('Start Update user Profile:');
        DB::beginTransaction();
        try {
            $inputs = $request->all();
            $user = $request->user();
            $isUser = $user->token();

            if (!$isUser) {
                return $this->sendFailedResponse(Lang::get('messages.user.auth_token_not_found'), 401);
            }

            $validator = $this->profileValidator->insertValidation($inputs);

            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            User::where('id', $user->id)->update(['name' => $inputs['name']]);
            $memberData = $user->fresh();

            DB::commit();
            Log::info('End code for Update user Profile.');
            return $this->sendSuccessResponse('User Profile Update Successfully.', 200, $memberData);
        } catch (\Exception $ex) {
            Log::info('Exception in update user profile');
            Log::error($ex);
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 500);
        }
    }
}
