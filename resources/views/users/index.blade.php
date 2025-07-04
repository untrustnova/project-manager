@extends('layouts.app')
@section('content')
<h1>Daftar Pengguna</h1>
@if(isset($users) && count($users) > 0)
    <!-- List user -->
@else
    <div class="text-center text-gray-500 my-8">Tidak ada data pengguna.</div>
@endif
@endsection 