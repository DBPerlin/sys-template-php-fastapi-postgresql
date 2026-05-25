<?php
require_once 'assets/data/crnList.php';

$api_url = "http://backend:8000/vivencias/lista_experiencias";
$options = [
    "http" => [
        "method" => "GET",
        "ignore_errors" => true
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
    <title>Vivências em Nutrição</title>
    <?php include 'assets/inc/head.php'; ?>
    
    <link href="assets/css/consultarelato.css" rel="stylesheet">
</head>

<body>

    <?php include 'assets/inc/header.php'; ?>
    
    <main id="main">
        <section id="relatos-publicos" class="recent-blog-posts mt-5">
            <div class="container" data-aos="fade-up">

                <header class="section-header mt-5">
                    <p>Confira mais relatos de profissionais</p>
                </header>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <input 
                            type="text" 
                            id="filtroRelatos" 
                            class="form-control"
                            placeholder="Filtrar por autor, área ou título..."
                        >
                    </div>
                </div>

                <div class="row">
                    <?php if (!empty($data["dados"])): ?>
                        <?php foreach ($data["dados"] as $row): ?>
                            <?php if ($row["status"] != 1) continue; ?>

                            <div class="col-lg-4 mb-4 relato-card"
                                data-autor="<?php echo htmlspecialchars(strtolower($row["nome_completo"] ?? "")); ?>"
                                data-area="<?php echo htmlspecialchars(strtolower($row["area_nutricao"] ?? "")); ?>"
                                data-titulo="<?php echo htmlspecialchars(strtolower($row["titulo_trabalho"] ?? "")); ?>"
                                data-estado="<?php echo htmlspecialchars(strtolower($estadoMap[$row["estado_id"]] ?? "")); ?>">

                                <div class="post-box h-100 d-flex flex-column">

                                    <?php if (!empty($row["possui_arquivo"])): ?>
                                        <div class="post-img text-center">
                                            <img 
                                                src="/api/vivencias/arquivo_experiencia/<?php echo $row["id"]; ?>" 
                                                class="img-fluid"
                                                style="width: 100%; height: 220px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;"
                                                onerror="this.parentElement.style.display='none'"
                                                alt="Imagem do relato"
                                            >
                                        </div>  
                                    <?php endif; ?>

                                    <div class="p-3 d-flex flex-column flex-grow-1">
                                        <span class="post-date mt-2">
                                            <?php echo date("d/m/Y", strtotime($row["criado_em"] ?? 'now')); ?>
                                        </span>

                                        <h3 class="post-title text-break">
                                            <?php echo htmlspecialchars($row["titulo_trabalho"]); ?>
                                        </h3>

                                        <p class="mb-4">
                                            <strong>Área:</strong> <?php echo htmlspecialchars($row["area_nutricao"]); ?><br>
                                            <strong>Autor:</strong> <?php echo htmlspecialchars($row["nome_completo"]); ?><br>
                                            <strong>Estado:</strong> <?php echo htmlspecialchars($estadoMap[$row["estado_id"]] ?? ""); ?>
                                        </p>

                                        <a href="#" class="readmore stretched-link mt-auto abrir-relato"
                                            data-id="<?php echo $row["id"]; ?>"
                                            data-tem-arquivo="<?php echo $row["possui_arquivo"] ? 'true' : 'false'; ?>"
                                            data-nome="<?php echo htmlspecialchars($row["nome_completo"]); ?>"
                                            data-area="<?php echo htmlspecialchars($row["area_nutricao"]); ?>"
                                            data-titulo="<?php echo htmlspecialchars($row["titulo_trabalho"]); ?>"
                                            data-objetivo="<?php echo htmlspecialchars($row["objetivo_trabalho"]); ?>"
                                            data-acoes="<?php echo htmlspecialchars($row["acoes_trabalho"]); ?>"
                                            data-resultados="<?php echo htmlspecialchars($row["resultados_trabalho"]); ?>"
                                            data-estado="<?php echo htmlspecialchars($estadoMap[$row["estado_id"]] ?? ""); ?>">
                                            <span>Leia mais</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-muted">Nenhum relato encontrado no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </main>

    <?php include 'assets/inc/footer.php'; ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <div class="modal fade" id="relatoModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable"> 
            <div class="modal-content">
                
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-break" id="modalTitulo" style="max-width: 95%;"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 clearfix">
                    
                    <div id="modalImagemContainer" class="float-lg-start me-lg-4 mb-3 text-center text-lg-start" style="max-width: 45%;">
                        <img id="modalImagem" class="modal-imagem-relato img-fluid rounded shadow-sm" style="display: none;" alt="Imagem da vivência">
                    </div> 

                    <div id="modalTextoContainer" class="text-break">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-success mb-0 fw-bold">Sobre o Relato</h6>
                            <a id="btnDownload" class="btn btn-sm btn-success" target="_blank" style="display: none;">
                                <i class="bi bi-download"></i> Baixar Anexo
                            </a>
                        </div>

                        <div class="bg-light p-3 rounded mb-4 overflow-hidden">
                            <p class="mb-1"><strong>Autor:</strong> <span id="modalNome"></span></p>
                            <p class="mb-1"><strong>Área:</strong> <span id="modalArea"></span></p>
                            <p class="mb-0"><strong>Estado:</strong> <span id="modalEstado"></span></p>
                        </div>

                        <h6 class="fw-bold">Objetivo</h6>
                        <p id="modalObjetivo" class="text-muted"></p>

                        <hr>
                        <h6 class="fw-bold">Ações realizadas</h6>
                        <p id="modalAcoes" class="text-muted"></p>

                        <hr>
                        <h6 class="fw-bold">Resultados</h6>
                        <p id="modalResultados" class="text-muted"></p>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Lógica do Modal
            document.addEventListener("click", function(e) {
                const btn = e.target.closest(".abrir-relato");
                if (!btn) return;
                
                e.preventDefault();

                const id = btn.dataset.id;
                const temArquivo = btn.dataset.temArquivo;
                const urlArquivo = `/api/vivencias/arquivo_experiencia/${id}`;

                // Elementos do Modal
                const modalImagem = document.getElementById("modalImagem");
                const imgContainer = document.getElementById("modalImagemContainer");
                const btnDownload = document.getElementById("btnDownload");

                // Resetando a visualização da imagem
                imgContainer.style.display = "block";
                modalImagem.style.display = "block";

                // Configs Imagem
                if (modalImagem) {
                    modalImagem.src = urlArquivo;
                    
                    // Se a imagem falhar, esconde o container
                    modalImagem.onerror = function() {
                        this.style.display = "none";
                        imgContainer.style.display = "none";
                    };
                }

                // Configs Download
                if (btnDownload) {
                    btnDownload.href = urlArquivo;
                    btnDownload.style.display = (temArquivo === "true") ? "inline-block" : "none";
                }

                // Textos
                const preencherCampo = (id, valor) => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = valor || "";
                };

                preencherCampo("modalNome", btn.dataset.nome);
                preencherCampo("modalArea", btn.dataset.area);
                preencherCampo("modalEstado", btn.dataset.estado);
                preencherCampo("modalTitulo", btn.dataset.titulo);
                preencherCampo("modalObjetivo", btn.dataset.objetivo);
                preencherCampo("modalAcoes", btn.dataset.acoes);
                preencherCampo("modalResultados", btn.dataset.resultados);

                // Exibir Modal
                const modal = new bootstrap.Modal(document.getElementById("relatoModal"));
                modal.show();
            });


            // Lógica de Filtro
            const filtroInput = document.getElementById("filtroRelatos");
            const cards = document.querySelectorAll(".relato-card");

            function filtrarRelatos(filtros = {}) {
                cards.forEach(card => {
                    const autor = card.dataset.autor || "";
                    const area = card.dataset.area || "";
                    const titulo = card.dataset.titulo || "";
                    const estado = card.dataset.estado || "";

                    let mostrar = true;

                    if (filtros.busca) {
                        const busca = filtros.busca;
                        if (!autor.includes(busca) && !area.includes(busca) && !titulo.includes(busca) && !estado.includes(busca)) {
                            mostrar = false;
                        }
                    }

                    if (filtros.estado && estado !== filtros.estado) mostrar = false;
                    if (filtros.area && area !== filtros.area) mostrar = false;

                    card.style.display = mostrar ? "block" : "none";
                });
            }

            // Input filtro
            if (filtroInput) {
                filtroInput.addEventListener("keyup", function() {
                    filtrarRelatos({ busca: this.value.toLowerCase() });
                });
            }

            // Lógica de Parâmetros da URL
            const params = new URLSearchParams(window.location.search);
            const paramsBusca = params.get("busca")?.toLowerCase();
            const paramsArea = params.get("area")?.toLowerCase();
            const paramsEstado = params.get("estado")?.toLowerCase();

            // Aplica os filtros baseados na URL no carregamento inicial
            filtrarRelatos({
                busca: paramsBusca,
                area: paramsArea,
                estado: paramsEstado
            });

            // Preenche o input visualmente com o filtro que estiver ativo
            if (filtroInput) {
                if (paramsBusca) filtroInput.value = paramsBusca;
                else if (paramsArea) filtroInput.value = paramsArea;
                else if (paramsEstado) filtroInput.value = paramsEstado;
            }

        });
    </script>

</body>
</html>