<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRegisterRequest;
use App\Http\Requests\StoreTutorRegisterRequest;
use App\Services\ScheduleService;
use App\Services\TutorService;
use App\Services\UserService;
use Illuminate\Support\Arr;

class   UserController extends Controller
{
    /**
     * Update the specified resource in storage.
     * @OA\Patch(
     * path="api/register/student/{user}",
     * operationId="regStudentRole",
     * tags={"students", "Users"},
     * summary="Biodata saat registrasi student",
     * description="Endpoint untuk mengisi biodata saat registrasi student",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "gender", "date_of_birth", "telephone_number", "province", "regency", "district", "subdistrict", "street", "parent", "parent_telephone_number", "class_id"},
     * @OA\Property(property="name", type="string", example="Jane Doe", description="Nama lengkap student"),
     * @OA\Property(property="gender", type="string", example="wanita", description="Jenis kelamin student"),
     * @OA\Property(property="date_of_birth", type="string", format="date", example="2005-05-15", description="Tanggal lahir student"),
     * @OA\Property(property="telephone_number", type="string", example="089876543210", description="Nomor telepon student"),
     * @OA\Property(property="province", type="string", example="Jawa Barat", description="Provinsi tempat tinggal student"),
     * @OA\Property(property="regency", type="string", example="Bandung", description="Kota/Kabupaten tempat tinggal student"),
     * @OA\Property(property="district", type="string", example="Coblong", description="Kecamatan tempat tinggal student"),
     * @OA\Property(property="subdistrict", type="string", example="Dago", description="Kelurahan tempat tinggal student"),
     * @OA\Property(property="street", type="string", example="Jl. Dago No. 456", description="Alamat lengkap student"),
     * @OA\Property(property="profile_photo_url", type="string", example="http://example.com/photo.jpg", description="URL foto profil student"),
     * @OA\Property(property="latitude", type="string", example="-6.90389", description="Latitude lokasi student"),
     * @OA\Property(property="longitude", type="string", example="107.61861", description="Longitude lokasi student"),
     * @OA\Property(property="parent", type="string", example="Mr. Doe", description="Nama orang tua/wali student"),
     * @OA\Property(property="parent_telephone_number", type="string", example="081234567899", description="Nomor telepon orang tua/wali student"),
     * @OA\Property(property="class_id", type="integer", example=10, description="ID kelas student"),
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Data berhasil diubah"
     * )
     * )
     */
    public function updateStudentRole(StoreStudentRegisterRequest $request, UserService $userService)
    {
        $user = $request->user();
        
        $validatedData = $request->validated();
        
        $userData = collect(Arr::only($validatedData, ['name', 'gender', 'date_of_birth', 'telephone_number', 'profile_photo_url', 'latitude', 'longitude']));
        $addressData = collect(Arr::only($validatedData, ['province', 'regency', 'district', 'subdistrict', 'street']));
        $studentData = collect(Arr::only($validatedData, ['parent', 'parent_telephone_number', 'class_id', 'curriculum_id']));

        $userService->updateBiodataRegistration($user, $userData, $addressData);
        $userService->storeStudentRole($user, $studentData);

        return response()->json([
            'message' => 'Berhasil'
        ], 200);;
    }
    
