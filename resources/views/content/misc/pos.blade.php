@extends('layouts/contentNavbarLayout')

@section('title', __('POS'))

@section('content')

<div class="container-xxl container-p-y d-flex justify-content-center">
  <div class="misc-wrapper" style="text-align: center !important">
      <h3 class="mb-2 mx-2">POS</h3>
      <p class="mb-6 mx-2">
        Contactez-nous pour profiter de ce service.
        <h5>+213 771 99 47 34</h5>
      </p>
      <div class="mt-6">
          <img src="{{ url('/assets/img/illustrations/Logistics-amico.png') }}" width="400" class="img-fluid">
      </div>
  </div>
</div>
@endsection
