<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found | FOCUS Trivia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-800">
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
    <div class="bg-white/90 rounded-xl shadow-2xl px-10 py-12 max-w-lg w-full text-center">
        <h1 class="text-7xl font-extrabold text-slate-700 mb-4">500</h1>
        <p class="text-2xl font-bold text-slate-700 mb-2">Server Error</p>
        <p class="text-slate-600 mb-6">Something went wrong on the server.</p>
        @if(isset($exception) && app()->environment('production'))
            <div class="bg-red-100 text-red-800 rounded p-4 text-left mt-4 overflow-x-auto">
                <div class="break-all">
                    <strong>Error:</strong> {{ $exception->getMessage() }}<br>
                    <strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
                </div>
            </div>
        @else
            <p class="text-slate-600 mb-6">Please try again later.</p>
        @endif
        <p class="text-base mt-4 text-blue-900 italic bg-blue-100 rounded px-3 py-2">🧠 Fun Fact: {{ $randomFact }}</p>
        <a href="/" class="mt-8 inline-block bg-slate-600 hover:bg-slate-700 text-white font-semibold py-2 px-6 rounded shadow transition">← Back to Home</a>
    </div>
</body>
</html>
