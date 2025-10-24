<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="lazuardy",
 * description="API registrasi",
 * @OA\Contact(
 * email="yafinuqmanelianto@mail.ugm.ac.id",
 * )
 * )
 * 
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Server Development (Lokal)"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
