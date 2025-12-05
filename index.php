<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlimentoSolidário - Início</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-950 antialiased">

    <header class="border-b bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    <span class="text-xl font-medium tracking-tight">AlimentoSolidário</span>
                </div>
                
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#sobre" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Sobre</a>
                    <a href="#como-funciona" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Como Funciona</a>
                    <a href="#usuarios" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Para Você</a>
                </nav>
                
                <div class="hidden md:flex items-center gap-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="feed.php" class="bg-green-600 text-white hover:bg-green-700 h-10 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Ir para o App
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="border border-gray-200 bg-white hover:bg-gray-100 h-9 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Entrar
                        </a>
                        <a href="cadastro.php" class="bg-green-600 text-white hover:bg-green-700 h-9 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Cadastrar-se
                        </a>
                    <?php endif; ?>
                </div>

                <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-gray-900 p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-100 pt-4 space-y-4">
                <nav class="flex flex-col gap-4">
                    <a href="#sobre" class="text-base font-medium text-gray-600 hover:text-green-600">Sobre</a>
                    <a href="#como-funciona" class="text-base font-medium text-gray-600 hover:text-green-600">Como Funciona</a>
                    <a href="#usuarios" class="text-base font-medium text-gray-600 hover:text-green-600">Para Você</a>
                </nav>
                <div class="flex flex-col gap-3 pt-2">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="feed.php" class="text-center bg-green-600 text-white py-2 rounded-md font-medium">Ir para o App</a>
                    <?php else: ?>
                        <a href="login.php" class="text-center border border-gray-200 py-2 rounded-md font-medium">Entrar</a>
                        <a href="cadastro.php" class="text-center bg-green-600 text-white py-2 rounded-md font-medium">Cadastrar-se</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <section id="sobre" class="relative py-12 lg:py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 -z-10"></div>
        <div class="container mx-auto px-4 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 text-center lg:text-left">
                    <div class="space-y-4">
                        <div class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Contra o Desperdício
                        </div>
                        
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium leading-tight text-gray-900">
                            Transformando <span class="text-green-600">excesso</span> em <span class="text-orange-600">refeições</span>
                        </h1>
                        
                        <p class="text-base md:text-lg text-gray-500 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                            Conectamos pequenos produtores, distribuidores e cozinheiros para combater o desperdício de alimentos e promover uma alimentação saudável.
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md bg-green-600 text-white hover:bg-green-700 h-11 px-8 font-medium transition-colors">
                            Começar Agora
                        </a>
                        <a href="#como-funciona" class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-transparent hover:bg-white text-gray-700 h-11 px-8 font-medium transition-colors">
                            Saiba Mais
                        </a>
                    </div>
                    
                    <div class="flex flex-wrap justify-center lg:justify-start gap-8 pt-4 border-t border-green-100/50">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">500+</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Produtores</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">1.2k</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Refeições</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">95%</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Aproveitamento</div>
                        </div>
                    </div>
                </div>
                
                <div class="relative mt-8 lg:mt-0">
                    <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-green-100 to-emerald-100 shadow-2xl ring-1 ring-gray-900/5">
                        <img src="https://images.unsplash.com/photo-1700064165267-8fa68ef07167?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmcmVzaCUyMHZlZ2V0YWJsZXMlMjBmcnVpdHMlMjBoZWFsdGh5JTIwZm9vZHxlbnwxfHx8fDE3NTg2NjkxNzV8MA&ixlib=rb-4.1.0&q=80&w=1080" alt="Frutas e vegetais frescos" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="py-16 md:py-20 bg-gray-50/50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-medium tracking-tight text-gray-900 mb-4">
                    Como Funciona
                </h2>
                <p class="text-gray-500 max-w-2xl mx-auto">
                    Um ecossistema simples e eficiente que conecta quem tem excesso de alimentos com quem pode transformá-los.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16 max-w-5xl mx-auto">
                <div class="text-center space-y-4 p-4 rounded-xl hover:bg-white transition-colors">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto shadow-sm text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">1. Produtor Doa</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Publica excedentes de frutas e vegetais no feed.
                    </p>
                </div>
                <div class="text-center space-y-4 p-4 rounded-xl hover:bg-white transition-colors">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto shadow-sm text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17h4V5H2v12h3M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5M14 17h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">2. Distribuidor Coleta</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Faz a logística até os cozinheiros parceiros.
                    </p>
                </div>
                <div class="text-center space-y-4 p-4 rounded-xl hover:bg-white transition-colors">
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mx-auto shadow-sm text-orange-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" x2="18" y1="17" y2="17"/></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">3. Cozinheiro Transforma</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Cria refeições saudáveis para a comunidade.
                    </p>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 md:p-12 shadow-sm border border-gray-100 max-w-6xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-2xl font-medium text-gray-900 mb-4">
                            Feed de Doações em Tempo Real
                        </h3>
                        <p class="text-gray-500 mb-6 leading-relaxed text-sm md:text-base">
                            Nossa plataforma funciona como uma rede social. Os produtores publicam, distribuidores coletam e cozinheiros transformam. Tudo transparente.
                        </p>
                        <ul class="space-y-3 text-sm md:text-base">
                            <li class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-700">Atualizações instantâneas</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-700">Geolocalização inteligente</span>
                            </li>
                        </ul>
                    </div>
                    <div class="aspect-video rounded-xl overflow-hidden bg-gradient-to-br from-green-50 to-emerald-50 shadow-lg mt-4 lg:mt-0">
                        <img src="https://images.unsplash.com/photo-1744870416768-25139537d856?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmb29kJTIwd2FzdGUlMjBkb25hdGlvbiUyMGNvbW11bml0eXxlbnwxfHx8fDE3NTg2NjkxNzd8MA&ixlib=rb-4.1.0&q=80&w=1080" alt="Comunidade" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="usuarios" class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-medium tracking-tight text-gray-900 mb-4">
                    Participe da Rede
                </h2>
                <p class="text-gray-500 max-w-2xl mx-auto">
                    Descubra como você pode fazer parte desta transformação.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Produtores</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-4">Evite o desperdício dos seus excedentes rurais ou urbanos.</p>
                    <a href="cadastro.php" class="block text-center border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200 text-gray-700 rounded-lg py-2 text-sm font-medium transition-colors">
                        Começar a Doar
                    </a>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M10 17h4V5H2v12h3M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5M14 17h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Distribuidores</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-4">Realize a logística solidária entre pontos de coleta.</p>
                    <a href="cadastro.php" class="block text-center border border-gray-200 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 text-gray-700 rounded-lg py-2 text-sm font-medium transition-colors">
                        Ser Distribuidor
                    </a>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" x2="18" y1="17" y2="17"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Cozinheiros</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-4">Receba ingredientes e crie refeições para quem precisa.</p>
                    <a href="cadastro.php" class="block text-center border border-gray-200 hover:bg-orange-50 hover:text-orange-700 hover:border-orange-200 text-gray-700 rounded-lg py-2 text-sm font-medium transition-colors">
                        Cozinhar Solidário
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t bg-gray-50 py-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm text-gray-500">&copy; 2024 AlimentoSolidário.</p>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>