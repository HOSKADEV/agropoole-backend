@extends('layouts/contentNavbarLayout')

@section('title', __('Diffusion'))

@section('content')

<div class="container-xxl container-p-y d-flex justify-content-center">
  <div class="misc-wrapper" style="text-align: center !important">
      <h3 class="mb-2 mx-2">Diffusion</h3>
      <p class="mb-6 mx-2">
        Vous n'avez aucune diffusion pour le moment. Veuillez nous contacter pour partager vos prochaines diffusions.
          <h5>+213 771 99 47 34</h5>
      </p>
      <div class="mt-6">
          <img src="{{ url('/assets/img/illustrations/office-amico.png') }}" width="400" class="img-fluid">
      </div>
  </div>
</div>
@endsection
