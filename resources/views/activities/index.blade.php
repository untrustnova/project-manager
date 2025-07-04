@extends('layouts.app')
@section('content')
<h1>Daftar Aktivitas</h1>
@if(isset($activities) && count($activities) > 0)
    <!-- List aktivitas -->
@else
    <div class="text-center text-gray-500 my-8">Tidak ada data aktivitas.</div>
@endif
@endsection 