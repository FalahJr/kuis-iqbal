@extends('layouts.app')

@section('title', 'Management Quiz')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Management Quiz</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Quiz</a></div>
                </div>
            </div>

            <div class="section-body">
                <a href="{{ route('admin.quiz.create') }}" class="btn btn-success mb-3">+ Tambah Quiz</a>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table-striped table-md table">
                                <tr>
                                    <th>#</th>
                                    <th>Quiz ID</th>
                                    <th>Nama Quiz</th>
                                    <th>Action</th>
                                </tr>
                                @foreach ($quizzes as $index => $quiz)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $quiz['quiz_id'] }}</td>
                                        <td>{{ $quiz['quiz_name'] }}</td>
                                        <td>
                                            <a href="{{ route('admin.quiz.indexSoal', $quiz['quiz_id']) }}"
                                                class="btn btn-primary">Detail</a>

                                            <a href="{{ route('admin.quiz.edit', $quiz['quiz_id']) }}"
                                                class="btn btn-secondary">Edit</a>

                                            <form action="{{ route('admin.quiz.destroy', $quiz['quiz_id']) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger"
                                                    onclick="return confirm('Hapus quiz ini?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
