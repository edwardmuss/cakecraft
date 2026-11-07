@extends('layouts.master')
@section('title') New Quotation @endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="cc-page-head">
    <div><h4>New Quotation</h4><div class="sub"><a href="{{ route('quotations.index') }}">&larr; Back to quotations</a></div></div>
</div>
@include('quotations._form')
@endsection
