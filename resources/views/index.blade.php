<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerador de Certificados</title>
  <style>
    /* ===== RESET ===== */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      background: #f3f4f6;
      height: 100vh;
      display: flex;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: 80px;
      background: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      box-shadow: 2px 0 6px rgba(0,0,0,0.1);
    }
    .sidebar img { width: 32px; height: 32px; margin: 20px 0; cursor: pointer; }
    .sidebar img.logo { width: 40px; height: 40px; margin-bottom: 40px; }

    /* ===== MAIN ===== */
    .main { flex: 1; display: flex; }

    /* ===== FORM COLUMN ===== */
    .form-column {
      width: 40%;
      background: #fff;
      padding: 30px;
      border-right: 1px solid #ddd;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }
    .form-column h1 { font-size: 22px; margin-bottom: 20px; }
    label { display: block; margin-top: 15px; font-size: 14px; }
    select, .file-label, button {
      width: 100%; margin-top: 8px; padding: 10px;
      border: 1px solid #ccc; border-radius: 6px; font-size: 14px;
    }
    .file-label { background: #f9f9f9; color: #555; cursor: pointer; text-align: center; }
    input[type="checkbox"] { margin-right: 8px; }
    button {
      background: #2563eb; color: #fff; font-weight: bold;
      cursor: pointer; margin-top: 20px; transition: background 0.2s;
    }
    button:hover { background: #1d4ed8; }
    button:disabled { background: #9ca3af; cursor: not-allowed; }

    /* ===== LOADING ===== */
    .loading { display: flex; align-items: center; margin-top: 15px; color: #444; }
    .spinner {
      width: 20px; height: 20px; border: 3px solid #ccc; border-top: 3px solid #2563eb;
      border-radius: 50%; margin-right: 8px; animation: spin 1s linear infinite;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* ===== PREVIEW COLUMN ===== */
    .preview-column {
      flex: 1; background: #f8f9fb; padding: 30px;
      display: flex; flex-direction: column; align-items: center;
    }
    .preview-column h2 { margin-bottom: 15px; }
    .preview-box {
      background: #fff; border: 1px solid #ddd; border-radius: 6px;
      padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .preview-pdf { width: 100%; height: 600px; border: none; }

    /* ===== NOTIFICATION ===== */
    .notification-container {
      position: fixed; top: 10px; right: 10px;
      background: #fff; padding: 15px 20px;
      border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: none; z-index: 1000;
      border-left: 4px solid #2563eb;
    }
    .notification-container.error { border-left-color: #dc2626; }
    .notification-container.success { border-left-color: #16a34a; }

    /* ===== MAPEAMENTO ===== */
    .mapping-section { 
      margin-top: 20px; 
      display: none; /* Inicialmente escondido */
    }
    .mapping-columns { 
      display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;
      max-height: 150px; overflow-y: auto; padding: 5px;
    }
    .column-item {
      padding: 8px 12px; background: #f3f4f6; border: 1px solid #ccc; border-radius: 4px;
      cursor: grab; font-size: 13px; user-select: none;
    }
    .mapping-targets { margin-top: 15px; }
    .target-box {
      border: 2px dashed #bbb; padding: 10px; height: 40px; border-radius: 6px;
      margin-bottom: 10px; display: flex; align-items: center; justify-content: center;
      font-size: 13px; color: #777; position: relative;
    }
    .target-box.filled { 
      border-style: solid; background: #e6f7ff; color: #333;
      justify-content: space-between;
    }
    .target-box .remove-mapping {
      background: #fca5a5; color: #b91c1c; border: none;
      width: 20px; height: 20px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 10px;
    }

    /* ===== UPLOAD SECTION ===== */
    .upload-section {
      transition: all 0.3s ease;
    }

    /* ===== RESULTS MODAL ===== */
    .modal-overlay {
      position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5); display: none;
      justify-content: center; align-items: center; z-index: 999;
    }
    .modal-content {
      background: white; padding: 20px; border-radius: 8px;
      width: 80%; max-width: 600px; max-height: 80vh;
      overflow-y: auto;
    }
    .modal-close {
      float: right; cursor: pointer; font-size: 20px;
    }
    .error-item {
      padding: 8px; border-bottom: 1px solid #eee;
      font-size: 13px; color: #dc2626;
    }
  </style>
</head>
<body>

  <!-- NOTIFICAÇÕES -->
  <div id="notification-container" class="notification-container">
    <div id="notification-content"></div>
  </div>

  <!-- MODAL DE RESULTADOS -->
  <div class="modal-overlay" id="results-modal">
    <div class="modal-content">
      <span class="modal-close" onclick="closeModal()">×</span>
      <h3>Resultado do Processamento</h3>
      <div id="results-content"></div>
    </div>
  </div>

  <div class="sidebar">
    <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" class="logo" alt="Logo">
    <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" title="Gerador">
    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" title="Relatórios">
    <img src="https://cdn-icons-png.flaticon.com/512/992/992680.png" title="Sair" onclick="alert('Logout');">
  </div>

  <div class="main">
    <!-- FORMULÁRIO -->
    <div class="form-column">
      <h1>Gerador de Certificados</h1>
      <form id="certificate-form" enctype="multipart/form-data">
        <!-- ÁREA DE UPLOAD (visível inicialmente) -->
        <div class="upload-section" id="upload-section">
          <label for="file-label">Selecione o arquivo Excel:</label>
          <label for="file" class="file-label"><span id="file-name">Selecione um arquivo...</span></label>
          <input type="file" name="file" id="file" accept=".xls,.xlsx" style="display:none;" required>
        </div>

        <!-- ÁREA DE MAPEAMENTO (inicialmente escondida) -->
        <div class="mapping-section" id="mapping-section">
          <h3>Mapeie as Colunas:</h3>
          <div id="excel-columns" class="mapping-columns"></div>
          <div class="mapping-targets">
            <div class="target-box" data-field="nome">Arraste aqui: Nome</div>
            <div class="target-box" data-field="email">Arraste aqui: Email</div>
            <div class="target-box" data-field="curso">Arraste aqui: Curso</div>
            <div class="target-box" data-field="carga_horaria">Arraste aqui: Carga Horária</div>
            <div class="target-box" data-field="data_conclusao">Arraste aqui: Data Conclusão</div>
            <div class="target-box" data-field="unidade">Arraste aqui: Unidade</div>
            <div class="target-box" data-field="cpf">Arraste aqui: CPF</div>
          </div>
        </div>

        <label for="template">Escolha o modelo de certificado:</label>
        <select name="template" id="template" required>
          <option value="template1.pdf">Graduação Odontologia</option>
          <option value="template2.pdf">Pós-Odontologia</option>
          <option value="template3.pdf">SLMandic</option>
        </select>

        <label><input type="checkbox" id="enviar_email" name="enviar_email" value="1">Enviar Certificado por e-mail aos participantes</label>
        <button type="submit" id="submit-btn">Gerar e Enviar Certificados</button>

        <div id="loading" class="loading" style="display:none;">
          <div class="spinner"></div> Processando arquivo...
        </div>
      </form>
    </div>

    <!-- PREVIEW -->
    <div class="preview-column">
      <h2>Pré-visualização do Certificado</h2>
      <div class="preview-box">
        <iframe id="preview-pdf" class="preview-pdf" src="about:blank"></iframe>
      </div>
    </div>
  </div>

  <script>
    // Elementos DOM
    const fileInput = document.getElementById('file');
    const fileName = document.getElementById('file-name');
    const excelColumnsContainer = document.getElementById('excel-columns');
    const uploadSection = document.getElementById('upload-section');
    const mappingSection = document.getElementById('mapping-section');
    const form = document.getElementById('certificate-form');
    const submitBtn = document.getElementById('submit-btn');
    const loadingIndicator = document.getElementById('loading');
    const previewIframe = document.getElementById('preview-pdf');
    const templateSelect = document.getElementById('template');

    // Variáveis de estado
    let columnMap = {};
    let excelData = null;

    // Event Listeners
    fileInput.addEventListener('change', handleFileUpload);
    form.addEventListener('submit', enviarFormulario);
    templateSelect.addEventListener('change', atualizarPreview);

    // Função para mostrar notificação
    function showNotification(message, type = 'info') {
      const notification = document.getElementById('notification-container');
      const content = document.getElementById('notification-content');
      
      content.textContent = message;
      notification.className = `notification-container ${type}`;
      notification.style.display = 'block';
      
      setTimeout(() => {
        notification.style.display = 'none';
      }, 5000);
    }

    // Função para fechar modal
    function closeModal() {
      document.getElementById('results-modal').style.display = 'none';
    }

    // Função para mostrar resultados
    function showResults(data) {
      const modal = document.getElementById('results-modal');
      const content = document.getElementById('results-content');
      
      let html = `
        <p><strong>Total de certificados gerados:</strong> ${data.quantidadeCertificados}</p>
        <p><strong>Status:</strong> ${data.mensagem}</p>
      `;

      if (data.erros && data.erros.length > 0) {
        html += `<h4>Erros encontrados (${data.erros.length}):</h4>`;
        data.erros.forEach(error => {
          html += `
            <div class="error-item">
              <strong>Linha ${error.linha}:</strong> ${error.nome} - ${error.curso}<br>
              <em>${error.erro}</em>
            </div>
          `;
        });
      }

      content.innerHTML = html;
      modal.style.display = 'flex';
    }

    // Atualizar pré-visualização
  async function atualizarPreview() {
  // Verifica se o template foi carregado
  if (!templateSelect.value) return;

  // Dados de exemplo para preview
  const previewData = {
    nome: "Fulano de Tal",
    curso: "Curso de Exemplo",
    carga_horaria: "40 horas",
    data_conclusao: new Date().toLocaleDateString('pt-BR'),
    unidade: "Unidade Exemplo"
  };

  try {
    const formData = new FormData();
    formData.append('template', templateSelect.value);
    Object.entries(previewData).forEach(([key, value]) => {
      formData.append(key, value);
    });

    const response = await fetch('/preview-certificado', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json'
      },
      body: formData
    });

    if (!response.ok) {
      throw new Error('Erro no servidor');
    }

    const blob = await response.blob();
    const pdfUrl = URL.createObjectURL(blob);
    previewIframe.src = pdfUrl;

  } catch (error) {
    console.error('Erro ao carregar preview:', error);
    // Mostra mensagem mais amigável
    previewIframe.srcdoc = `
      <html><body style="display:flex;justify-content:center;align-items:center;height:100%;">
        <p style="color:#666;font-family:Arial">Pré-visualização indisponível</p>
      </body></html>
    `;
  }
}

    // Manipular upload de arquivo
   async function handleFileUpload() {
  try {
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);

    const response = await fetch('/certificados/ler-colunas', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    });

    // Verifica se a resposta é JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      throw new Error(`Resposta inesperada: ${text.substring(0, 100)}...`);
    }

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Erro no servidor');
    }

        if (data.status === 'success') {
          // Esconde upload e mostra mapeamento
          uploadSection.style.display = 'none';
          mappingSection.style.display = 'block';
          
          // Preenche colunas do Excel
          excelColumnsContainer.innerHTML = '';
          data.colunas.forEach(col => {
            const div = document.createElement('div');
            div.className = 'column-item';
            div.textContent = col;
            div.draggable = true;
            div.addEventListener('dragstart', e => {
              e.dataTransfer.setData('text/plain', col);
            });
            excelColumnsContainer.appendChild(div);
          });

          // Configura targets para drag and drop
          setupDragAndDrop();
          showNotification('Arquivo carregado com sucesso!', 'success');
        } else {
          throw new Error(data.mensagem || 'Erro ao ler arquivo');
        }
      } catch (error) {
    console.error('Erro detalhado:', error);
    showNotification(`Erro: ${error.message}`, 'error');
    
    // Mostra o erro completo no console
    if (error.response) {
      error.response.text().then(text => console.error('Resposta completa:', text));
    }
  }
}

    // Configurar drag and drop
    function setupDragAndDrop() {
      const targets = document.querySelectorAll('.target-box');
      
      targets.forEach(target => {
        // Limpa qualquer mapeamento existente
        target.classList.remove('filled');
        target.innerHTML = target.dataset.field.replace('_', ' ') + ' (arraste aqui)';
        columnMap = {};

        target.addEventListener('dragover', e => e.preventDefault());
        target.addEventListener('drop', e => {
          e.preventDefault();
          const colName = e.dataTransfer.getData('text/plain');
          const field = target.dataset.field;
          
          // Atualiza visual do target
          target.innerHTML = `
            ${colName}
            <button class="remove-mapping" onclick="removeMapping('${field}', this)">×</button>
          `;
          target.classList.add('filled');
          
          // Atualiza mapeamento
          columnMap[field] = colName;
          validateForm();
        });
      });
    }

    // Remover mapeamento
    window.removeMapping = function(field, button) {
      const target = button.parentElement;
      target.innerHTML = target.dataset.field.replace('_', ' ') + ' (arraste aqui)';
      target.classList.remove('filled');
      delete columnMap[field];
      validateForm();
    };

    // Validar formulário antes de enviar
    function validateForm() {
      const requiredFields = ['nome', 'curso', 'carga_horaria', 'data_conclusao', 'unidade', 'cpf'];
      const allMapped = requiredFields.every(field => columnMap[field]);
      submitBtn.disabled = !allMapped;
      return allMapped;
    }

    // Enviar formulário
    async function enviarFormulario(e) {
      e.preventDefault();
      
      if (!validateForm()) {
        showNotification('Por favor, mapeie todas as colunas necessárias', 'error');
        return;
      }

      submitBtn.disabled = true;
      loadingIndicator.style.display = 'flex';

      try {
        const formData = new FormData(form);
        formData.append('mapa', JSON.stringify(columnMap));

        const response = await fetch('/gerar-certificados', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
          showResults(data);
          showNotification(data.mensagem, 'success');
        } else {
          throw new Error(data.mensagem || 'Erro ao gerar certificados');
        }
      } catch (error) {
        console.error('Erro:', error);
        showNotification(error.message, 'error');
      } finally {
        loadingIndicator.style.display = 'none';
        submitBtn.disabled = false;
      }
    }

    // Inicializar preview
    atualizarPreview();
  </script>
</body>
</html>