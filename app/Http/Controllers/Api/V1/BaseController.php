<?php

namespace App\Http\Controllers\Api\V1;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Routing\Helpers;

class BaseController extends Controller
{
    use Helpers;

    function errorBadRequest($validation)
    {
        $result = [];
        $messages = $validation->errors()->toArray();

        if ($messages) {
            foreach ($messages as $field => $errors) {
                foreach ($errors as $error) {
                    $result[] = [
                        'field' => $field,
                        'code' => $error,
                    ];
                }
            }
        }

        throw new ValidationHttpException($result);
    }
}
