<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <form action="{{ route('login') }}" method="POST" class="bg-white p-8 rounded-lg shadow-md w-96">
        @csrf
        <h2 class="text-2xl font-bold mb-6">Connexion</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-600 p-2 mb-4 rounded">{{ $errors->first() }}</div>
        @endif

        <input type="email" name="email" placeholder="Email" class="w-full p-2 border rounded mb-4" required>
        <input type="password" name="password" placeholder="Mot de passe" class="w-full p-2 border rounded mb-4" required>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Se connecter</button>
    </form>
</body>
</html>
