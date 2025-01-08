@extends('layouts.app')

@section('title', 'Manajemen Soal')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Soal untuk Quiz {{ $quizId }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Quiz</a></div>
                    <div class="breadcrumb-item"><a href="#">Soal</a></div>
                </div>
            </div>

            <div class="section-body">
                <a href="{{ route('admin.soal.create', $quizId) }}" class="btn btn-success mb-3">+ Tambah Soal</a>
                <a href="{{ url('/admin/quiz') }}" class="btn btn-danger mb-3">
                    < Kembali</a>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table-striped table-md table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Pertanyaan</th>
                                                <th>Pilihan</th>
                                                <th>Jawaban</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($soal as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item['pertanyaan'] }}</td>
                                                    <td>
                                                        @foreach ($item['pilihan'] as $index => $pilihan)
                                                            <div>{{ chr(65 + $index) . '. ' . $pilihan }}</div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @php
                                                            $correctAnswer = chr(65 + $item['jawaban']); // Menentukan huruf abjad untuk kunci jawaban
                                                        @endphp
                                                        {{ $correctAnswer . '. ' . $item['pilihan'][$item['jawaban']] }}
                                                    </td>
                                                    <td>
                                                        <!-- Aksi Edit dan Hapus soal -->
                                                        <a href="{{ route('admin.soal.edit', ['quizId' => $quizId, 'questionId' => $index]) }}"
                                                            class="btn btn-warning">Edit</a>
                                                        <form
                                                            action="{{ route('admin.soal.destroy', ['quizId' => $quizId, 'questionId' => $index]) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Hapus soal ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger" type="submit">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada soal untuk quiz ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
            </div>
        </section>
    </div>
@endsection
