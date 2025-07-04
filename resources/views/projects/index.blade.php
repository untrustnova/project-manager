@extends('layouts.app')
@section('content')
<h1>Daftar Proyek</h1>
@if(isset($projects) && count($projects) > 0)
    <!-- List proyek -->
@else
    <div class="text-center text-gray-500 my-8">Tidak ada data proyek.</div>
@endif
@endsection 