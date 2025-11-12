<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HERO - Human Engagement & Resource Optimizer | Sistema de Gestão de RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --hero-primary: #0d6efd;
            --hero-secondary: #6c757d;
            --hero-success: #28a745;
            --hero-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .hero-section {
            background: var(--hero-gradient);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            color: #2c3e50;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 4rem;
        }

        .cta-button {
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .benefits-section {
            background: #f8f9fa;
            padding: 80px 0;
        }

        .benefit-item {
            padding: 20px;
            text-align: center;
        }

        .benefit-icon {
            font-size: 3rem;
            color: var(--hero-primary);
            margin-bottom: 1rem;
        }

        .stats-section {
            background: var(--hero-gradient);
            color: white;
            padding: 60px 0;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .footer-section {
            background: #2c3e50;
            color: white;
            padding: 40px 0;
        }

        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content text-center">
                <div class="floating-animation">
                    <i class="fas fa-rocket fa-5x mb-4"></i>
                </div>
                <h1 class="hero-title">HERO</h1>
                <h2 class="hero-subtitle">Human Engagement & Resource Optimizer</h2>
                <p class="lead mb-4" style="font-size: 1.3rem;">
                    A solução completa para gestão de Recursos Humanos
                </p>
                <div class="mt-5">
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg cta-button me-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Acessar Sistema
                    </a>
                    <a href="#funcionalidades" class="btn btn-outline-light btn-lg cta-button">
                        <i class="fas fa-info-circle me-2"></i>Conhecer Mais
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="funcionalidades" class="py-5" style="padding: 80px 0;">
        <div class="container">
            <h2 class="section-title">Funcionalidades Principais</h2>
            <p class="section-subtitle">
                Um sistema completo e integrado para todas as necessidades do seu departamento de RH
            </p>

            <div class="row g-4">
                <!-- Gestão de Candidatos -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h4 class="card-title fw-bold">Gestão de Candidatos</h4>
                            <p class="card-text text-muted">
                                Cadastre e gerencie candidatos com histórico completo, timeline de processos seletivos, 
                                interações e observações. Acompanhe todo o ciclo de vida do candidato.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Processos Seletivos -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h4 class="card-title fw-bold">Processos Seletivos</h4>
                            <p class="card-text text-muted">
                                Crie processos seletivos com etapas personalizáveis, movimente candidatos entre etapas, 
                                registre interações e aprovações. Controle total do processo de seleção.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Gestão de Funcionários -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="card-title fw-bold">Gestão de Funcionários</h4>
                            <p class="card-text text-muted">
                                Gerencie dados completos dos funcionários, departamentos, cargos, histórico de movimentações 
                                e timeline de eventos. Tudo em um só lugar.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Negociações -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h4 class="card-title fw-bold">Negociações e Propostas</h4>
                            <p class="card-text text-muted">
                                Gerencie propostas de contratação com versionamento, controle de status (aceita, recusada, 
                                contraproposta) e anexos em PDF. Sistema completo de negociação.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Exames Médicos -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <h4 class="card-title fw-bold">Exames Médicos</h4>
                            <p class="card-text text-muted">
                                Agende exames admissionais e demissionais, gerencie clínicas parceiras, gere documentos 
                                em PDF e acompanhe o status de cada exame.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Movimentações -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h4 class="card-title fw-bold">Movimentações de Cargo</h4>
                            <p class="card-text text-muted">
                                Solicite movimentações de departamento e cargo com sistema de aprovação hierárquico. 
                                Controle total sobre mudanças organizacionais.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Desligamentos -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <h4 class="card-title fw-bold">Gestão de Desligamentos</h4>
                            <p class="card-text text-muted">
                                Registre desligamentos com informações completas: tipo, motivo, aviso prévio, rescisão, 
                                equipamentos devolvidos e observações detalhadas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Calendário -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h4 class="card-title fw-bold">Calendário Integrado</h4>
                            <p class="card-text text-muted">
                                Visualize aniversários, eventos, exames agendados e datas importantes em um calendário 
                                completo e interativo.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Timeline e Histórico -->
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                <i class="fas fa-history"></i>
                            </div>
                            <h4 class="card-title fw-bold">Timeline e Histórico</h4>
                            <p class="card-text text-muted">
                                Acompanhe todo o histórico de candidatos e funcionários com timeline visual, separando 
                                atividades e interações de forma clara e organizada.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <h2 class="section-title">Por que escolher o HERO?</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="fw-bold">Segurança</h5>
                        <p class="text-muted">Sistema seguro com controle de permissões e auditoria completa de ações.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold">Eficiência</h5>
                        <p class="text-muted">Automatize processos e reduza tempo gasto em tarefas administrativas.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="fw-bold">Responsivo</h5>
                        <p class="text-muted">Acesse de qualquer dispositivo, a qualquer hora, de qualquer lugar.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h5 class="fw-bold">Personalizável</h5>
                        <p class="text-muted">Adapte o sistema às necessidades específicas da sua empresa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Integrado</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Disponível</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">Escalável</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Confiável</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="padding: 80px 0;">
        <div class="container text-center">
            <h2 class="section-title">Pronto para transformar sua gestão de RH?</h2>
            <p class="section-subtitle">
                Comece a usar o HERO hoje mesmo e descubra como podemos otimizar seus processos
            </p>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg cta-button">
                <i class="fas fa-rocket me-2"></i>Começar Agora
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row ">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="fas fa-rocket me-2"></i>HERO
                    </h5>
                    <p class="text-white">
                        Human Engagement & Resource Optimizer<br>
                        Sistema completo de gestão de Recursos Humanos
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5 class="mb-3">Contato</h5>
                    <p class="text-white mb-0">
                        <i class="fas fa-envelope me-2"></i>contato@hero.com.br<br>
                        <i class="fas fa-phone me-2"></i>(00) 0000-0000
                    </p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center text-white">
                <p class="mb-0">&copy; {{ date('Y') }} HERO. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
