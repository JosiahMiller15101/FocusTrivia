@extends('layouts.app')

@php
    $funFacts = [
        'The first computer bug was an actual moth.',
        'Octopuses have three hearts.',
        'Honey never spoils — archaeologists found 3,000-year-old edible honey!',
        'Sharks are older than trees.',
        'There are more stars in the universe than grains of sand on Earth.',
        'Bananas are berries, but strawberries aren’t.',
        'A day on Venus is longer than a year on Venus.',
        'Wombat poop is cube-shaped.',
        'Some cats are allergic to humans.',
        'The Eiffel Tower can grow over 6 inches taller during the summer.'
    ];
    $randomFact = $funFacts[array_rand($funFacts)];
@endphp

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 p-6">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-blue-700 mb-4">404</h1>
        <p class="text-2xl font-semibold text-gray-800 mb-2">Oops! Page not found.</p>
        <p class="text-gray-600 mb-6">The page you're looking for doesn’t exist or was moved.</p>
        <p class="text-sm mt-4 text-gray-600 italic">🧠 Fun Fact: {{ $randomFact }}</p>
        <a href="{{ url('/') }}" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow">
            ← Back to Home
        </a>
    </div>
</div>
@endsection
