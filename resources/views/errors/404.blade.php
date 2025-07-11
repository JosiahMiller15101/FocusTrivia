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
    'The Eiffel Tower can grow over 6 inches taller during the summer.',
    'A group of flamingos is called a “flamboyance.”',
    'Sloths can hold their breath longer than dolphins.',
    'The inventor of the frisbee was turned into a frisbee after he died.',
    'The dot over a lowercase “i” or “j” is called a “tittle.”',
    'Cows have best friends and get stressed when separated.',
    'The unicorn is the national animal of Scotland.',
    'There’s a basketball court on the top floor of the U.S. Supreme Court — it’s called the “highest court in the land.”',
    'A day on Mercury lasts longer than its year.',
    'You can’t hum while holding your nose closed.',
    'The longest hiccuping spree lasted 68 years.',
    'Honeybees can recognize human faces.',
    'There are more fake flamingos in the world than real ones.',
    'The first alarm clock could only ring at 4 a.m.',
    'Octopuses have nine brains and blue blood.',
    'Nintendo was founded in 1889 — originally making playing cards.',
    'Bananas are radioactive.',
    'The Eiffel Tower can be 15 cm taller in the summer.',
    'The inventor of the microwave got the idea when a chocolate bar melted in his pocket.',
    'Some turtles can breathe through their butts.',
    'Your brain uses about 20% of your body’s total energy.'
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
