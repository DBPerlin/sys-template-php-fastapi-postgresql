<?php
session_start();

if(!isset($_SESSION["token"])){
    header("Location: admin.php");
    exit;
}

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
<?php

include 'assets/inc/head.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<title>Painel Interno - Relatos</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<link rel="stylesheet" href="assets/css/painel_interno.css">

</head>

<body>

<?php

include 'assets/inc/header.php';

?>

<div class="container py-5">

<h2 class="mb-4 mt-5">Painel Interno de relatos</h2>

<div class="table-responsive">

<table id="tabelaRelatos" class="table table-striped">

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
<th>    </th>
</tr>

</thead>

<tbody>

<?php if($data): ?>

<?php foreach($data["dados"] as $row): ?>

<tr>

<td class="text-center fw-bold">
<?php echo htmlspecialchars($row["id"]); ?>
</td>

<td class="text-center">
<?php if($row["status"] == 1): ?>
<span class="badge bg-success">Aprovado</span>
<?php elseif($row["status"] == 2): ?>
<span class="badge bg-danger">Rejeitado</span>
<?php else: ?>
<span class="badge bg-warning text-dark">Pendente</span>
<?php endif; ?>
</td>

<td> <?php echo htmlspecialchars($row["nome_completo"]); ?></td>

<td class="text-center">
<?php echo htmlspecialchars($row["crn_id"]); ?>
</td>

<td class="text-center">
<?php echo htmlspecialchars($estadoMap[$row["estado_id"]] ?? ""); ?>
</td>

<td>
<?php echo htmlspecialchars($row["area_nutricao"]); ?>
</td>

<td>
<?php echo htmlspecialchars($row["titulo_trabalho"]); ?>
</td>

<td class="text-center">

<button class="btn btn-sm btn-primary abrir-relato"

data-nome="<?php echo htmlspecialchars($row["nome_completo"]); ?>"
data-area="<?php echo htmlspecialchars($row["area_nutricao"]); ?>"
data-titulo="<?php echo htmlspecialchars($row["titulo_trabalho"]); ?>"

data-objetivo="<?php echo htmlspecialchars($row["objetivo_trabalho"]); ?>"
data-acoes="<?php echo htmlspecialchars($row["acoes_trabalho"]); ?>"
data-resultados="<?php echo htmlspecialchars($row["resultados_trabalho"]); ?>">

Abrir relato

</button>

</td>

<td class="text-center">
<?php echo htmlspecialchars($row["telefone"]); ?>
</td>

<td>
<?php echo htmlspecialchars($row["email"]); ?>
</td>

<td class="text-center">
<?php echo date("d/m/Y", strtotime($row["criado_em"] ?? '')); ?>
</td>

<td class="text-center">
<?php if(!empty($row["possui_arquivo"])): ?>
<a href="/api/vivencias/arquivo_experiencia/<?php echo $row["id"]; ?>"
   class="btn btn-sm btn-info"
   target="_blank">
   📥 Baixar
</a>
<?php else: ?>
<span class="text-muted">—</span>
<?php endif; ?>
</td>

<td class="text-center">
<?php if($row["status"] == 0): ?>
<button class="btn btn-sm btn-success aprovar-relato"
data-id="<?php echo $row["id"]; ?>">
Aprovar
</button>
<button class="btn btn-sm btn-danger rejeitar-relato mt-1"
data-id="<?php echo $row["id"]; ?>">
Rejeitar
</button>
<?php else: ?>
<button class="btn btn-sm btn-secondary" disabled>
🔒
</button>
<?php endif; ?>
</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
<td colspan="11" class="text-center">
Nenhum relato encontrado
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>


<!-- MODAL RELATO -->

<div class="modal fade" id="relatoModal" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-scrollable">

<div class="modal-content">

<div class="modal-header">

<h5 class="modal-title">Relato completo</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<h6 style="font-weight: bold;">Nome</h6>
<p id="modalNome"></p>

<hr>

<h6 style="font-weight: bold;">Área</h6>
<p id="modalArea"></p>

<hr>

<h6 style="font-weight: bold;">Título</h6>
<p id="modalTitulo"></p>

<hr>

<h6 style="font-weight: bold;">Objetivo</h6>
<p id="modalObjetivo"></p>

<hr>

<h6 style="font-weight: bold;">Ações realizadas</h6>
<p id="modalAcoes"></p>

<hr>

<h6 style="font-weight: bold;">Resultados</h6>
<p id="modalResultados"></p>

</div>

</div>

</div>

</div>


<!-- JS -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script src="assets/js/main.js"></script>


<script>

$(document).ready(function(){

$('#tabelaRelatos').DataTable({

pageLength: 10,

scrollX: true,

language: {
url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json"
}

});

});


// abrir relato no modal

document.addEventListener("click", function(e){

if(e.target.classList.contains("abrir-relato")){

let nome = e.target.dataset.nome
let area = e.target.dataset.area
let titulo = e.target.dataset.titulo
let objetivo = e.target.dataset.objetivo
let acoes = e.target.dataset.acoes
let resultados = e.target.dataset.resultados

document.getElementById("modalNome").innerText = nome
document.getElementById("modalArea").innerText = area
document.getElementById("modalTitulo").innerText = titulo
document.getElementById("modalObjetivo").innerText = objetivo
document.getElementById("modalAcoes").innerText = acoes
document.getElementById("modalResultados").innerText = resultados

let modal = new bootstrap.Modal(document.getElementById("relatoModal"))

modal.show()

}

})

// Script aprovar e rejeitar
document.addEventListener("click", function(e){

if(e.target.classList.contains("aprovar-relato")){

if(confirm("Tem certeza que deseja APROVAR este relato?")){

alterarStatus(e.target.dataset.id, 1)

}

}

if(e.target.classList.contains("rejeitar-relato")){

if(confirm("Tem certeza que deseja REJEITAR este relato?")){

alterarStatus(e.target.dataset.id, 2)

}

}

})

function alterarStatus(id, status){

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

if(data.sucesso){

alert("Status atualizado")

location.reload()

}

})

}

</script>

</body>
</html>
