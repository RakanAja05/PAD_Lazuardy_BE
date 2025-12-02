<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="PAD Lazuardy API",
 *     description="API Documentation untuk aplikasi tutor matching PAD Lazuardy - Platform untuk menghubungkan siswa dengan tutor berkualitas",
 *     @OA\Contact(
 *         email="yafinuqmanelianto@mail.ugm.ac.id",
 *         name="PAD Lazuardy Team"
 *     ),
 *     @OA\License(
 *         name="Private",
 *         url=""
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Development Server (Local)"
 * )
 * 
 * @OA\Server(
 *     url="https://api.padlazuardy.com",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum Bearer Token. Dapatkan token dari endpoint login, lalu masukkan di sini."
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints untuk login, register, dan logout"
 * )
 * 
 * @OA\Tag(
 *     name="Admin - Tutor Verification",
 *     description="Endpoints untuk admin verifikasi tutor"
 * )
 * 
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Dashboard untuk student dan tutor"
 * )
 * 
 * @OA\Tag(
 *     name="Find Tutor",
 *     description="Pencarian tutor dengan geospatial search"
 * )
 * 
 * @OA\Tag(
 *     name="Profile",
 *     description="Manajemen profile user"
 * )
 * 
 * @OA\Tag(
 *     name="Payment & Orders",
 *     description="Pembayaran dan order paket belajar"
 * )
 * 
 * @OA\Tag(
 *     name="Schedule",
 *     description="Jadwal belajar student dan tutor"
 * )
 * 
 * @OA\Tag(
 *     name="Reviews",
 *     description="Review dan rating tutor"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
