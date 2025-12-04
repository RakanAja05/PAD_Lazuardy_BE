<?php

namespace App\Http\Controllers;

use App\Enums\GenderEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\ReligionEnum;
use App\Enums\RoleEnum;
use App\Enums\ClassEnum;
use App\Enums\SubjectEnum;
use Exception;
use Illuminate\Http\Request;

class   UserController extends Controller
{
    
    public function indexFormRegister(){
        return response()->json([
            'gender' => GenderEnum::displayList(),
            'religion' => ReligionEnum::displayList(),
            'bank' => PaymentMethodEnum::displayList(),
            'class' => ClassEnum::displayList(),
            'subject' => SubjectEnum::displayList(),
        ],200);
    }

    public function viewRole()
    {
        return response()->json([
            "status" => "success",
            "Message" => "role berhasil dikirimkan",
            'role' => RoleEnum::displayList(),
        ], 200);
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'role' => [
                'required',
                'enum:' . RoleEnum::class,
            ],
        ]);

        $user = $request->user();

        try{
            $user->update(['role' => $request['role']]);

            return response()->json([
                'status' => 'succeess',
                'message' => 'Role berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role gagal diupdate'
            ], 500);
        }
    }
}
