@extends('layouts.app')
@section('content')
<h1>Daftar Cuti</h1>
@if(isset($leaves) && count($leaves) > 0)
    <!-- List cuti -->
@else
    <div class="text-center text-gray-500 my-8">Tidak ada data cuti.</div>
@endif
@endsection 