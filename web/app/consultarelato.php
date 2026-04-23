<?php

$api_url = "http://backend:8000/vivencias/lista_experiencias";

$options = [
    "http" => [
        "method" => "GET"
    ]
];

$context = stream_context_create($options);

$response = file_get_contents($api_url, false, $context);

$data = json_decode($response, true);

?>

<?php
require 'assets/data/crnList.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<meta charset="UTF-8">
<title>Vivências em Nutrição</title>

<?php

include 'assets/inc/head.php';

?>

<body>

  <?php
  include 'assets/inc/header.php';
  ?>
  
  <div class="mt-5"></div>
  
    <main id="main">

      <section id="relatos-publicos" class="recent-blog-posts">

        <div class="container" data-aos="fade-up">

          <div class="mt-5"></div>

            <header class="section-header">
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

              <?php if($data): ?>
              <?php foreach($data["dados"] as $row): ?>
              <?php if($row["status"] != 1) continue; ?>

              <div class="col-lg-4 mb-4 relato-card"

                data-autor="<?php echo strtolower($row["nome_completo"]); ?>"
                data-area="<?php echo strtolower($row["area_nutricao"]); ?>"
                data-titulo="<?php echo strtolower($row["titulo_trabalho"]); ?>"
                data-estado="<?php echo strtolower($estadoMap[$row["estado_id"]] ?? ""); ?>"
                >

                <div class="post-box h-100">

                  <div class="post-img">

                   <?php if($row["possui_arquivo"]): ?>

                    <div class="post-img">
                        <img 
                            src="/api/vivencias/arquivo_experiencia/<?php echo $row["id"]; ?>" 
                            class="img-fluid"
                            onerror="this.parentElement.style.display='none'"
                        >
                    </div>  
                  <?php endif; ?>

                  </div>

                  <!-- DATA -->
                  <span class="post-date">
                    <?php echo date("d/m/Y", strtotime($row["criado_em"] ?? '')); ?>
                  </span>

                  <!-- TÍTULO -->
                  <h3 class="post-title">
                    <?php echo htmlspecialchars($row["titulo_trabalho"]); ?>
                  </h3>

                  <p>
                    <strong>Área:</strong>
                    <?php echo htmlspecialchars($row["area_nutricao"]); ?>

                    <br>

                    <strong>Autor:</strong>
                    <?php echo htmlspecialchars($row["nome_completo"]); ?>

                    <br>

                    <strong>Estado:</strong>
                    <?php echo htmlspecialchars($estadoMap[$row["estado_id"]] ?? ""); ?>

                  </p>

                  <!-- CLICÁVEL DO MODAL -->
                  <a href="#"
                    class="readmore stretched-link mt-auto abrir-relato"

                    data-id="<?php echo $row["id"]; ?>"
                    data-tem-arquivo="<?php echo $row["possui_arquivo"] ? 'true' : 'false'; ?>"
                    data-nome="<?php echo htmlspecialchars($row["nome_completo"]); ?>"
                    data-area="<?php echo htmlspecialchars($row["area_nutricao"]); ?>"
                    data-titulo="<?php echo htmlspecialchars($row["titulo_trabalho"]); ?>"
                    data-objetivo="<?php echo htmlspecialchars($row["objetivo_trabalho"]); ?>"
                    data-acoes="<?php echo htmlspecialchars($row["acoes_trabalho"]); ?>"
                    data-resultados="<?php echo htmlspecialchars($row["resultados_trabalho"]); ?>"
                    data-estado="<?php echo htmlspecialchars($estadoMap[$row["estado_id"]] ?? ""); ?>"
                    >
                    <span>Leia mais</span>
                    <i class="bi bi-arrow-right"></i>
                  </a>

                </div>

              </div>

              <?php endforeach; ?>
              <?php else: ?>
                <p>Nenhum relato encontrado.</p>
              <?php endif; ?>

            </div>

          </div>

        </div>

      </section>

    </main>

    <?php

    include 'assets/inc/footer.php';

    ?>

    <!-- BOTÃO BACK TO TOP -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS -->
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- JS MODAL -->
  <script src="assets/js/main.js"></script>

    <div class="modal fade" id="relatoModal" tabindex="-1">

      <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title" id="modalTitulo"></h5>

            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

          </div>

          <!-- MODAL -->

          <div class="modal-body">

           <!-- ARRUMAR IMAGEM -->
            <div class="text-center mb-3">
              <img id="modalImagem" class="img-fluid rounded w-100" style="max-height:300px; object-fit: cover;">
              <!--<img id="modalImagem" class="img-fluid rounded w-100" style="max-height:300px; object-fit: contain; background:#ffffff;">-->
            </div> 

            <div class="text-center mb-3">
            <a id="btnDownload" class="btn btn-success btn-primary" target="_blank">
            Baixar arquivo
            </a>
            </div>

            <p><strong>Autor:</strong> <span id="modalNome"></span></p>
            <p><strong>Área:</strong> <span id="modalArea"></span></p>
            <p><strong>Estado:</strong> <span id="modalEstado"></span></p>

            <hr>

              <h6><strong>Objetivo</strong></h6>
              <p id="modalObjetivo"></p>

            <hr>

            <h6><strong>Ações realizadas</strong></h6>
            <p id="modalAcoes"></p>

            <hr>

            <h6><strong>Resultados</strong></h6>
            <p id="modalResultados"></p>

          </div>

        </div>

      </div>

    </div>


  <!-- SCRIPT -->
  <script>

    document.addEventListener("DOMContentLoaded", function(){

    document.addEventListener("click", function(e){

    const btn = e.target.closest(".abrir-relato")
    if(!btn) return

    const btnDownload = document.getElementById("btnDownload")
    const modalImagem = document.getElementById("modalImagem")

    const id = btn.dataset.id
    const temArquivo = btn.dataset.temArquivo

    const urlArquivo = "/api/vivencias/arquivo_experiencia/" + id

    // IMAGEM
    if(modalImagem){
    modalImagem.src = urlArquivo
    modalImagem.style.display = "block"

    modalImagem.onerror = function(){
    this.style.display = "none"
    }
    }

    // DOWNLOAD
    if(btnDownload){
    btnDownload.href = urlArquivo

    if(temArquivo === "true"){
    btnDownload.style.display = "inline-block"
    }else{
    btnDownload.style.display = "none"
    }
    }

    const modalNome = document.getElementById("modalNome")
    const modalArea = document.getElementById("modalArea")
    const modalEstado = document.getElementById("modalEstado")
    const modalTitulo = document.getElementById("modalTitulo")
    const modalObjetivo = document.getElementById("modalObjetivo")
    const modalAcoes = document.getElementById("modalAcoes")
    const modalResultados = document.getElementById("modalResultados")

    if(modalNome) modalNome.innerText = btn.dataset.nome || ""
    if(modalArea) modalArea.innerText = btn.dataset.area || ""
    if(modalEstado) modalEstado.innerText = btn.dataset.estado || ""
    if(modalTitulo) modalTitulo.innerText = btn.dataset.titulo || ""
    if(modalObjetivo) modalObjetivo.innerText = btn.dataset.objetivo || ""
    if(modalAcoes) modalAcoes.innerText = btn.dataset.acoes || ""
    if(modalResultados) modalResultados.innerText = btn.dataset.resultados || ""

    const modal = new bootstrap.Modal(document.getElementById("relatoModal"))
    modal.show()

    })

    })

   function filtrarRelatos(filtros = {}) {

    const cards = document.querySelectorAll(".relato-card")

    cards.forEach(card => {

        const autor = card.dataset.autor.toLowerCase()
        const area = card.dataset.area.toLowerCase()
        const titulo = card.dataset.titulo.toLowerCase()
        const estado = card.dataset.estado.toLowerCase()

        let mostrar = true

        if (filtros.busca) {
            const busca = filtros.busca
            if (
                !autor.includes(busca) &&
                !area.includes(busca) &&
                !titulo.includes(busca) &&
                !estado.includes(busca)
            ) {
                mostrar = false
            }
        }

        if (filtros.estado) {
            if (estado !== filtros.estado) {
                mostrar = false
            }
        }

        if (filtros.area) {
            if (area !== filtros.area) {
                mostrar = false
            }
        }

        card.style.display = mostrar ? "block" : "none"
    })
}

    document.getElementById("filtroRelatos").addEventListener("keyup", function () {
        filtrarRelatos({
            busca: this.value.toLowerCase()
        })
    })
    
    const params = new URLSearchParams(window.location.search)

    const filtros = {
        estado: params.get("estado")?.toLowerCase(),
        area: params.get("area")?.toLowerCase(),
        busca: params.get("busca")?.toLowerCase()
    }

filtrarRelatos(filtros)

    if(area){
    document.getElementById("filtroRelatos").value = area
    filtrarRelatos()
    }

    if(busca){
    document.getElementById("filtroRelatos").value = busca
    filtrarRelatos()
    }

    if(estado){
    document.getElementById("filtroRelatos").value = estado
    filtrarRelatos()
    }

  </script>

</body>
</html>