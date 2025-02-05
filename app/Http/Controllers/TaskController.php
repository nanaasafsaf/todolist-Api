<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Menampilkan semua task yang terkait dengan user yang sedang login.
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->get();
        return response()->json(['data' => $tasks, 'user' => Auth::user()], 200);
    }

    /**
     * Menyimpan task baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
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
        $task = Task::create([
            'title' => $request->title,
            'urgent_level' => $request->urgent_level,
            'user_id' => $user_id,
        ]);

        return response()->json(['message' => 'Tambah Tugas Berhasil', 'data' => $task], 201);
    }

    /**
     * Menampilkan detail task tertentu.
     */
    public function show(Task $task)
    {
        // Pastikan task yang akan diakses adalah milik user yang sedang login
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $task], 200);
    }

    /**
     * Mengupdate task tertentu.
     */
    public function update(Request $request, Task $task)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Pastikan hanya pemilik task yang bisa mengupdate
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update($request->all());

        return response()->json(['message' => 'Tugas berhasil diperbarui', 'data' => $task], 200);
    }

    /**
     * Menghapus task tertentu.
     */
    public function destroy(Task $task)
    {
        // Pastikan hanya pemilik task yang bisa menghapus
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus'], 200);
    }
}
