@extends('layouts.app')

@section('title','My Profile')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">My Profile</h4>

        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">

                    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                        <div class="flex-shrink-0 mx-sm-0 ">
                            <img
                                src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                                alt="user image" width="100" height="100"
                                class="d-block h-auto ms-0 ms-sm-4 mt-3 rounded-3 user-profile-img"
                            />
                        </div>
                        <div class="flex-grow-1 mt-3 mt-sm-5">
                            <div
                                class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4"
                            >
                                <div class="user-profile-info">
                                    <h4>{{ auth()->user()->nama }}</h4>
                                    <ul
                                        class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2"
                                    >
                                        <li class="list-inline-item fw-semibold"><i class="bx bx-id-card"></i> {{ auth()->user()->role?->getDescription()??'' }}</li>
                                        <li class="list-inline-item fw-semibold"><i class="bx bx-phone"></i> {{ auth()->user()->no_telp??'' }}</li>
                                    </ul>
                                </div>
                                @if(auth()->user()->is_active )
                                    <a href="javascript:void(0)" class="btn btn-success text-nowrap">
                                        <i class="bx bx-user-check me-1"></i>Active
                                    </a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-danger text-nowrap">
                                        <i class="bx bx-user-x me-1"></i>Not Active
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Header -->

        <!-- Navbar pills -->
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab=="data" ? "active" : "" }}" href="#navs-pills-data"
                           role="tab"
                           data-bs-toggle="tab"
                           data-bs-target="#navs-pills-data"
                           aria-controls="navs-pills-profile"><i class="bx bx-user me-1"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab=="settings" ? "active" : "" }}" href="#navs-pills-settings"
                           role="tab"
                           data-bs-toggle="tab"
                           data-bs-target="#navs-pills-settings"
                           aria-controls="navs-pills-settings"
                        ><i class="bx bx-cog me-1"></i> Account Settings</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--/ Navbar pills -->
        <div class="tab-content p-0">
            <div class="tab-pane {{ $tab=="data" ? "show active" : "" }} " id="navs-pills-data" role="tabpanel">
                <div class="card mb-4">
                    <form id="needs-validation" novalidate method="post"
                          enctype="multipart/form-data" data-parsley-validate
                          action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method("PUT")
                    <div class="card-body">
                        @include('includes.error_alert')
                        <div class="row">
                            <div class="mb-3 col-lg-9 col-md-9 col-sm-12">
                                <label class="form-label">Nama Pengguna</label>
                                <input type="text" class="form-control @error('nama') parsley-error @enderror" id="nama" name="nama"
                                       data-parsley-required  value="{{ old('nama',auth()->user()->nama??'') }}" />
                            </div>
                            <div class="mb-3 col-lg-3 col-md-3 col-sm-12">
                                <label class="form-label">Nomor Telp</label>
                                <input type="text" class="form-control @error('no_telp') parsley-error @enderror" id="no_telp" name="no_telp"
                                       data-parsley-required  value="{{ old('no_telp',auth()->user()->no_telp??'') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-lg-12">
                                <label class="form-label">Profile Picture (200x200)</label>
                                <input type="file" class="form-control @error('avatar') parsley-error @enderror" id="avatar" name="avatar" accept=".png,.jpg,.jpeg" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                    </form>
                </div>
            </div>
            <div class="tab-pane {{ $tab=="settings" ? "show active" : "" }}" id="navs-pills-settings" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ganti Password</h5>
                    </div>
                    <form id="needs-validation" novalidate method="post"
                          enctype="multipart/form-data" data-parsley-validate
                          action="{{ route('admin.profile.change-password') }}">
                        @csrf
                        @method("PUT")
                        <div class="card-body">
                            @include('includes.error_alert')
                            <div class="row">
                                <div class="mb-3 col-lg-4 col-md-4 col-sm-12">
                                    <label class="form-label">Password Lama</label>
                                    <input type="password" class="form-control @error('old_password') parsley-error @enderror" id="old_password" name="old_password"
                                           data-parsley-required  value="{{ old('old_password','') }}" />
                                </div>

                                <div class="mb-3 col-lg-4 col-md-4 col-sm-12">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('password') parsley-error @enderror" id="password" name="password"
                                           data-parsley-required  value="{{ old('password','') }}" />
                                </div>
                                <div class="mb-3 col-lg-4 col-md-4 col-sm-12">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control @error('password_confirmation') parsley-error @enderror" id="password_confirmation" name="password_confirmation"
                                           data-parsley-required  value="{{ old('password_confirmation','') }}" />
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.css') }}">
@endpush
@include('includes.parsleyJs')
@include('includes.bootstrapSelectJs')
