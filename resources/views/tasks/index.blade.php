@extends('layouts.app')
@section('content')
<h1>Daftar Tugas</h1>
@if(isset($tasks) && count($tasks) > 0)
    <!-- List tugas -->
@else
    <div class="text-center text-gray-500 my-8">Tidak ada data tugas.</div>
@endif
@endsection 