    /**
     * Update the specified resource in storage.
     * @OA\Patch(
     * path="api/register/tutor/{user}",
     * operationId="regTutorRole",
     * tags={"tutors", "Users"},
     * summary="Biodata saat registrasi tutor",
     * description="Endpoint untuk mengisi biodata saat registrasi tutor",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "gender", "date_of_birth", "telephone_number", "province", "regency", "district", "subdistrict", "street", "experience", "organization", "course_location", "description", "qualification", "learning_method", "subject_ids", "schedules"},
     * @OA\Property(property="name", type="string", example="John Doe", description="Nama lengkap tutor"),
     * @OA\Property(property="gender", type="string", example="pria", description="Jenis kelamin tutor"),
     * @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01", description="Tanggal lahir tutor"),
     * @OA\Property(property="telephone_number", type="string", example="081234567890", description="Nomor telepon tutor"),
     * @OA\Property(property="province", type="string", example="Jawa Barat", description="Provinsi tempat tinggal tutor"),
     * @OA\Property(property="regency", type="string", example="Bandung", description="Kota/Kabupaten tempat tinggal tutor"),
     * @OA\Property(property="district", type="string", example="Coblong", description="Kecamatan tempat tinggal tutor"),
     * @OA\Property(property="subdistrict", type="string", example="Dago", description="Kelurahan tempat tinggal tutor"),
     * @OA\Property(property="street", type="string", example="Jl. Dago No. 123", description="Alamat lengkap tutor"),
     * @OA\Property(property="profile_photo_url", type="string", example="http://example.com/photo.jpg", description="URL foto profil tutor"),
     * @OA\Property(property="latitude", type="string", example="-6.90389", description="Latitude lokasi tutor"),
     * @OA\Property(property="longitude", type="string", example="107.61861", description="Longitude lokasi tutor"),
     * @OA\Property(property="experience", type="string", example="3 tahun mengajar matematika", description="Pengalaman mengajar tutor"),
     * @OA\Property(property="organization", type="string", example="Bimbel XYZ", description="Organisasi tempat tutor mengajar"),
     * @OA\Property(property="course_location", type="string", example="Rumah tutor", description="Lokasi kursus tutor"),
     * @OA\Property(property="description", type="string", example="Saya seorang tutor yang berpengalaman...", description="Deskripsi tentang tutor"),
     * @OA\Property(property="qualification", type="string", example="S1 Pendidikan Matematika", description="Kualifikasi akademik tutor"),
     * @OA\Property(property="learning_method", type="string", example="Online dan Offline", description="Metode pembelajaran tutor"),
     * @OA\Property(property="subject_ids", type="array", @OA\Items(type="integer"), example={1,2,3}, description="Daftar ID mata pelajaran yang diajarkan tutor"),
     * @OA\Property(property="schedules", type="array", @OA\Items(
     *     type="object",
     *     @OA\Property(property="day", type="string", example="Monday", description="Hari tersedia"),
     *     @OA\Property(property="time", type="string", example="18:00-20:00", description="Waktu tersedia"),
     * ), example={{"day":"Monday","time":"18:00-20:00"},{"day":"Wednesday","time":"18:00-20:00"}}, description="Jadwal ketersediaan tutor"),
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Data berhasil diubah"
     * )
     * )
    */
    public function updateTutorRole(StoreTutorRegisterRequest $request, UserService $userService, TutorService $tutorService, ScheduleService $scheduleService)
    {
        $user = $request->user();

        $validatedData = $request->validated();

        $userData = collect(Arr::only($validatedData, ['name', 'gender', 'date_of_birth', 'telephone_number', 'profile_photo_url', 'latitude', 'longitude']));
        $addressData = collect(Arr::only($validatedData, ['province', 'regency', 'district', 'subdistrict', 'street']));
        $tutorData = collect(Arr::only($validatedData, ['experience', 'organization','course_mode', 'description', 'qualification', 'learning_method'],));
        $subjectData = collect(Arr::only($validatedData, ['subject_ids']));
        $fileData = collect(Arr::only($validatedData, ['cv', 'ktp','ijazah','certificate', 'portofolio']));
        $scheduleDatas = collect(Arr::only($validatedData, ['schedules']));
        
        $userService->updateBiodataRegistration($user, $userData, $addressData);
        $userService->storeTutorRole($user, $tutorData);
        $tutorService->storeSubject($user, $subjectData);
        $tutorService->storeFile($user, $fileData);
        $scheduleService->storeScheduleTutor($user, $scheduleDatas);

        
        return response()->json([
            'message' => 'Berhasil'
        ], 200);;
    }
}
