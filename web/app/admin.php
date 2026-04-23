<!DOCTYPE html>
<html lang="pt-br">

<!---- PopUp cadastro ---->
<?php
$sucesso = isset($_GET['sucesso']);
$erro = isset($_GET['erro']);
?>

<?php if($sucesso): ?>

<div class="modal fade" id="modalSucesso" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Cadastro realizado</h5>
      </div>

      <div class="modal-body">
        Seu cadastro foi realizado com sucesso!
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" data-bs-dismiss="modal">
          OK
        </button>
      </div>

    </div>
  </div>
</div>

<script>
window.onload = function(){
    var modal = new bootstrap.Modal(document.getElementById('modalSucesso'));
    modal.show();
}
</script>

<?php endif; ?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Vivências em Nutrição</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CSS DO ADMIN -->
<link rel="stylesheet" href="./assets/css/admin.css">

</head>

<body class="admin-page">

  <div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="row w-100 justify-content-center">

      <div class="col-md-6 col-lg-4">

        <!-- LOGO -->
        <div class="text-center mb-4">

          <a href="index.php">
            <img src="assets/img/logo_sistema.png" style="max-width:280px">
          </a>

          <h4 class="mt-3">
            Conselho Federal de Nutrição
          </h4>

          <h3 class="mt-1">
            Bem-vindo ao sistema de gestão do Vivências em Nutrição
          </h3>

        </div>

        <!-- ALERTA -->
        <div class="alert alert-warning">

          <strong>Atenção.</strong> Você está em uma área restrita ao CFN.

          <br><br>

          Caso seja nutricionista e queira participar do projeto Vivências em Nutrição,
          <a href="index.php">clique aqui</a> para acessar o site público.
<!--
          <br><br>

          Caso seja membro do CFN e não possua cadastro administrativo,
          <a href="cadastro.php">clique aqui</a> para acessar a página de cadastro.
-->
        </div>

        <!-- CARD LOGIN -->
        <div class="card login-card shadow">

          <div class="card-body">

            <h5 class="text-center mb-4">Login</h5>

            <form method="post" action="enviar_login.php">

              <div class="mb-3">

                <label class="form-label">Usuário</label>

                <input
                  type="text"
                  name="nome"
                  class="form-control"
                  required
                  autofocus
                >

              </div>

              <div class="mb-3">

                <label class="form-label">Senha</label>

                <input
                  type="password"
                  name="senha"
                  class="form-control"
                  required
                >

              </div>

              <button class="btn w-100">

                Entrar

              </button>

            </form>

          </div>

        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>