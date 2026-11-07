@extends('layouts.master')
@section('title') Edit Invoice @endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="cc-page-head">
    <div><h4>Edit {{ $invoice->invoice_number }}</h4><div class="sub"><a href="{{ route('invoices.show', $invoice->id) }}">&larr; Back to invoice</a></div></div>
</div>
@include('invoices._form')
@endsection
