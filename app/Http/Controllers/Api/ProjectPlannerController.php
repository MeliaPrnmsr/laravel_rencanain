<?php

namespace App\Http\Controllers\Api;

use App\Models\Workspace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;


class ProjectPlannerController extends Controller
{
    public function index()
    {
        $workspaces = Workspace::latest()->paginate(5);

        return response()->json([
            'success' => true,
            'message' => 'List of Workspaces',
            'data' => $workspaces
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_projek' => ['required', 'string'],
            'deskripsi' => ['required', 'string'],
            'status' => ['required', 'string'],
            'creator' => ['required', 'integer'],

        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $workspaces = Workspace::create([
            'nama_projek' =>$request->nama_projek,
            'deskripsi' =>$request->deskripsi,
            'status' =>$request->status,
            'creator' => $request->creator,

        ]);
        return response()->json([
            'success' => true,
            'message' => 'Workspace berahasil ditambahkan',
            'data' => $workspaces
        ]);
    }

    public function show($id)
    {
        $workspaces = Workspace::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Workspace',
            'data' => $workspaces
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_projek' => ['sometimes', 'string'],
            'deskripsi' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'creator' => ['sometimes', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $workspaces = Workspace::find($id);

        if (!$workspaces) {
            return response()->json([
                'success' => false,
                'message' => 'Workspace tidak ditemukan',
            ], 404);
        }

        $dataToUpdate = array_filter($request->all(), function ($value) {
            return $value !== null;
        });

        $workspaces->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Workspace berhasil diubah',
            'data' => $workspaces->refresh() 
        ]);
    }

    public function destroy($id)
    {
        $workspaces = Workspace::find($id);

        $workspaces->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workspace berhasil dihapus',
            'data' => $workspaces->refresh() 
        ]);
    }

}
