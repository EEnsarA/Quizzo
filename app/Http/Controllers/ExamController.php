<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        return view("pages.create_exam");
    }

    public function test()
    {
        return view("pages.test");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'elements' => 'required|array', // Canvas verisi
            'page_count' => 'required|integer'
        ]);

        $exam = ExamPaper::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'canvas_data' => $validated['elements'],
            'page_count' => $validated['page_count'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sınav kağıdı başarıyla kaydedildi!',
            'id' => $exam->id
        ]);
    }
}
