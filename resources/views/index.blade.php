<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/build/assets/stylesNew.css">
  <title>GERADOR DE CERTIFICADOS</title>

  <script src="{{ asset('build/assets/scripts.js') }}"></script>
</head>
<body class="font-sans">

    <div id="notification-container" class="notification-container" style="display:none;">

    <div id="notification-content"></div>
  </div>

  <div id="error-container" class="notification-container" style="display:none;">
    <h3>Certificados com Erro</h3>
    <div id="error-content"></div>
</div>
  
  <div class="container">
    <div class="header">
      <h1 class="large-title">Gerador de Certificados</h1> 
      <img src="{{ asset('build/assets/img/Selo.png') }}" alt="Logo" class="logo">
    </div>
    <div class="content">

        <form onsubmit="enviarFormulario(event)" enctype="multipart/form-data">
        @csrf
        <label for="file-label">Selecione o arquivo Excel:</label>
        <label for="file" class="file-label">
          <span id="file-name">Selecione um arquivo...</span>
        </label>
        <input type="file" name="file" id="file" accept=".xls,.xlsx" style="display:none;" required onchange="updateFileName()">

        <label for="template">Escolha o modelo de certificado:</label>
        <select name="template" id="template" required>
          <option value="template_certificado_1.pdf">Graduação Odontologia</option>
          <option value="template_certificado_2.pdf">Pós-Odontologia</option>
          <option value="template_certificado_3.pdf">SLMandic</option>
        </select>
        <div class="form-group">
        <label for="enviar_email">  
        <input type="checkbox" id="enviar_email" name="enviar_email" value="1">
        Enviar Certificado por e-mail aos participantes
        </label>
        </div>
        <button type="submit"><b>Gerar e Enviar Certificados</b></button>

        
        <div id="loading" style="display:none;" class="loading">
          <div class="spinner"></div>
          Criando e Enviando...
        </div>

        <div class="message"></div>
      </form>
    </div>
  </div>

  <form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf
  </form>
  <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Sair
  </a>
  
  
  <!-- <script src="/build/assets/scripts.js"></script> -->
</body>
</html>
