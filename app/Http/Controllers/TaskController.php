<?php

namespace App\Http\Controllers;

use App\Models\Task;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menampilkan semua task yang terkait dengan user yang sedang login
        $task = Task::where('user_id', Auth::id())->get();
        return response()->json(['data' => $task], 200);
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Menambahkan task yang terkait dengan user yang sedang login
        $tambahTugas = Task::create([
            'title' => $request->title,
            'user_id' => Auth::id(), // Menyimpan user_id dari user yang login
        ]);
        
        return response()->json(['message' => 'Tambah Tugas Berhasil', 'data' => $tambahTugas], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Pastikan task yang akan diupdate milik user yang sedang login
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update($request->all());
        return response()->json([
            'message' => 'Tugas berhasil diperbarui',
            'data' => $task
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Pastikan task yang akan dihapus milik user yang sedang login
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json([
            'message' => 'Tugas berhasil dihapus'
        ], 200);
    }
}