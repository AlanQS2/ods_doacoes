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
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                <span class="text-xl font-medium tracking-tight">AlimentoSolidário</span>
            </div>
            
            <nav class="hidden md:flex items-center gap-8">
                <a href="#sobre" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Sobre</a>
                <a href="#como-funciona" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Como Funciona</a>
                <a href="#usuarios" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Para Você</a>
            </nav>
            
            <div class="flex items-center gap-3">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="feed.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-green-600 text-white hover:bg-green-700 h-10 px-4 py-2">
                        Ir para o App
                    </a>
                <?php else: ?>
                    <a href="login.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-gray-200 bg-white hover:bg-gray-100 hover:text-gray-900 h-9 px-4 py-2">
                        Entrar
                    </a>
                    <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-green-600 text-white hover:bg-green-700 h-9 px-4 py-2">
                        Cadastrar-se
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section id="sobre" class="relative py-20 lg:py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 -z-10"></div>
        <div class="container mx-auto px-4 relative">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <div class="inline-flex items-center rounded-full border border-transparent bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 w-fit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.28 3.6-1.28 5.09 0 1.49 1.28 1.49 3.36 0 4.63-1.49 1.28-3.6 1.28-5.09 0-1.49-1.28-1.49-3.36 0-4.63z"></path><path d="M24 12v-2"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path><path d="M12 6v6l4 2"></path></svg>
                            Contra o Desperdício
                        </div>
                        
                        <h1 class="text-4xl lg:text-6xl font-medium leading-tight tracking-tight text-gray-900">
                            Transformando <span class="text-green-600">excesso</span> em <span class="text-orange-600">refeições</span>
                        </h1>
                        
                        <p class="text-lg text-gray-500 max-w-lg leading-relaxed">
                            Conectamos pequenos produtores, distribuidores e cozinheiros para combater o desperdício de alimentos e promover uma alimentação saudável e solidária.
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md text-base font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-green-600 text-white hover:bg-green-700 h-11 px-8">
                            Começar Agora
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                        <a href="#como-funciona" class="inline-flex items-center justify-center rounded-md text-base font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-gray-200 bg-transparent hover:bg-white hover:text-gray-900 h-11 px-8">
                            Saiba Mais
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-8 pt-4 border-t border-green-100/50">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">500+</div>
                            <div class="text-sm text-gray-500">Produtores</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">1.2k</div>
                            <div class="text-sm text-gray-500">Refeições</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">95%</div>
                            <div class="text-sm text-gray-500">Menos Desperdício</div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-green-100 to-emerald-100 shadow-2xl ring-1 ring-gray-900/5">
                        <img src="https://images.unsplash.com/photo-1700064165267-8fa68ef07167?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmcmVzaCUyMHZlZ2V0YWJsZXMlMjBmcnVpdHMlMjBoZWFsdGh5JTIwZm9vZHxlbnwxfHx8fDE3NTg2NjkxNzV8MA&ixlib=rb-4.1.0&q=80&w=1080" alt="Frutas e vegetais frescos" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="py-20 bg-gray-50/50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-medium tracking-tight text-gray-900 mb-4">
                    Como o AlimentoSolidário Funciona
                </h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                    Um ecossistema simples e eficiente que conecta quem tem excesso de alimentos com quem pode transformá-los em refeições nutritivas.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16 max-w-5xl mx-auto">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900">1. Produtor Doa</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Pequenos produtores postam seus excedentes de frutas e vegetais no feed da plataforma
                    </p>
                </div>
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/><path d="M14 17h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900">2. Distribuidor Coleta</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Distribuidores pegam os alimentos e fazem a logística até os cozinheiros parceiros
                    </p>
                </div>
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" x2="18" y1="17" y2="17"/></svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900">3. Cozinheiro Transforma</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Cozinheiros criam refeições saudáveis e nutritivas com os alimentos recebidos
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 lg:p-12 shadow-sm border border-gray-100 max-w-6xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h3 class="text-2xl font-medium text-gray-900 mb-4">
                            Feed de Doações em Tempo Real
                        </h3>
                        <p class="text-gray-500 mb-8 leading-relaxed">
                            Nossa plataforma funciona como uma rede social para doações de alimentos. Os produtores publicam seus excedentes, distribuidores visualizam e coletam, e os cozinheiros recebem ingredientes frescos para suas criações.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-700">Atualizações em tempo real</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-700">Geolocalização para otimizar rotas</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-700">Sistema de avaliações e confiança</span>
                            </li>
                        </ul>
                    </div>
                    <div class="aspect-video rounded-xl overflow-hidden bg-gradient-to-br from-green-50 to-emerald-50 shadow-lg">
                        <img src="https://images.unsplash.com/photo-1744870416768-25139537d856?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmb29kJTIwd2FzdGUlMjBkb25hdGlvbiUyMGNvbW11bml0eXxlbnwxfHx8fDE3NTg2NjkxNzd8MA&ixlib=rb-4.1.0&q=80&w=1080" alt="Comunidade" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="usuarios" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-medium tracking-tight text-gray-900 mb-4">
                    Para Cada Participante
                </h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                    Cada papel é essencial no nosso ecossistema. Descubra como você pode fazer parte desta transformação.
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold leading-none tracking-tight">Para Produtores</h3>
                        <p class="text-sm text-gray-500 mt-2">Pequenos produtores rurais e urbanos</p>
                    </div>
                    <ul class="space-y-3 mb-6 text-sm text-gray-600">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-green-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Evite o desperdício dos seus excedentes</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-green-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Contribua para uma causa social</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-green-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Ganhe reconhecimento da comunidade</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-green-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Interface simples para publicar doações</span>
                        </li>
                    </ul>
                    <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-gray-100 hover:text-gray-900 h-10 px-4 py-2 w-full">
                        Começar a Doar
                    </a>
                </div>

                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/><path d="M14 17h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold leading-none tracking-tight">Para Distribuidores</h3>
                        <p class="text-sm text-gray-500 mt-2">Logística solidária e eficiente</p>
                    </div>
                    <ul class="space-y-3 mb-6 text-sm text-gray-600">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Rotas otimizadas por geolocalização</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Faça parte de uma rede de impacto</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Sistema de agendamento flexível</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Histórico de entregas e avaliações</span>
                        </li>
                    </ul>
                    <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-gray-100 hover:text-gray-900 h-10 px-4 py-2 w-full">
                        Ser Distribuidor
                    </a>
                </div>

                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" x2="18" y1="17" y2="17"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold leading-none tracking-tight">Para Cozinheiros</h3>
                        <p class="text-sm text-gray-500 mt-2">Transforme ingredientes em refeições</p>
                    </div>
                    <ul class="space-y-3 mb-6 text-sm text-gray-600">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-orange-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Ingredientes frescos e gratuitos</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-orange-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Crie refeições para a comunidade</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-orange-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Compartilhe suas criações culinárias</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-orange-600 rounded-full mt-1.5 flex-shrink-0"></div>
                            <span>Feed personalizado de ingredientes</span>
                        </li>
                    </ul>
                    <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-gray-100 hover:text-gray-900 h-10 px-4 py-2 w-full">
                        Cozinhar Solidário
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-br from-green-600 to-emerald-700 text-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-medium mb-6 leading-tight">
                        Nosso Impacto Coletivo
                    </h2>
                    <p class="text-lg text-green-100 mb-8 leading-relaxed">
                        Cada doação, cada entrega, cada refeição faz a diferença. Juntos, estamos construindo um futuro mais sustentável e solidário.
                    </p>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <div class="text-4xl font-medium mb-2">2.5 ton</div>
                            <div class="text-green-200">Alimentos salvos</div>
                        </div>
                        <div>
                            <div class="text-4xl font-medium mb-2">3.2k</div>
                            <div class="text-green-200">Refeições criadas</div>
                        </div>
                        <div>
                            <div class="text-4xl font-medium mb-2">850</div>
                            <div class="text-green-200">Famílias beneficiadas</div>
                        </div>
                        <div>
                            <div class="text-4xl font-medium mb-2">-65%</div>
                            <div class="text-green-200">Desperdício reduzido</div>
                        </div>
                    </div>
                </div>
                <div class="aspect-square rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10">
                    <img src="https://images.unsplash.com/photo-1740727665746-cfe80ababc23?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb29raW5nJTIwY2hlZiUyMGtpdGNoZW4lMjBwcmVwYXJhdGlvbnxlbnwxfHx8fDE3NTg2NjkxNzl8MA&ixlib=rb-4.1.0&q=80&w=1080" alt="Chef preparando" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl lg:text-4xl font-medium mb-6 tracking-tight text-gray-900">
                    Pronto para Fazer a Diferença?
                </h2>
                <p class="text-lg text-gray-500 mb-8 leading-relaxed">
                    Junte-se a centenas de produtores, distribuidores e cozinheiros que já estão transformando excesso em oportunidade.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="cadastro.php" class="inline-flex items-center justify-center rounded-md text-lg font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-green-600 text-white hover:bg-green-700 h-12 px-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Começar Agora
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                        <span class="font-bold text-gray-900">AlimentoSolidário</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Conectando comunidades para um futuro sem desperdício de alimentos.
                    </p>
                </div>
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-900">Plataforma</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#como-funciona" class="hover:text-gray-900 transition-colors">Como Funciona</a></li>
                        <li><a href="#usuarios" class="hover:text-gray-900 transition-colors">Para Produtores</a></li>
                        <li><a href="#usuarios" class="hover:text-gray-900 transition-colors">Para Distribuidores</a></li>
                        <li><a href="#usuarios" class="hover:text-gray-900 transition-colors">Para Cozinheiros</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-900">Recursos</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Central de Ajuda</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Guias</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Comunidade</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-900">Empresa</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Sobre Nós</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Impacto</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Parceiros</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Contato</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-12 pt-8 text-center text-sm text-gray-500">
                <p>&copy; 2024 AlimentoSolidário. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

</body>
</html>
