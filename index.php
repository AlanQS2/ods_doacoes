<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlimentoSolidário - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900 font-sans">

    <header class="border-b bg-white sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                <span class="text-xl font-medium text-gray-900">AlimentoSolidário</span>
            </div>
            <nav class="hidden md:flex items-center gap-6">
                <a href="#sobre" class="text-gray-500 hover:text-gray-900 transition-colors">Sobre</a>
                <a href="#como-funciona" class="text-gray-500 hover:text-gray-900 transition-colors">Como Funciona</a>
            </nav>
            <div class="flex items-center gap-3">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="feed.php" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">Ir para o App</a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-700 hover:text-gray-900 px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">Entrar</a>
                    <a href="cadastro.php" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">Cadastrar-se</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section id="sobre" class="relative py-20 lg:py-32 overflow-hidden bg-gradient-to-br from-green-50 to-emerald-50">
        <div class="container mx-auto px-4 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <h1 class="text-4xl lg:text-6xl font-medium leading-tight">
                        Transformando <span class="text-green-600">excesso</span> em <span class="text-orange-600">refeições</span>
                    </h1>
                    <p class="text-lg text-gray-500 max-w-lg">
                        Conectamos produtores, distribuidores e cozinheiros para combater o desperdício e promover solidariedade.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="cadastro.php" class="bg-green-600 text-white px-8 py-3 rounded-md hover:bg-green-700 text-center font-medium">
                            Começar Agora
                        </a>
                    </div>
                </div>
                <div class="relative h-64 lg:h-auto bg-green-200 rounded-2xl overflow-hidden shadow-xl">
                    <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&q=80&w=1080" alt="Comida saudável" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-medium mb-4">Como Funciona</h2>
                <div class="grid md:grid-cols-3 gap-8 mt-12">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold text-green-600 mb-2">1. Produtor</h3>
                        <p class="text-gray-600">Cadastra os alimentos excedentes disponíveis para doação.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold text-blue-600 mb-2">2. Distribuidor</h3>
                        <p class="text-gray-600">Visualiza doações, coleta e leva até os cozinheiros.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-bold text-orange-600 mb-2">3. Cozinheiro</h3>
                        <p class="text-gray-600">Recebe os alimentos e transforma em refeições para a comunidade.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>