<?php

namespace App\Traits;

trait ResponseTrait
{
    public function sendSuccessResponse($message = "", $code = 200, $data = [], $other_data = [])
    {
        $jsonData = array();
        $jsonData['result'] = is_null($data) ? [] : $data;
        $jsonData['other_result'] = is_null($other_data) ? [] : $other_data;
        $jsonData['error'] = false;
        $jsonData['message'] = $message;
        $jsonData['status_code'] = $code;
        return response()->json($jsonData, $code);
    }

    public function sendFailedResponse($message = "", $code = 400, $data = [], $additionalData = [])
    {
        $jsonData = array();
        $jsonData['result'] = $data;
        $jsonData['other_result'] = $additionalData;
        $jsonData['error'] = true;
        $jsonData['status_code'] = $code;
        $jsonData['message'] = $message;
        if ($data) {
            $jsonData['error']['errors'] = $data;
        }
        if ($additionalData && is_array($additionalData)) {
            foreach ($additionalData as $key => $value) {
                $jsonData['error'][$key] = $value;
            }
        }
        return response()->json($jsonData, $code);
    }

    public function noRecordResponse($message = "", $code = 404, $data = [], $additionalData = [])
    {
        $jsonData = array();
        $jsonData['result'] = $data;
        $jsonData['other_result'] = $additionalData;
        $jsonData['error'] = true;
        $jsonData['message'] = $message;
        $jsonData['status_code'] = $code;
        return response()->json($jsonData, $code);
    }

    public function sendCustomErrorMessage($message = array(), $code = 422)
    {
        $jsonData = [];
        $errors = '';
        foreach ($message as $key => $error) {
            $errors = $error[0];
            break;
        }
        $jsonData['result'] = [];
        $jsonData['other_result'] = [];
        $jsonData['error'] = true;
        $jsonData['message'] = $errors;
        $jsonData['status_code'] = $code;
        return response()->json($jsonData, $code);
    }
}
