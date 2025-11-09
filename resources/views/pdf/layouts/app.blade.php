<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Ispis dokumenta' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 80px 40px;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 60px;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-white text-gray-900">
<header>
    @includeIf('pdf.layouts.header', ['record' => $record])
</header>

<footer>
    @includeIf('pdf.layouts.footer', ['record' => $record])
</footer>

<main class="px-6 py-4">
    {{ $slot }}
</main>
</body>
</html>
