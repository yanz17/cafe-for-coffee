<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback; // Pastikan Model Feedback di-import

class FeedbackController extends Controller // Sesuaikan nama Controller Anda
{
    // ... (metode lain di Controller)

    public function indexFeedbacks()
    {
        // Ambil semua feedback.
        // Eager load relasi 'order' dan 'user' (pelanggan yang memberi feedback)
        $feedbacks = Feedback::orderBy('created_at', 'desc')
                             ->with(['order', 'user']) 
                             ->paginate(10); // Gunakan paginate agar halaman tidak terlalu berat

        return view('manager.feedbacks.index', compact('feedbacks'));
    }
}