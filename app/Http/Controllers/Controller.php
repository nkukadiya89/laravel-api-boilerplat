<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Routing\Controller as BaseController;

/**
 * @license Apache 2.0
 */

/**
 * @SWG\Swagger(
 *     host=L5_SWAGGER_CONST_HOST,
 *     basePath=L5_SWAGGER_CONST_PATH,
 *     @SWG\Info(
 *         version=L5_SWAGGER_CONST_VERSION,
 *         title=L5_SWAGGER_CONST_TITLE,
 *         description=L5_SWAGGER_CONST_DESCRIPTION,
 *         termsOfService="http://swagger.io/terms/",
 *         @SWG\Contact(
 *             email=L5_SWAGGER_CONST_EMAIL
 *         ),
 *         @SWG\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseTrait;

    /**
     * Get JWT Token User Data
     *
     * @return void
     */
    public function getCurrentUser()
    {
        if ($token = JWTAuth::getToken()) {
            return JWTAuth::parseToken()->authenticate();
        } else {
            return null;
        }
    }
}
