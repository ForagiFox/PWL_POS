@extends('layouts.template')

@section('content')
    @auth
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Halo {{ auth()->user()->username }}, apakabar!!!</h3>
                <div class="card-tools">
                    <a href="{{ url('logout') }}" class="btn btn-sm btn-danger mt-1">Logout</a>
                </div>
            </div>
            <div class="card-body">
                Selamat datang semua, ini adalah halaman utama dari aplikasi ini.
            </div>
        </div>
    @endauth
    @guest
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Halo, apakabar!!!</h3>
                <div class="card-tools">
                    <a href="{{ url('login') }}" class="btn btn-sm btn-success mt-1">Login</a>
                </div>
            </div>
            <div class="card-body">
                Selamat datang semua, ini adalah halaman utama dari aplikasi ini.
            </div>
        </div>

    @endguest
@endsection
