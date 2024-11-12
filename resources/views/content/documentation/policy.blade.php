@extends('layouts/contentNavbarLayout')

@section('title', __('Privacy policy'))

@section('content')

<h4 class="fw-bold py-3 mb-3">{{__('Privacy policy')}}</h4>

<div class="card mb-4">

  <div class="card-body">

    {!! $privacy_policy !!}

  </div>
</div>

@endsection

