@extends('layouts.master')
@section('title') New Invoice @endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="cc-page-head">
    <div><h4>New Invoice</h4><div class="sub"><a href="{{ route('invoices.index') }}">&larr; Back to invoices</a></div></div>
</div>
@include('invoices._form')
@endsection
