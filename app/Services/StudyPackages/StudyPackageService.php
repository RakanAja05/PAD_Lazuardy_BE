<?php

namespace App\Services\StudyPackages;

use App\DTOs\ResponseDTO;
use App\Enums\RoleEnum;
use App\Models\StudentPackage;
use Illuminate\Http\Request;

class StudyPackageService
{
    public function packages(Request $request): ResponseDTO
    {
        $user = $request->user();

        if ($user->role !== RoleEnum::STUDENT) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Hanya student yang dapat mengakses endpoint ini',
                'errors' => [
                    'role' => 'student_required',
                ],
            ], 403);
        }

        $studentPackages = StudentPackage::where('student_user_id', $user->id)
            ->with(['package', 'subject'])
            ->get()
            ->groupBy('package_id');

        $data = $studentPackages->map(function ($packages, $packageId) {
            $firstPackage = $packages->first();
            $package = $firstPackage->package;

            $totalRemainingSession = $packages->sum('remaining_session');
            $totalSession = $package->session * $package->subject_amount;
            $totalUsedSession = $totalSession - $totalRemainingSession;

            $subjects = $packages->map(function ($sp) {
                return [
                    'id' => $sp->subject->id,
                    'name' => $sp->subject->name,
                ];
            })->values();

            return [
                'package_id' => $package->id,
                'package_name' => $package->name,
                'price' => $package->price,
                'discount' => $package->discount,
                'image_url' => $package->image_url,
                'subject_amount' => $package->subject_amount,
                'subjects_taken' => $packages->count(),
                'subjects' => $subjects,
                'session_per_subject' => $package->session,
                'total_session' => $totalSession,
                'total_remaining_session' => $totalRemainingSession,
                'total_used_session' => $totalUsedSession,
                'overall_progress_percentage' => round(($totalUsedSession / $totalSession) * 100, 2),
                'purchased_at' => $firstPackage->created_at->format('Y-m-d H:i:s'),
            ];
        })->values();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil daftar paket yang dibeli',
            'data' => $data,
        ], 200);
    }

    public function index(Request $request): ResponseDTO
    {
        $user = $request->user();

        if ($user->role !== RoleEnum::STUDENT) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Hanya student yang dapat mengakses endpoint ini',
                'errors' => [
                    'role' => 'student_required',
                ],
            ], 403);
        }

        $studentPackages = StudentPackage::where('student_user_id', $user->id)
            ->with([
                'package',
                'subject',
                'tutor:id,name,email,profile_photo_url',
            ])
            ->get();

        $groupedPackages = $studentPackages->groupBy('package_id');

        $data = $groupedPackages->map(function ($packages, $packageId) {
            $firstPackage = $packages->first();
            $package = $firstPackage->package;

            $subjects = $packages->map(function ($sp) use ($package) {
                return [
                    'id' => $sp->subject->id,
                    'name' => $sp->subject->name,
                    'tutor' => $sp->tutor ? [
                        'id' => $sp->tutor->id,
                        'name' => $sp->tutor->name,
                        'email' => $sp->tutor->email,
                        'profile_photo_url' => $sp->tutor->profile_photo_url,
                    ] : null,
                    'remaining_session' => $sp->remaining_session,
                    'used_session' => $package->session - $sp->remaining_session,
                    'progress_percentage' => round((($package->session - $sp->remaining_session) / $package->session) * 100, 2),
                ];
            })->values();

            $totalRemainingSession = $packages->sum('remaining_session');
            $totalSession = $package->session * $package->subject_amount;
            $totalUsedSession = $totalSession - $totalRemainingSession;

            return [
                'package_id' => $package->id,
                'package_name' => $package->name,
                'price' => $package->price,
                'discount' => $package->discount,
                'description' => $package->description,
                'benefit' => $package->benefit,
                'image_url' => $package->image_url,
                'subject_amount' => $package->subject_amount,
                'session_per_subject' => $package->session,
                'total_session' => $totalSession,
                'total_remaining_session' => $totalRemainingSession,
                'total_used_session' => $totalUsedSession,
                'overall_progress_percentage' => round(($totalUsedSession / $totalSession) * 100, 2),
                'subjects' => $subjects,
                'purchased_at' => $firstPackage->created_at->format('Y-m-d H:i:s'),
            ];
        })->values();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil data paket belajar',
            'data' => $data,
        ], 200);
    }

    public function show(Request $request, $packageId): ResponseDTO
    {
        $user = $request->user();

        if ($user->role !== RoleEnum::STUDENT) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Hanya student yang dapat mengakses endpoint ini',
                'errors' => [
                    'role' => 'student_required',
                ],
            ], 403);
        }

        $studentPackages = StudentPackage::where('student_user_id', $user->id)
            ->where('package_id', $packageId)
            ->with([
                'package',
                'subject',
                'tutor:id,name,email,profile_photo_url',
            ])
            ->get();

        if ($studentPackages->isEmpty()) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Paket tidak ditemukan',
                'errors' => [
                    'package_id' => $packageId,
                ],
            ], 404);
        }

        $firstPackage = $studentPackages->first();
        $package = $firstPackage->package;

        $subjects = $studentPackages->map(function ($sp) use ($package) {
            return [
                'id' => $sp->subject->id,
                'name' => $sp->subject->name,
                'tutor' => $sp->tutor ? [
                    'id' => $sp->tutor->id,
                    'name' => $sp->tutor->name,
                    'email' => $sp->tutor->email,
                    'profile_photo_url' => $sp->tutor->profile_photo_url,
                ] : null,
                'remaining_session' => $sp->remaining_session,
                'used_session' => $package->session - $sp->remaining_session,
                'progress_percentage' => round((($package->session - $sp->remaining_session) / $package->session) * 100, 2),
            ];
        })->values();

        $totalRemainingSession = $studentPackages->sum('remaining_session');
        $totalSession = $package->session * $package->subject_amount;
        $totalUsedSession = $totalSession - $totalRemainingSession;

        $data = [
            'package_id' => $package->id,
            'package_name' => $package->name,
            'price' => $package->price,
            'discount' => $package->discount,
            'description' => $package->description,
            'benefit' => $package->benefit,
            'image_url' => $package->image_url,
            'subject_amount' => $package->subject_amount,
            'session_per_subject' => $package->session,
            'total_session' => $totalSession,
            'total_remaining_session' => $totalRemainingSession,
            'total_used_session' => $totalUsedSession,
            'overall_progress_percentage' => round(($totalUsedSession / $totalSession) * 100, 2),
            'subjects' => $subjects,
            'purchased_at' => $firstPackage->created_at->format('Y-m-d H:i:s'),
        ];

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail paket belajar',
            'data' => $data,
        ], 200);
    }
}
