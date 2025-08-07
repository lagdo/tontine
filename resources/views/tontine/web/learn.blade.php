@extends('tontine.web.layout')

@section('page-title', __('Tutoriels vidéos'))

@section('content')
      <h3>Ces tutoriels vidéos d'une durée entre 3 et 9 minutes, divisés en {{ count($videos) }} parties, présentent les différentes fonctions de l'application Siak Tontine.</h3>
      <ul>
@foreach ($videos as $name => $part)
        <li><a href="#{{ $name }}">{{ $part['title'] }}</a></li>
@endforeach
      </ul>
      <p>Ces tutoriels sont également disponibles <a href="{{ $playlist }}" target="_blank">dans une playlist</a>.</p>
      <p><a href="{{ route('login') }}">{{ __('Back to the login page', locale: 'fr') }}</a></p>
@endsection
