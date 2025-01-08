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
        $quizzes = $this->firebaseService->getReference('nama_quiz');

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
            $existingQuizzes = $this->firebaseService->getReference('nama_quiz');
            $newId = $existingQuizzes ? count($existingQuizzes) : 0;

            $quizData = [
                'quiz_id' => $request->input('quiz_id'),
                'quiz_name' => $request->input('quiz_name'),
            ];

            $this->firebaseService->setReference("nama_quiz/{$newId}", $quizData);

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
            $existingSoal = $this->firebaseService->getReference("soal/{$quizId}");

            if (!$existingSoal) {
                // Jika belum ada, set path soal/{quiz_id}
                $this->firebaseService->setReference("soal/{$quizId}", $dataSoal);
            }

            return redirect()->route('admin.quiz.index')->with('success', 'Quiz berhasil ditambahkan dengan struktur soal kosong!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method Edit - Menampilkan form edit quiz
    public function edit($id)
    {
        $quizzes = $this->firebaseService->getReference('nama_quiz');

        // Cari quiz berdasarkan quiz_id
        $quiz = collect($quizzes)->firstWhere('quiz_id', $id);

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
            $quizzes = $this->firebaseService->getReference('nama_quiz');

            $quizToUpdate = null;
            foreach ($quizzes as $key => $quiz) {
                if ($quiz['quiz_id'] == $id) {
                    $quizToUpdate = $key;
                    break;
                }
            }

            if ($quizToUpdate === null) {
                return redirect()->route('admin.quiz.index')->with('error', 'Quiz tidak ditemukan.');
            }

            $quizData = [
                'quiz_id' => $request->input('quiz_id'),
                'quiz_name' => $request->input('quiz_name'),
            ];

            $this->firebaseService->setReference('nama_quiz/' . $quizToUpdate, $quizData);

            // Jika quiz_id berubah, update juga di path soal/{quiz_id}
            $oldQuizId = $id;
            $newQuizId = $request->input('quiz_id');

            if ($oldQuizId !== $newQuizId) {
                $oldSoalData = $this->firebaseService->getReference("soal/{$oldQuizId}");

                if ($oldSoalData) {
                    $this->firebaseService->setReference("soal/{$newQuizId}", $oldSoalData);
                    $this->firebaseService->setReference("soal/{$oldQuizId}", null);
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
            $quizzes = $this->firebaseService->getReference('nama_quiz');

            $quizToDelete = null;
            foreach ($quizzes as $key => $quiz) {
                if ($quiz['quiz_id'] == $id) {
                    $quizToDelete = $key;
                    break;
                }
            }

            if ($quizToDelete === null) {
                return redirect()->route('admin.quiz.index')->with('error', 'Quiz tidak ditemukan.');
            }

            $this->firebaseService->setReference("soal/{$id}", null);
            $this->firebaseService->setReference('nama_quiz/' . $quizToDelete, null);

            return redirect()->route('admin.quiz.index')->with('success', 'Quiz beserta soal berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexSoal($quizId)
    {
        $soal = $this->firebaseService->getReference("soal/{$quizId}/data");

        if ($soal === null) {
            dd('Data soal tidak ditemukan untuk quiz ID: ' . $quizId);
        }

        $soal = $soal ?? [];

        return view('pages.soal-manage', compact('soal', 'quizId'));
    }

    public function storeQuestion(Request $request, $quizId)
    {
        $validated = $request->validate([
            'pertanyaan' => 'string',
            'pilihan' => 'array|min:4|max:4',
            'jawaban' => 'integer|min:0|max:3',
        ]);

        $soal = [
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan' => $validated['pilihan'],
            'jawaban' => $validated['jawaban'],
        ];

        $quizRef = $this->firebaseService->getReference("soal/{$quizId}");

        $quizData = $quizRef ?? ['data' => [$soal]];

        $quizData['data'][] = $soal;

        $isSaved = $quizRef->set($quizData);

        if ($isSaved !== null) {
            return redirect()->route('admin.quiz.indexSoal', $quizId)
                ->with('success', 'Soal berhasil ditambahkan.');
        }

        return redirect()->route('admin.quiz.indexSoal', $quizId)
            ->with('error', 'Gagal menambahkan soal. Silakan coba lagi.');
    }

    public function updateQuestion(Request $request, $quizId, $questionId)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'pilihan' => 'required|array|min:4|max:4',
            'jawaban' => 'required|integer|min:0|max:3',
        ]);

        $soal = [
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan' => $validated['pilihan'],
            'jawaban' => $validated['jawaban'],
        ];

        $questionRef = $this->firebaseService->getReference("soal/{$quizId}/data/{$questionId}");

        $isUpdated = $questionRef->set($soal);

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
            $quizRef = $this->firebaseService->getReference("soal/{$quizId}");

            $quizData = $quizRef->getValue();

            if ($quizData && isset($quizData['data'][$questionId])) {
                unset($quizData['data'][$questionId]);
                $quizData['data'] = array_values($quizData['data']);
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
}
