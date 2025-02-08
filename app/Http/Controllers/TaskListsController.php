<?php

namespace App\Http\Controllers;

use App\Models\TaskLists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskListsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = TaskLists::where('user_id', Auth::id())->get();
        return response()->json(['data' => $lists, 'user' => Auth::user()], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil ID user yang sedang login
        $user_id = Auth::id();

        if (!$user_id) {
            return response()->json(['error' => 'User tidak terautentikasi'], 401);
        }

        // Simpan task ke database
        $list = TaskLists::create([
            'name' => $request->name,
            'user_id' => $user_id,
        ]);

        return response()->json(['message' => 'Tambah Tugas Berhasil', 'data' => $list], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskLists $taskLists)
    {
          // Pastikan task yang akan diakses adalah milik user yang sedang login
        if ($taskLists->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $taskLists], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskLists $taskLists)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskLists $list)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Pastikan hanya pemilik task yang bisa mengupdate
        if ($list->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $list->update($request->all());

        return response()->json(['message' => 'List berhasil diperbarui', 'data' => $list], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskLists $list)
    {
        // Pastikan hanya pemilik task yang bisa menghapus
        if ($list->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $list->delete();

        return response()->json(['message' => 'List berhasil dihapus'], 200);
    }
}
