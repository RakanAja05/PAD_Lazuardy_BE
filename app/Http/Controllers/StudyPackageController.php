<?php

namespace App\Http\Controllers;

use App\Models\StudentPackage;
use Illuminate\Http\Request;

class StudyPackageController extends Controller
{
    /**
     * Menampilkan daftar paket yang dibeli student (summary)
     */
    public function packages(Request $request)
    {
        $user = $request->user();
        
        // Pastikan user adalah student
        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya student yang dapat mengakses endpoint ini'
            ], 403);
        }

        // Ambil semua paket yang dibeli (distinct by package_id)
        $studentPackages = StudentPackage::where('student_user_id', $user->id)
            ->with([
                'package',
                'subject'
            ])
            ->get()
            ->groupBy('package_id');

        // Format response - dengan list subjects
        $data = $studentPackages->map(function ($packages, $packageId) {
            $firstPackage = $packages->first();
            $package = $firstPackage->package;
            
            $totalRemainingSession = $packages->sum('remaining_session');
            $totalSession = $package->session * $package->subject_amount;
            $totalUsedSession = $totalSession - $totalRemainingSession;

            // Hitung jumlah subject yang sudah dipilih untuk package ini
            $subjectsCount = $packages->count();

            // List nama subjects saja (tanpa detail tutor)
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
                'subjects_taken' => $subjectsCount,
                'subjects' => $subjects, // ✅ Tambahkan list subjects
                'session_per_subject' => $package->session,
                'total_session' => $totalSession,
                'total_remaining_session' => $totalRemainingSession,
                'total_used_session' => $totalUsedSession,
                'overall_progress_percentage' => round(($totalUsedSession / $totalSession) * 100, 2),
                'purchased_at' => $firstPackage->created_at->format('Y-m-d H:i:s'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar paket yang dibeli',
            'data' => $data
        ], 200);
    }

    /**
     * Menampilkan semua paket yang dibeli oleh student yang sedang login (dengan detail subjects)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Pastikan user adalah student
        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya student yang dapat mengakses endpoint ini'
            ], 403);
        }

        // Ambil semua paket yang dibeli oleh student
        $studentPackages = StudentPackage::where('student_user_id', $user->id)
            ->with([
                'package',
                'subject',
                'tutorUser' => function($query) {
                    $query->select('id', 'name', 'email', 'profile_photo_url');
                }
            ])
            ->get();

        // Group by package_id untuk mengelompokkan subject yang sama package-nya
        $groupedPackages = $studentPackages->groupBy('package_id');

        // Format response
        $data = $groupedPackages->map(function ($packages, $packageId) {
            $firstPackage = $packages->first();
            $package = $firstPackage->package;
            
            // Ambil semua subjects dari package ini
            $subjects = $packages->map(function ($sp) {
                return [
                    'id' => $sp->subject->id,
                    'name' => $sp->subject->name,
                    'tutor' => $sp->tutorUser ? [
                        'id' => $sp->tutorUser->id,
                        'name' => $sp->tutorUser->name,
                        'email' => $sp->tutorUser->email,
                        'profile_photo_url' => $sp->tutorUser->profile_photo_url,
                    ] : null,
                    'remaining_session' => $sp->remaining_session,
                    'used_session' => $package->session - $sp->remaining_session,
                    'progress_percentage' => round((($package->session - $sp->remaining_session) / $package->session) * 100, 2),
                ];
            })->values();

            // Total remaining session dari semua subject
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

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data paket belajar',
            'data' => $data
        ], 200);
    }

    /**
     * Menampilkan detail paket tertentu yang dibeli oleh student
     * Parameter id adalah package_id
     */
    public function show(Request $request, $packageId)
    {
        $user = $request->user();
        
        // Pastikan user adalah student
        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya student yang dapat mengakses endpoint ini'
            ], 403);
        }

        // Ambil semua student_packages dengan package_id tertentu milik student ini
        $studentPackages = StudentPackage::where('student_user_id', $user->id)
            ->where('package_id', $packageId)
            ->with([
                'package',
                'subject',
                'tutorUser' => function($query) {
                    $query->select('id', 'name', 'email', 'profile_photo_url');
                }
            ])
            ->get();

        if ($studentPackages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan'
            ], 404);
        }

        $firstPackage = $studentPackages->first();
        $package = $firstPackage->package;
        
        // Ambil semua subjects dari package ini
        $subjects = $studentPackages->map(function ($sp) use ($package) {
            return [
                'id' => $sp->subject->id,
                'name' => $sp->subject->name,
                'tutor' => $sp->tutorUser ? [
                    'id' => $sp->tutorUser->id,
                    'name' => $sp->tutorUser->name,
                    'email' => $sp->tutorUser->email,
                    'profile_photo_url' => $sp->tutorUser->profile_photo_url,
                ] : null,
                'remaining_session' => $sp->remaining_session,
                'used_session' => $package->session - $sp->remaining_session,
                'progress_percentage' => round((($package->session - $sp->remaining_session) / $package->session) * 100, 2),
            ];
        });

        // Total remaining session dari semua subject
        $totalRemainingSession = $studentPackages->sum('remaining_session');
        $totalSession = $package->session * $package->subject_amount;
        $totalUsedSession = $totalSession - $totalRemainingSession;

        // Format response
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

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil detail paket belajar',
            'data' => $data
        ], 200);
    }
}
