<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Kreait\Firebase\Exception\DatabaseException;

class QuizController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    // Method Index - Menampilkan daftar quiz
    public function index()
    {
        $quizzes = $this->firebaseService->getReference('nama_quiz')->getValue();

        $quizzes = $quizzes ? array_values($quizzes) : []; // Konversi object ke array

        return view('pages.quiz', compact('quizzes'));
    }

    // Method Create - Menampilkan form create quiz
    public function create()
    {
        return view('pages.quiz-create');
    }

    // Method Store - Menyimpan data quiz ke Firebase
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|max:255',
            'quiz_name' => 'required|max:255',
        ]);

        try {
            // Simpan data ke tabel nama_quiz
            $existingQuizzes = $this->firebaseService->getReference('nama_quiz')->getValue();
            $newId = $existingQuizzes ? count($existingQuizzes) : 0;

            $quizData = [
                'quiz_id' => $request->input('quiz_id'),
                'quiz_name' => $request->input('quiz_name'),
            ];

            $this->firebaseService->getReference("nama_quiz/{$newId}")->set($quizData);

            // Buat struktur awal soal
            $quizId = $request->input('quiz_id');
            $dataSoal = [
                'data' => [
                    [
                        'pertanyaan' => "Contoh Pertanyaan?",
                        'jawaban' => 1, // Indeks jawaban yang benar (dimulai dari 0)
                        'pilihan' => [
                            "Jawaban A",
                            "Jawaban B",
                            "Jawaban C",
                            "Jawaban D"
                        ]
                    ]
                ]
            ];

            // Cek apakah path soal/{quiz_id} sudah ada
            $existingSoal = $this->firebaseService->getReference("soal/{$quizId}")->getValue();

            if (!$existingSoal) {
                // Jika belum ada, set path soal/{quiz_id}
                $this->firebaseService->getReference("soal/{$quizId}")->set($dataSoal);
            }

            return redirect()->route('admin.quiz.index')->with('success', 'Quiz berhasil ditambahkan dengan struktur soal kosong!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }







    // Method Edit - Menampilkan form edit quiz
    // public function edit($id)
    // {
    //     $quiz = $this->firebaseService->getReference('nama_quiz/' . $id)->getValue();

    //     if (!$quiz) {
    //         return redirect()->route('admin.quiz.index')->with('error', 'Quiz tidak ditemukan.');
    //     }

    //     return view('pages.quiz-edit', compact('quiz', 'id'));
    // }

    public function edit($id)
    {
        // dd($id);
        // Ambil semua data quiz dari Firebase
        $quizzes = $this->firebaseService->getReference('nama_quiz')->getValue();

        // Cari quiz berdasarkan quiz_id
        $quiz = collect($quizzes)->firstWhere('quiz_id', $id);
        // dd($quiz);
        if (!$quiz) {
            return redirect()->route('admin.quiz.index')->with('error', 'Quiz tidak ditemukan.');
        }

        return view('pages.quiz-edit', compact('quiz', 'id'));
    }

    // Method Update - Memperbarui data quiz di Firebase
    public function update(Request $request, $id)
    {
        $request->validate([
            'quiz_id' => 'required|max:255',
            'quiz_name' => 'required|max:255',
        ]);

        try {
            // Ambil seluruh data quiz dari Firebase
            $quizzes = $this->firebaseService->getReference('nama_quiz')->getValue();

            // Cari quiz berdasarkan quiz_id yang ada di request
            $quizToUpdate = null;
            foreach ($quizzes as $key => $quiz) {
                if ($quiz['quiz_id'] == $id) {
                    $quizToUpdate = $key; // Menyimpan indeks key untuk update
                    break;
                }
            }

            // Jika quiz tidak ditemukan
            if ($quizToUpdate === null) {
                return redirect()->route('admin.quiz.index')->with('error', 'Quiz tidak ditemukan.');
            }

            // Data yang akan di-update
            $quizData = [
                'quiz_id' => $request->input('quiz_id'),
                'quiz_name' => $request->input('quiz_name'),
            ];

            // Update data pada Firebase berdasarkan key yang ditemukan
            $this->firebaseService->getReference('nama_quiz/' . $quizToUpdate)->update($quizData);

            // Jika quiz_id berubah, update juga di path soal/{quiz_id}
            $oldQuizId = $id;
            $newQuizId = $request->input('quiz_id');

            if ($oldQuizId !== $newQuizId) {
                // Ambil data soal lama
                $oldSoalData = $this->firebaseService->getReference("soal/{$oldQuizId}")->getValue();

                // Jika ada soal yang terkait dengan quiz_id lama
                if ($oldSoalData) {
                    // Pindahkan data soal ke quiz_id yang baru
                    $this->firebaseService->getReference("soal/{$newQuizId}")->set($oldSoalData);
                    // Hapus data soal lama
                    $this->firebaseService->getReference("soal/{$oldQuizId}")->remove();
                }
            }

            return redirect()->route('admin.quiz.index')->with('success', 'Quiz berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }



    // Method Destroy - Menghapus data quiz dari Firebase
    public function destroy($id)
    {
        try {
            // Ambil seluruh data quiz dari Firebase
            $quizzes = $this->firebaseService->getReference('nama_quiz')->getValue();

            // Cari quiz berdasarkan quiz_id
            $quizToDelete = null;
            foreach ($quizzes as $key => $quiz) {
                if ($quiz['quiz_id'] == $id) {
                    $quizToDelete = $key; // Menyimpan indeks key untuk dihapus
                    break;
                }
            }

            // Jika quiz tidak ditemukan
            if ($quizToDelete === null) {
                return redirect()->route('admin.quiz.index')->with('error', 'Quiz tidak ditemukan.');
            }

            // Hapus data soal yang terkait dengan quiz_id ini
            $this->firebaseService->getReference("soal/{$id}")->remove();

            // Hapus data quiz dari Firebase berdasarkan key yang ditemukan
            $this->firebaseService->getReference('nama_quiz/' . $quizToDelete)->remove();

            return redirect()->route('admin.quiz.index')->with('success', 'Quiz beserta soal berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }



    // Menampilkan daftar nama quiz
    // public function indexNamaQuiz()
    // {
    //     // Mengambil semua quiz dari Firebase
    //     $namaQuiz = $this->firebaseService->getReference('nama_quiz')->getValue();

    //     return view('pages.quiz-manage', compact('namaQuiz'));
    // }

    // Menampilkan soal dan jawaban berdasarkan quizId
    public function indexSoal($quizId)
    {
        // dd($quizId);
        // Mengambil soal dari quiz berdasarkan quizId
        $soal = $this->firebaseService->getReference('soal/' . $quizId . '/data')->getValue();
        // dd($soal);

        // Cek jika data soal ada atau tidak
        if ($soal === null) {
            dd('Data soal tidak ditemukan untuk quiz ID: ' . $quizId);
        }

        // Pastikan data soal tidak null, jika null, buat array kosong
        $soal = $soal ?? [];

        return view('pages.soal-manage', compact('soal', 'quizId'));
    }

    public function createQuestion($quizId)
    {
        $quizId = $quizId;

        return view('pages.add-question', compact('quizId'));
    }



    public function storeQuestion(Request $request, $quizId)
    {
        // Validasi request
        $validated = $request->validate([
            'pertanyaan' => 'string',
            'pilihan' => 'array|min:4|max:4',
            'jawaban' => 'integer|min:0|max:3',
        ]);

        // dd($quizId);

        // Data soal baru
        $soal = [
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan' => $validated['pilihan'],
            'jawaban' => $validated['jawaban'],
        ];

        // Instansiasi FirebaseService
        // $firebaseService = new FirebaseService();

        // Mendapatkan referensi database ke lokasi soal
        $quizRef = $this->firebaseService->getReference("soal/{$quizId}");
        // $soal = $this->firebaseService->getReference('soal/' . $quizId . '/data')->getValue();

        // Ambil data soal dari Firebase
        $quizData = $quizRef->getValue();

        // Cek jika data soal sudah ada
        if ($quizData === null) {
            $quizData = ['data' => [$soal]]; // Jika belum ada data soal, buat data baru
        } else {
            $quizData['data'][] = $soal; // Tambahkan soal baru ke array data
        }

        // Simpan data ke Firebase
        $isSaved = $quizRef->set($quizData);

        // Periksa apakah data berhasil disimpan
        if ($isSaved !== null) {
            // Redirect jika berhasil
            return redirect()->route('admin.quiz.indexSoal', $quizId)
                ->with('success', 'Soal berhasil ditambahkan.');
        }

        // Redirect jika gagal menyimpan
        return redirect()->route('admin.quiz.indexSoal', $quizId)
            ->with('error', 'Gagal menambahkan soal. Silakan coba lagi.');
    }

    public function editQuestion($quizId, $questionId)
    {
        // Ambil data soal dari Firebase
        $firebaseService = new FirebaseService();
        $questionRef = $firebaseService->getReference("soal/{$quizId}/data/{$questionId}");
        $questionData = $questionRef->getValue();

        if (!$questionData) {
            return redirect()->route('admin.quiz.indexSoal', $quizId)
                ->with('error', 'Soal tidak ditemukan.');
        }

        // Tampilkan view edit dengan data soal
        return view('pages.edit-question', compact('quizId', 'questionId', 'questionData'));
    }

    public function updateQuestion(Request $request, $quizId, $questionId)
    {
        // Validasi request
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'pilihan' => 'required|array|min:4|max:4',
            'jawaban' => 'required|integer|min:0|max:3',
        ]);

        // Data soal baru
        $soal = [
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan' => $validated['pilihan'],
            'jawaban' => $validated['jawaban'],
        ];

        // Simpan data ke Firebase
        $firebaseService = new FirebaseService();
        $questionRef = $firebaseService->getReference("soal/{$quizId}/data/{$questionId}");
        $isUpdated = $questionRef->set($soal);

        // Periksa apakah data berhasil diperbarui
        if ($isUpdated !== null) {
            return redirect()->route('admin.quiz.indexSoal', $quizId)
                ->with('success', 'Soal berhasil diperbarui.');
        }

        return redirect()->route('admin.quiz.indexSoal', $quizId)
            ->with('error', 'Gagal memperbarui soal. Silakan coba lagi.');
    }

    public function deleteQuestion($quizId, $questionId)
    {
        try {
            // Instansiasi FirebaseService
            $firebaseService = new FirebaseService();

            // Mendapatkan referensi soal pada quiz tertentu
            $quizRef = $firebaseService->getReference("soal/{$quizId}");

            // Ambil data soal
            $quizData = $quizRef->getValue();

            if ($quizData && isset($quizData['data'][$questionId])) {
                // Menghapus soal dari data soal
                unset($quizData['data'][$questionId]);

                // Re-indexing array untuk menghindari index yang hilang
                $quizData['data'] = array_values($quizData['data']);

                // Menyimpan kembali data yang sudah dihapus ke Firebase
                $quizRef->set($quizData);

                return redirect()->route('admin.quiz.indexSoal', $quizId)
                    ->with('success', 'Soal berhasil dihapus.');
            } else {
                return redirect()->route('admin.quiz.indexSoal', $quizId)
                    ->with('error', 'Soal tidak ditemukan.');
            }
        } catch (DatabaseException $e) {
            \Log::error('Error deleting question: ' . $e->getMessage());
            return redirect()->route('admin.quiz.indexSoal', $quizId)
                ->with('error', 'Gagal menghapus soal. Silakan coba lagi.');
        }
    }

    // Menghapus quiz beserta soal dan jawabannya

}
