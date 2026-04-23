<?php
require 'assets/data/crnList.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Vivências em Nutrição</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/form.css">

</head>

<body>

<div class="container py-5">

<!-- LOGO -->
<div class="text-center mb-5">

<a href="index.php">
<img src="assets/img/logo.png" style="max-width:250px">
</a>

<h2 class="mt-4">
Preencha o formulário com seus dados
</h2>

</div>


<!-- INTRO -->
<div class="alert alert-info">

<h5>Bem-vindo(a)</h5>

<p>
A plataforma Vivências em Nutrição é uma oportunidade para nutricionistas e técnicos em nutrição e dietética,
de todo o Brasil, contarem as suas experiências nas diferentes áreas da Nutrição que promovem saúde,
bem-estar e desenvolvimento para a população.
</p>

</div>


<form id="form_submit" method="post" action="enviar_experiencia.php" enctype="multipart/form-data">

<!-- VALIDAÇÃO -->
<div class="card shadow-sm mb-4">

<div class="card-body">

<h4 class="mb-4">
1 - Validação da sua inscrição
</h4>

<div class="row g-3">

<div class="col-md-3">

<label class="form-label">CPF *</label>

<input
class="form-control"
required
name="cpf"
id="cpf"
placeholder="xxx.xxx.xxx-xx"
type="text">

</div>


<div class="col-md-4">

<label class="form-label">
Nome Completo *
</label>

<input
type="text"
name="nome_completo"
id="nome_completo"
class="form-control"
required>

</div>


<div class="col-md-3">

<label class="form-label">
Inscrição profissional *
</label>

<input
class="form-control"
required
name="inscricao"
id="inscricao"
type="text">

</div>


<div class="col-md-2">

<label class="form-label">CRN *</label>

<select name="crn_id" id="crn_id" class="form-select" required>

<option value="">Selecione</option>

<?php foreach ($crnList as $crn) { ?>

<option value="<?php echo $crn['id']; ?>">
<?php echo $crn['nome']; ?>
</option>

<?php } ?>

</select>
<button type="button" id="btnValidar" class="btn btn-success btn-lg mt-4">
Concluir e Enviar
</button>
</div>

</div>

</div>
</div>

<div id="form_restante" style="display:none;">

<!-- CONTATO -->
<div class="card shadow-sm mb-4">

<div class="card-body">

<h4 class="mb-4">2 - Dados de contato</h4>

<div class="row g-3">

<div class="col-md-4">

<label class="form-label">Telefone *</label>

<input class="form-control" required name="telefone" id="telefone" type="text">

</div>

<div class="col-md-6">

<label class="form-label">Email *</label>

<input class="form-control" required name="email" id="email" type="email">

</div>

</div>

</div>

</div>


<!-- TRABALHO -->
<div class="card shadow-sm mb-4">

<div class="card-body">

<h4 class="mb-4">3 - Descreva seu relato</h4>


<div class="mb-3">

<label class="form-label">Estado onde o relato se passa *</label>

<select name="estado_id" class="form-select" required>

<option value="">Selecione</option>

<?php foreach ($estadoList as $estado) { ?>

<option value="<?php echo $estado['id']; ?>">
<?php echo $estado['nome']; ?>
</option>

<?php } ?>

</select>

</div>


<div class="mb-3">

<label class="form-label">Área da nutrição</label>

<select required name="area_nutricao" class="form-select">

<option value="">Selecione</option>

<option>Nutrição Clínica</option>
<option>Nutrição em Esportes e Exercício Físico</option>
<option>Nutrição em Saúde Coletiva</option>
<option>Nutrição na Cadeia de Produção</option>
<option>Nutrição no Ensino, Pesquisa e Extensão</option>
<option>Nutrição em Alimentação Coletiva</option>

</select>

</div>


<div class="mb-3">

<label class="form-label">Título</label>

<input maxlength="90" class="form-control" required name="titulo_trabalho" type="text">

</div>


<div class="mb-3">

<label class="form-label">Objetivos</label>

<textarea maxlength="1000" rows="4" class="form-control" name="objetivo_trabalho" required></textarea>

</div>


<div class="mb-3">

<label class="form-label">Ações realizadas</label>

<textarea maxlength="1500" rows="4" class="form-control" name="acoes_trabalho" required></textarea>

</div>


<div class="mb-3">

<label class="form-label">Resultados</label>

<textarea maxlength="1500" rows="4" class="form-control" name="resultados_trabalho" required></textarea>

</div>

</div>

</div>


