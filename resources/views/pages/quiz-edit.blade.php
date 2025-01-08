@extends('layouts.app')

@section('title', 'Edit Quiz')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Quiz</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('/admin/home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('admin.quiz.index') }}">Quiz</a></div>
                    <div class="breadcrumb-item">Edit Quiz</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <form action="{{ route('admin.quiz.update', $id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="quiz_id">Quiz ID</label>
                                <input type="text" id="quiz_id" class="form-control" name="quiz_id"
                                    value="{{ $quiz['quiz_id'] }}" required>
                            </div>
                            <div class="form-group">
                                <label for="quiz_name">Nama Quiz</label>
                                <input type="text" id="quiz_name" class="form-control" name="quiz_name"
                                    value="{{ $quiz['quiz_name'] }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.quiz.index') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
