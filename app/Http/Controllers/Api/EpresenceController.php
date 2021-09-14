<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Epresence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EpresenceController extends Controller
{
    public function insert(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'waktu' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        DB::beginTransaction();
        try {
            $user = User::where('api_token', $request->bearerToken())->first();
            $input = $request->all();
            $input['id_users'] = $user->id;
            $input['is_approve'] = false;
            if (!Epresence::create($input)) throw new \Exception('Failed Creating Data!');
            DB::commit();
            return response()->json(['message' => 'Data created successfully!'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }
}
