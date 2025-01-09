<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\models\Admin;
use App\Models\Materi;
use App\Models\Notifikasi;
use App\Models\Pelatihan;
use App\Models\Periode;
use App\Models\QuizAttempts;
use App\Models\Quizzes;
use App\Models\User;
use Carbon\Carbon;


class DashboardController extends Controller
{

    public function index()
    {
        return view('pages.dashboard-guru');
    }




    public function indexDashboardAdmin()
    {


        // dd($listPelatihan);



        // dd($data);
        return view('pages.dashboard');
    }
}
