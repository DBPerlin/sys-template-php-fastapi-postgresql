<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Base | Projeto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 80px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .status-card {
            transition: transform 0.2s;
        }
        .status-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">NomeDoProjeto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#servicos">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="#status">Status do Sistema</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-dark">Nome Do Projeto</h1>
            <p class="lead text-muted mt-3 mb-4">Template estruturado para projetos com PHP, FastAPI e PostgreSQL.</p>
            <a href="#status" class="btn btn-primary btn-lg px-4 gap-3">Verificar Conexões</a>
        </div>
    </header>

    <section id="status" class="container py-5">
        <div class="row text-center mb-4">
            <h2>Status dos Serviços</h2>
            <p class="text-muted">Comunicação em tempo real entre os containers.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card status-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">PostgreSQL via PHP</h5>
                        <hr>
                        <?php
                            $host = 'db';
                            $db   = getenv('POSTGRES_DB') ?: 'template_db';
                            $user = getenv('POSTGRES_USER') ?: 'admin';
                            $pass = getenv('POSTGRES_PASSWORD') ?: 'senha_padrao';
                            $port = "5433";

                            $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

                            try {
                                $pdo = new PDO($dsn);
                                echo '<div class="alert alert-success mb-0" role="alert">
                                        <strong>Online!</strong> Conectado com sucesso ao banco de dados.
                                      </div>';
                            } catch (PDOException $e) {
                                echo '<div class="alert alert-danger mb-0" role="alert">
                                        <strong>Offline!</strong> Erro: ' . htmlspecialchars($e->getMessage()) . '
                                      </div>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card status-card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-success">FastAPI via JavaScript</h5>
                        <hr>
                        <div id="api-status">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2 text-muted">Testando API na porta 8000...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">© 2026 Template Base. Pronto para adaptação.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiStatusDiv = document.getElementById('api-status');
            
            fetch('http://localhost:8000/')
                .then(response => {
                    if (!response.ok) throw new Error('Rede não respondeu');
                    return response.json();
                })
                .then(data => {
                    apiStatusDiv.innerHTML = `
                        <div class="alert alert-success mb-0" role="alert">
                            <strong>Online!</strong> ${data.message}
                        </div>
                    `;
                })
                .catch(err => {
                    apiStatusDiv.innerHTML = `
                        <div class="alert alert-danger mb-0" role="alert">
                            <strong>Offline!</strong> O container do backend não está acessível.
                        </div>
                    `;
                });
        });
    </script>
</body>
</html>
