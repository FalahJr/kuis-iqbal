<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html" class="" style="width:100%">
                {{-- <img alt="image" class="rounded-circle mr-1" width="50" src="{{ asset('img/jatim.png') }}"> --}}
                <span style="width: 50%">Admin Dashboard</span>
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">
                <img alt="image" class="rounded-circle" width="50" src="{{ asset('img/jatim.png') }}">
            </a>
        </div>
        <div class="d-flex flex-column justify-content-between" style="height: 90vh">
            <ul class="sidebar-menu">

                <li class="">
                    <a class="nav-link" href="{{ url('admin/home') }}"><i class="fas fa-th-large"></i>
                        <span>Dashboard</span></a>
                </li>
                <li class="">
                    <a class="nav-link" href="{{ url('admin/quiz') }}"><i class="fas fa-home"></i>
                        <span>Management Quiz</span></a>
                </li>

            </ul>
        </div>
    </aside>
</div>
