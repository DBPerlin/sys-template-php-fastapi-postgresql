  <!-- ======= START Header ======= -->
  <header id="header" class="header fixed-top">
      <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

          <a href="index.php" class="logo d-flex align-items-center">
              <img src="assets/img/logo.png" alt="">
              <!-- <span>CFN</span> -->
          </a>

          <nav id="navbar" class="navbar">
              <ul>
                  <li><a class="nav-link scrollto" href="index.php">Início</a></li>
                  <li><a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#termoModal">
                    Consulte o edital</a></li>
                  <li><a class="nav-link scrollto" href="consultarelato.php">Descobrir relatos</a></li>
                  <li><a class="nav-link scrollto" href="form.php">Enviar relato</a></li>
                  <li><a class="getstarted scrollto" href="admin.php"><i class="bi bi-lock-fill"></i>‎ Administrativo</a></li>
              </ul>
              <i class="bi bi-list mobile-nav-toggle"></i>
          </nav><!-- .navbar -->

      </div>
  </header><!-- END Header -->

  <!-- MODAL EDITAL -->
  <div class="modal fade" id="termoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Consultar edital</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <iframe src="assets/docs/1_EDITAL_Vivencias_em_Nutricão_Cobran_2026_ final.pdf"
          width="100%" height="500px"></iframe>

        </div>

      </div>
    </div>
  </div>