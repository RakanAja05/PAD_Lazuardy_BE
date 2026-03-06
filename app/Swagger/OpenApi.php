<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;



/**
 * @OA\Info(
 *     title="PAD Lazuardy API",
 *     version="1.0.0",
 *     description="API documentation for PAD Lazuardy backend."
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Local API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use Bearer token from login/register responses"
 * )
 *
 * @OA\Tag(name="Auth", description="Authentication and account flows")
 * @OA\Tag(name="Dashboards", description="Student and tutor dashboards")
 * @OA\Tag(name="Tutors", description="Tutor discovery and profile")
 * @OA\Tag(name="StudyPackages", description="Student study packages")
 * @OA\Tag(name="Notifications", description="Notifications")
 * @OA\Tag(name="Payments", description="Student payments")
 * @OA\Tag(name="Schedules", description="Schedules")
 * @OA\Tag(name="Students", description="Student profile and reviews")
 * @OA\Tag(name="Admin", description="Admin endpoints")
 * @OA\Tag(name="SocialAuth", description="Social authentication")
 *
 * @OA\Schema(
 *     schema="StandardSuccess",
 *     type="object",
 *     required={"status","message","data"},
 *     @OA\Property(property="status", type="string", example="success"),
 *     @OA\Property(property="message", type="string", example="OK"),
 *     @OA\Property(property="data", type="object")
 * )
 *
 * @OA\Schema(
 *     schema="StandardError",
 *     type="object",
 *     required={"status","message","errors"},
 *     @OA\Property(property="status", type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="Validation error"),
 *     @OA\Property(property="errors", type="object")
 * )
 *
 * @OA\Schema(
 *     schema="UserPublic",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="role", type="string", example="student"),
 *     @OA\Property(property="telephone_number", type="string", example="081234567890"),
 *     @OA\Property(property="profile_photo_url", type="string", example="uploads/photo.jpg"),
 *     @OA\Property(property="gender", type="string", example="male"),
 *     @OA\Property(property="date_of_birth", type="string", example="2005-01-15"),
 *     @OA\Property(property="religion", type="string", example="islam")
 * )
 */
class OpenApi
{
}
