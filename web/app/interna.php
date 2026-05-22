<?php
session_start();

if (!isset($_SESSION["token"])) {
    header("Location: admin.php");
    exit;
}

require_once 'assets/data/crnList.php';

$api_url = "http://backend:8000/vivencias/lista_experiencias";
$options = [
    "http" => [
        "method" => "GET",
        "ignore_errors" => true,
        "timeout" => 10
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($api_url, false, $context);
$data = $response ? json_decode($response, true) : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Interno - Relatos</title>

    <?php include 'assets/inc/head.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <link rel="stylesheet" href="assets/css/painel_interno.css">
    
    <style>
        .acoes-col { white-space: nowrap; }
    </style>
</head>

<body>

    <?php include 'assets/inc/header.php'; ?>

    <div class="container-fluid py-5 px-4 px-lg-5">
        <h2 class="mb-4 mt-5">Painel Interno de Relatos</h2>

        <div class="card">
            <div class="card-body">
                <table id="tabelaRelatos" class="table table-striped table-hover nowrap w-100" style="width: 100%;">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Nome</th>
                            <th>CRN</th>
                            <th>Estado</th>
                            <th>Área</th>
                            <th>Título</th>
                            <th>Relato</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Data</th>
                            <th>Arquivo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (is_array($data) && !empty($data["dados"])): ?>
                            <?php foreach ($data["dados"] as $row): ?>
                                <tr>
                                    <td class="text-center fw-bold">
                                        <?php echo (int)$row["id"]; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($row["status"] == 1): ?>
                                            <span class="badge bg-success">Aprovado</span>
                                        <?php elseif ($row["status"] == 2): ?>
                                            <span class="badge bg-danger">Rejeitado</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?php echo htmlspecialchars($row["nome_completo"] ?? ""); ?></td>
                                    
                                    <td class="text-center"><?php echo htmlspecialchars($row["crn_id"] ?? ""); ?></td>
                                    
                                    <td class="text-center"><?php echo htmlspecialchars($estadoMap[$row["estado_id"]] ?? ""); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($row["area_nutricao"] ?? ""); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($row["titulo_trabalho"] ?? ""); ?></td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary abrir-relato"
                                            data-nome="<?php echo htmlspecialchars($row["nome_completo"] ?? ""); ?>"
                                            data-area="<?php echo htmlspecialchars($row["area_nutricao"] ?? ""); ?>"
                                            data-titulo="<?php echo htmlspecialchars($row["titulo_trabalho"] ?? ""); ?>"
                                            data-objetivo="<?php echo htmlspecialchars($row["objetivo_trabalho"] ?? ""); ?>"
                                            data-acoes="<?php echo htmlspecialchars($row["acoes_trabalho"] ?? ""); ?>"
                                            data-resultados="<?php echo htmlspecialchars($row["resultados_trabalho"] ?? ""); ?>">
                                            Abrir relato
                                        </button>
                                    </td>

                                    <td class="text-center"><?php echo htmlspecialchars($row["telefone"] ?? ""); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($row["email"] ?? ""); ?></td>
                                    
                                    <td class="text-center">
                                        <?php echo date("d/m/Y", strtotime($row["criado_em"] ?? 'now')); ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if (!empty($row["possui_arquivo"])): ?>
                                            <a href="/api/vivencias/arquivo_experiencia/<?php echo (int)$row["id"]; ?>"
                                               class="btn btn-sm btn-info text-white"
                                               target="_blank">
                                               📥 Baixar
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center acoes-col">
                                        <?php if ($row["status"] == 0): ?>
                                            <button class="btn btn-sm btn-success aprovar-relato" data-id="<?php echo (int)$row["id"]; ?>">
                                                Aprovar
                                            </button>
                                            <button class="btn btn-sm btn-danger rejeitar-relato" data-id="<?php echo (int)$row["id"]; ?>">
                                                Rejeitar
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                🔒 Concluído
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" class="text-center text-muted">Nenhum relato encontrado</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="relatoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Relato Completo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold">Nome</h6>
                    <p id="modalNome"></p>
                    <hr>
                    
                    <h6 class="fw-bold">Área</h6>
                    <p id="modalArea"></p>
                    <hr>
                    
                    <h6 class="fw-bold">Título</h6>
                    <p id="modalTitulo"></p>
                    <hr>
                    
                    <h6 class="fw-bold">Objetivo</h6>
                    <p id="modalObjetivo"></p>
                    <hr>
                    
                    <h6 class="fw-bold">Ações Realizadas</h6>
                    <p id="modalAcoes"></p>
                    <hr>
                    
                    <h6 class="fw-bold">Resultados</h6>
                    <p id="modalResultados"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <script src="assets/js/main.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelaRelatos').DataTable({
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json"
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // ID
                    { responsivePriority: 2, targets: 2 }, // Nome
                    { responsivePriority: 3, targets: -1 } // Ações
                ]
            });
        });

        document.addEventListener("click", function(e) {
            
            // Lógica: Abrir Modal
            if (e.target.classList.contains("abrir-relato")) {
                const btn = e.target;
                
                document.getElementById("modalNome").innerText = btn.dataset.nome || "";
                document.getElementById("modalArea").innerText = btn.dataset.area || "";
                document.getElementById("modalTitulo").innerText = btn.dataset.titulo || "";
                document.getElementById("modalObjetivo").innerText = btn.dataset.objetivo || "";
                document.getElementById("modalAcoes").innerText = btn.dataset.acoes || "";
                document.getElementById("modalResultados").innerText = btn.dataset.resultados || "";

                const modal = new bootstrap.Modal(document.getElementById("relatoModal"));
                modal.show();
            }

            // Lógica: Aprovar Relato
            if (e.target.classList.contains("aprovar-relato")) {
                if (confirm("Tem certeza que deseja APROVAR este relato?")) {
                    alterarStatus(e.target.dataset.id, 1);
                }
            }

            // Lógica: Rejeitar Relato
            if (e.target.classList.contains("rejeitar-relato")) {
                if (confirm("Tem certeza que deseja REJEITAR este relato?")) {
                    alterarStatus(e.target.dataset.id, 2);
                }
            }
        });

        // Função de comunicação com a API
        function alterarStatus(id, status) {
            fetch("alterar_status_relato.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id: id,
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    alert("Status atualizado com sucesso!");
                    location.reload();
                } else {
                    alert("Ocorreu um erro ao atualizar o status.");
                }
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                alert("Erro ao tentar conectar com o servidor.");
            });
        }
    </script>
</body>
</html>