<!-- UPLOAD DE ARQUIVOS -->

<div class="card shadow-sm mb-4">

<div class="card-body">

    <h4 class="mb-4">4 - Upload de arquivos</h4>

    <input class="form-control mb-3" type="file" name="arquivo" accept="image/*,.pdf">

    <!-- ARRUMAR IMAGEM 
    <input class="form-control mb-3" type="file" name="fileUpload_imagem2" accept="image/*">

    <input class="form-control" type="file" name="fileUpload_imagem3" accept="image/*">
    -->

</div>

</div>

<div class="card shadow-sm mb-4">

<div class="card-body">

<h4 class="mb-4">5 - Termo de Responsabilidade</h4>

<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="checkDefault" required>
  <label class="form-check-label" for="checkDefault">
    "Declaro para os devidos fins de direito, sob as penas da lei e da legislação profissional,
	balizadas pelo Código de Ética e Conduta do Nutricionista, que as informações prestadas e
	documentos anexados na plataforma Vivências em Nutrição, são verdadeiros e autênticos
	(fiéis a verdade e condizentes com a realidade dos fatos à época relatados)."
  </label>
</div>

</div>

</div>

<div class="text-end">


<div class="mb-4">

<button id="save_button" class="btn btn-success btn-lg">
Concluir e Enviar
</button>

</div>

</div>

</form>

</div>


<!-- MODAL TERMO -->

<div class="modal fade" id="termoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">

<div class="modal-dialog modal-lg modal-dialog-scrollable">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Termo de consentimento</h5>
</div>

<div class="modal-body">

<p>Para enviar seu relato é necessário concordar com o termo abaixo:
</p>

<iframe src="assets/docs/1_EDITAL_Vivencias_em_Nutricão_Cobran_2026_ final.pdf" width="100%" height="400px"></iframe>

<div class="form-check mt-3">
<input class="form-check-input" type="checkbox" id="aceiteTermo">
<label class="form-check-label">
Li e concordo com os termos apresentados
</label>
</div>

</div>

<div class="modal-footer">

<button id="btnContinuar" class="btn btn-success" disabled>
Continuar
</button>

</div>

</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- SCRIPT VALIDAÇÃO PROFISSIONAL -->

<script>

document.addEventListener("DOMContentLoaded", function(){

document.getElementById("btnValidar").addEventListener("click", function(){

let formData = new FormData();
let cpf = document.getElementById("cpf").value.replace(/\D/g, '');
let nome = document.getElementById("nome_completo").value.toUpperCase().trim();

console.log({
    cpf,
    nome,
    registro: document.getElementById("inscricao").value,
    crn: document.getElementById("crn_id").value
});

formData.append("inscricao", document.getElementById("inscricao").value);
formData.append("crn_id", document.getElementById("crn_id").value);
formData.append("cpf", cpf);
formData.append("nome", nome);

fetch("enviar_valida_profissional.php", {
method: "POST",
body: formData
})

.then(response => response.text())
.then(text => {

    console.log("RESPOSTA BRUTA:", text);

    let data;

    try {
        data = JSON.parse(text);
    } catch(e){
        console.error("Erro ao converter JSON:", e);
        return;
    }

    console.log("JSON:", data);

    if(data.sucesso){

        document.getElementById("form_restante").style.display = "block";

        document.getElementById("cpf").readOnly = true;
        document.getElementById("inscricao").readOnly = true;
        document.getElementById("nome_completo").readOnly = true;
        document.getElementById("crn_id").disabled = true;

        document.getElementById("btnValidar").disabled = true;
        document.getElementById("btnValidar").innerText = "Validado ✅";

    }else{

        alert("Profissional não encontrado");

    }

})

.catch(error => {
    console.error("ERRO:", error);
});

});

});

</script>

<!-- SCRIPT TERMO MODAL -->

<script>

document.addEventListener("DOMContentLoaded", function(){

let modal = new bootstrap.Modal(document.getElementById('termoModal'))

modal.show()

let checkbox = document.getElementById("aceiteTermo")
let botao = document.getElementById("btnContinuar")
let form = document.getElementById("form_submit")
let enviar = document.getElementById("save_button")

enviar.disabled = true

checkbox.addEventListener("change", function(){

botao.disabled = !this.checked

})

botao.addEventListener("click", function(){

modal.hide()

enviar.disabled = false

})

form.addEventListener("submit", function(e){

if(!checkbox.checked){

alert("Você precisa aceitar o termo antes de enviar.")
e.preventDefault()

}

})

})

</script>

</body>
</html>