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
     * Dapat difilter berdasarkan list_id jika diberikan.
     */
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $query = Task::where('user_id', $user_id);

        // Jika request memiliki list_id, tambahkan filter
        if ($request->has('list_id')) {
            $query->where('list_id', $request->list_id);
        }

        $tasks = $query->get();

        return response()->json(['data' => $tasks], 200);
    }

    /**
     * Menyimpan task baru dengan list_id.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'list_id' => 'required|integer', // Pastikan list_id ada
            'deadline' => 'nullable|date', // Validasi format tanggal
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user_id = Auth::id();

        // Simpan task ke database
        $task = Task::create([
            'title' => $request->title,
            'urgent_level' => $request->urgent_level ?? null,
            'user_id' => $user_id,
            'list_id' => $request->list_id,
            'deadline' => $request->deadline,
        ]);

        return response()->json(['message' => 'Tambah Tugas Berhasil', 'data' => $task], 201);
    }

    /**
     * Menampilkan detail task tertentu berdasarkan list_id.
     */
    public function show(Task $task)
    {
        // Pastikan task milik user yang sedang login
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
        // Pastikan hanya pemilik task yang bisa mengupdate
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'list_id' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->update($request->only(['title', 'urgent_level', 'list_id']));

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
