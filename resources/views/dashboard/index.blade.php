@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div
    id="project-dashboard"
    data-dashboard='@json($dashboardData)'
></div>
@endsection
