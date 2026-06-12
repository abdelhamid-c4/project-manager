@extends('layouts.app')
@section('title', 'AI Work Report')

@section('content')
<div
    id="ai-report-page"
    data-report='@json($reportData)'
></div>
@endsection
