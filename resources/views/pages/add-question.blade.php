@extends('layouts.app')

@section('title', 'Tambah Soal')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Soal</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Soal</a></div>
                    <div class="breadcrumb-item">Tambah Soal</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Form Tambah Soal</h4>
                            </div>
                            <div class="card-body">
                                <!-- Alert Messages -->
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form class="form" action="{{ route('admin.soal.store', $quizId) }}" method="post">
                                    @csrf
                                    <div class="card-body">
                                        <!-- Pertanyaan -->
                                        <div class="form-group">
                                            <label>Pertanyaan</label>
                                            <textarea class="form-control" name="pertanyaan"></textarea>
                                        </div>

                                        <!-- Pilihan Jawaban -->
                                        <div class="form-group">
                                            <label>Pilihan Jawaban</label>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="pilihan_a">Pilihan A</label>
                                                    <input type="text" class="form-control" id="pilihan_a"
                                                        name="pilihan[]" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="pilihan_b">Pilihan B</label>
                                                    <input type="text" class="form-control" id="pilihan_b"
                                                        name="pilihan[]" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="pilihan_c">Pilihan C</label>
                                                    <input type="text" class="form-control" id="pilihan_c"
                                                        name="pilihan[]" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="pilihan_d">Pilihan D</label>
                                                    <input type="text" class="form-control" id="pilihan_d"
                                                        name="pilihan[]" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Jawaban Benar -->
                                        <div class="form-group">
                                            <label>Jawaban Benar</label>
                                            <select class="form-control selectric" name="jawaban" required>
                                                <option value="" hidden>Pilih Jawaban Benar</option>
                                                <option value="0">A</option>
                                                <option value="1">B</option>
                                                <option value="2">C</option>
                                                <option value="3">D</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/summernote/dist/summernote-bs4.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
