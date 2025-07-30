<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editor de Certificados</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; display: flex; height: 100vh; background: #f3f4f6; }

    /* ==== SIDEBAR ==== */
    .sidebar { width: 80px; background: #fff; display: flex; flex-direction: column; align-items: center; padding: 20px 0; box-shadow: 2px 0 6px rgba(0,0,0,0.1); z-index: 10; }
    .sidebar img { width: 32px; height: 32px; margin: 20px 0; cursor: pointer; transition: transform 0.2s; }
    .sidebar img:hover { transform: scale(1.1); }
    .sidebar img.logo { width: 40px; height: 40px; margin-bottom: 40px; }

    /* ==== MAIN ==== */
    .main { flex: 1; display: flex; flex-direction: row; overflow: hidden; }

    /* ==== LATERAL ESQUERDA ==== */
    .form-column { width: 300px; background: #fff; padding: 20px; border-right: 1px solid #ddd; overflow-y: auto; transition: width 0.3s; }
    .form-column.collapsed { width: 60px; padding: 10px; overflow: hidden; }
    .form-column.collapsed > * { display: none; }
    .form-column.collapsed .toggle-sidebar { display: block; margin: 10px auto; }
    h1 { font-size: 20px; margin-bottom: 15px; }
    label { display: block; margin-top: 15px; font-size: 14px; color: #4b5563; }
    select, input[type="color"], input[type="file"], input[type="number"], textarea { 
      width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #d1d5db; border-radius: 6px;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    select:focus, input:focus, textarea:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    /* Upload area */
    .upload-area {
      border: 2px dashed #d1d5db;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      margin-bottom: 15px;
      transition: background 0.2s;
    }
    .upload-area:hover {
      background: #f9fafb;
      border-color: #2563eb;
    }
    .upload-area.active {
      background: #eff6ff;
      border-color: #2563eb;
    }

    /* Colunas do Excel */
    .mapping-columns { 
      display: flex; 
      flex-wrap: wrap; 
      gap: 8px; 
      margin-top: 10px; 
      background: #f8f8f8; 
      padding: 12px; 
      border-radius: 8px; 
      min-height: 80px;
      max-height: 200px;
      overflow-y: auto;
    }
    .column-item { 
      padding: 6px 12px; 
      background: #e5e7eb; 
      border-radius: 6px; 
      cursor: grab; 
      font-size: 13px; 
      transition: all 0.2s;
      user-select: none;
    }
    .column-item:hover {
      background: #d1d5db;
      transform: translateY(-2px);
    }
    .column-item:active {
      cursor: grabbing;
    }

    /* ==== PREVIEW ==== */
    .preview-column { 
      flex: 1; 
      display: flex; 
      flex-direction: column; 
      padding: 20px;
      overflow: auto;
      background: #f3f4f6;
    }
    
    .preview-wrapper {
      width: 800px;
      margin: 0 auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .preview-container { 
      position: relative; 
      width: 100%; 
      height: 565px; 
      border: 1px solid #e5e7eb; 
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center; 
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      background-color: white;
      transition: all 0.3s;
    }
    .preview-container.drop-active {
      border: 2px dashed #2563eb;
      background-color: #f0f7ff;
    }
    
    .draggable { 
      position: absolute; 
      padding: 6px 12px; 
      cursor: move; 
      border: 1px dashed transparent; 
      user-select: none; 
      max-width: 80%;
      transition: all 0.2s;
    }
    .draggable.selected { 
      border: 1px dashed #2563eb; 
      background: rgba(37,99,235,0.05); 
      box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }
    .draggable:hover {
      transform: scale(1.02);
    }

    /* Controles de estilo */
    .style-controls { 
      margin-top: 20px; 
      background: #f9fafb;
      padding: 15px;
      border-radius: 8px;
    }
    .style-row { 
      display: flex; 
      gap: 15px; 
      margin-bottom: 10px; 
    }
    .style-group { 
      flex: 1; 
    }
    .controls { 
      display: flex; 
      gap: 10px; 
      margin-top: 15px; 
    }
    button { 
      background: #2563eb; 
      color: #fff; 
      border: none; 
      padding: 10px 15px; 
      border-radius: 6px; 
      cursor: pointer; 
      font-weight: 600;
      flex: 1;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    button:hover { 
      background: #1d4ed8; 
      transform: translateY(-1px);
    }
    button:active {
      transform: translateY(0);
    }
    button.secondary {
      background: #f3f4f6;
      color: #4b5563;
      border: 1px solid #d1d5db;
    }
    button.secondary:hover {
      background: #e5e7eb;
    }
    button.danger {
      background: #dc2626;
    }
    button.danger:hover {
      background: #b91c1c;
    }

    /* Text toolbar */
    .text-toolbar {
      display: flex;
      gap: 5px;
      margin-bottom: 8px;
    }
    .text-toolbar button {
      flex: none;
      width: 32px;
      height: 32px;
      padding: 0;
      border-radius: 4px;
    }

    /* Toast notifications */
    .toast {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: 12px 20px;
      background: #10b981;
      color: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      animation: slideIn 0.3s ease-out;
      z-index: 100;
    }
    .toast.error {
      background: #ef4444;
    }
    .toast.warning {
      background: #f59e0b;
    }

    @keyframes slideIn {
      from { transform: translateY(100px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    /* Toggle sidebar button */
    .toggle-sidebar {
      background: none;
      border: none;
      cursor: pointer;
      padding: 5px;
      display: none;
    }
    .toggle-sidebar svg {
      width: 24px;
      height: 24px;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
      .preview-wrapper {
        width: 100%;
      }
      .form-column {
        width: 250px;
      }
    }
    @media (max-width: 992px) {
      .main {
        flex-direction: column;
      }
      .form-column {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #ddd;
      }
      .preview-column {
        padding: 15px;
      }
    }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <div class="sidebar">
    <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" class="logo" alt="Logo">
    <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" title="Gerador">
    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" title="Relatórios">
    <img src="https://cdn-icons-png.flaticon.com/512/992/992680.png" title="Sair">
  </div>

  <!-- MAIN -->
  <div class="main">
    <!-- LATERAL ESQUERDA -->
    <div class="form-column" id="form-column">
      <button class="toggle-sidebar" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
      </button>
      
      <h1>Editor de Certificado</h1>
      
      <label>Upload do Excel:</label>
      <div class="upload-area" id="upload-area">
        <p>Arraste seu arquivo Excel aqui ou</p>
        <input type="file" id="excel-upload" accept=".xls,.xlsx" style="display: none;" />
        <button class="secondary" onclick="document.getElementById('excel-upload').click()">Selecionar Arquivo</button>
        <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">Formatos suportados: .xls, .xlsx</p>
      </div>

      <label>Campos do Excel (arraste para o certificado):</label>
      <div id="excel-columns" class="mapping-columns">
        <p style="color:#6b7280;font-size:12px;text-align:center;width:100%;">Faça upload do Excel para carregar as colunas...</p>
      </div>
      
      <div style="margin-top: 20px;">
        <label for="template">Selecione Template:</label>
        <select id="template">
          <option value="/storage/templates/template_certificado_1.jpg">Graduação Odontologia</option>
          <option value="/storage/templates/template_certificado_2.jpg">Pós-Odontologia</option>
          <option value="/storage/templates/template_certificado_3.jpg">SLMandic</option>
        </select>
      </div>
    </div>

    <!-- PREVIEW + CONTROLES -->
    <div class="preview-column">
      <div class="preview-wrapper">
        <!-- Controles de estilo -->
        <div class="style-controls">
          <div class="style-row">
            <div class="style-group">
              <label>Fontes:</label>
              <select id="font-selector">
                <option value="Arial">Arial</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="Georgia">Georgia</option>
                <option value="Verdana">Verdana</option>
                <option value="Courier New">Courier New</option>
              </select>
            </div>
            
            <div class="style-group">
              <label>Tamanho da Fonte:</label>
              <input type="number" id="font-size" min="8" max="100" value="24">
            </div>
            
            <div class="style-group">
              <label>Cor do Texto:</label>
              <input type="color" id="font-color" value="#000000">
            </div>
          </div>
          
          <div class="controls">
            <button id="auto-position" title="Reposiciona os campos automaticamente">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
              Auto-Posicionar
            </button>
            <button id="delete-field" class="danger" title="Remove o campo selecionado">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
              Remover
            </button>
            <button id="save-layout" class="secondary" title="Salva o layout atual">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              Salvar
            </button>
          </div>
        </div>

        <!-- Editor de texto -->
        <div style="margin-top: 20px;">
          <label for="descricao-certificado">Texto do Certificado:</label>
          <div class="text-toolbar">
            <button data-command="bold" title="Negrito"><b>B</b></button>
            <button data-command="italic" title="Itálico"><i>I</i></button>
            <button data-command="underline" title="Sublinhado"><u>S</u></button>
          </div>
          <textarea id="descricao-certificado" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;">
Certificamos que [NOME] concluiu o curso de [CURSO], com carga horária de [CARGA HORARIA], na [UNIDADE].
          </textarea>
          <button id="add-descricao" style="margin-top: 10px; width:100%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Adicionar/Atualizar Texto
          </button>
        </div>

        <!-- Preview do certificado -->
        <div class="preview-container" id="preview"></div>
      </div>
    </div>
  </div>

  <script>
  // Estado global da aplicação
  const state = {
    mappedFields: {},
    selectedElement: null,
    currentTemplate: null,
    fieldElements: {},
    excelData: null
  };

  // Elementos DOM
  const templateSelect = document.getElementById('template');
  const preview = document.getElementById('preview');
  const fontSelector = document.getElementById('font-selector');
  const fontSizeInput = document.getElementById('font-size');
  const fontColorInput = document.getElementById('font-color');
  const deleteFieldBtn = document.getElementById('delete-field');
  const excelUpload = document.getElementById('excel-upload');
  const excelColumns = document.getElementById('excel-columns');
  const uploadArea = document.getElementById('upload-area');
  const autoPositionBtn = document.getElementById('auto-position');
  const addDescricaoBtn = document.getElementById('add-descricao');
  const descricaoTextarea = document.getElementById('descricao-certificado');

  // Posições pré-definidas para os campos
  const fieldPositions = {
    nome: { x: '50%', y: '40%', align: 'center', fontSize: 36 },
    curso: { x: '50%', y: '50%', align: 'center', fontSize: 24 },
    'carga horaria': { x: '30%', y: '70%', align: 'left', fontSize: 18 },
    'data conclusao': { x: '70%', y: '70%', align: 'right', fontSize: 18 },
    unidade: { x: '30%', y: '80%', align: 'left', fontSize: 18 },
    cpf: { x: '70%', y: '80%', align: 'right', fontSize: 18 }
  };

  // Inicialização
  document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    atualizarPreview();
    setupDragAndDrop();
    setupTextEditor();
  });

  // Configura todos os event listeners
  function setupEventListeners() {
    templateSelect.addEventListener('change', atualizarPreview);
    fontSelector.addEventListener('change', updateSelectedElementStyle);
    fontSizeInput.addEventListener('input', debounce(updateSelectedElementStyle, 50));
    fontColorInput.addEventListener('input', debounce(updateSelectedElementStyle, 50));
    deleteFieldBtn.addEventListener('click', deleteSelectedElement);
    autoPositionBtn.addEventListener('click', autoPositionFields);
    addDescricaoBtn.addEventListener('click', () => addDescricaoTexto());
    
    // Upload de arquivo
    excelUpload.addEventListener('change', handleFileUpload);
    
    // Drag and drop para upload
    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.classList.add('active');
    });
    
    uploadArea.addEventListener('dragleave', () => {
      uploadArea.classList.remove('active');
    });
    
    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.classList.remove('active');
      if (e.dataTransfer.files.length) {
        excelUpload.files = e.dataTransfer.files;
        handleFileUpload();
      }
    });
    
    // Seleção de elementos no preview
    preview.addEventListener('click', handlePreviewClick);
  }

  // Atualiza o template de fundo
  function atualizarPreview() {
    state.currentTemplate = templateSelect.value;
    preview.style.backgroundImage = `url('${state.currentTemplate}')`;
    showToast('Template atualizado com sucesso');
  }

  // Manipulação do upload do Excel
  async function handleFileUpload() {
    const file = excelUpload.files[0];
    if (!file) return;

    showLoading();
    excelColumns.innerHTML = '<p style="color:#6b7280;font-size:12px;text-align:center;">Carregando colunas...</p>';

    try {
      const formData = new FormData();
      formData.append('file', file);
      
      const response = await fetch('/certificados/ler-colunas', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
      });

      const data = await response.json();
      
      if (data.status === 'success' && data.colunas?.length) {
        state.excelData = data;
        state.mappedFields = mapExcelFields(data.colunas);
        renderExcelColumns(data.colunas);
        placeFieldsOnCertificate(state.mappedFields);
        showToast('Arquivo carregado com sucesso!');
      } else {
        excelColumns.innerHTML = '<p style="color:#ef4444;font-size:12px;text-align:center;">Nenhuma coluna encontrada no arquivo.</p>';
        showToast('Nenhuma coluna encontrada no arquivo', 'error');
      }
    } catch (err) {
      console.error(err);
      excelColumns.innerHTML = '<p style="color:#ef4444;font-size:12px;text-align:center;">Erro ao processar o arquivo.</p>';
      showToast('Erro ao processar o arquivo', 'error');
    } finally {
      hideLoading();
    }
  }

  // Mapeia automaticamente os campos do Excel
  function mapExcelFields(columns) {
    const mapped = {};
    columns.forEach(col => {
      const key = col.toLowerCase().trim();
      if (key.includes('nome')) mapped[col] = 'nome';
      else if (key.includes('curso')) mapped[col] = 'curso';
      else if (key.includes('carga')) mapped[col] = 'carga horaria';
      else if (key.includes('data')) mapped[col] = 'data conclusao';
      else if (key.includes('unidade')) mapped[col] = 'unidade';
      else if (key.includes('cpf')) mapped[col] = 'cpf';
    });
    return mapped;
  }

  // Renderiza as colunas do Excel
  function renderExcelColumns(columns) {
    const fragment = document.createDocumentFragment();
    
    columns.forEach(col => {
      const div = document.createElement('div');
      div.className = 'column-item';
      div.textContent = col;
      div.draggable = true;
      div.dataset.columnName = col;
      div.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', col);
        e.dataTransfer.setData('application/json', JSON.stringify({
          type: 'excel-field',
          name: col
        }));
      });
      fragment.appendChild(div);
    });
    
    excelColumns.innerHTML = '';
    excelColumns.appendChild(fragment);
  }

  // Posiciona os campos no certificado
  function placeFieldsOnCertificate(mappedFields) {
    // Remove apenas campos de Excel existentes
    preview.querySelectorAll('.draggable[data-field-type="excel-field"]').forEach(el => el.remove());
    
    // Adiciona os novos campos
    Object.keys(mappedFields).forEach(fieldName => {
      const layoutKey = mappedFields[fieldName];
      const position = fieldPositions[layoutKey];
      
      if (position) {
        const fieldElement = createFieldElement(fieldName, true);
        applyPositionStyles(fieldElement, position);
        preview.appendChild(fieldElement);
        state.fieldElements[layoutKey] = fieldElement;
      }
    });
  }

  // Cria um novo elemento de campo
  function createFieldElement(fieldName, isExcelField = false) {
    const el = document.createElement('div');
    el.className = 'draggable';
    el.textContent = isExcelField ? `*${fieldName}` : fieldName;
    el.dataset.fieldType = isExcelField ? 'excel-field' : 'text-field';
    
    if (isExcelField) {
      el.dataset.sourceField = fieldName;
      el.dataset.mappedField = state.mappedFields[fieldName];
    }
    
    makeDraggable(el);
    return el;
  }

  // Aplica estilos de posição
  function applyPositionStyles(element, position) {
    element.style.left = position.x;
    element.style.top = position.y;
    element.style.transform = 'translate(-50%, -50%)';
    element.style.fontSize = position.fontSize + 'px';
    element.style.fontFamily = fontSelector.value;
    element.style.color = fontColorInput.value;
    element.style.textAlign = position.align;
  }

  // Configura drag and drop
  function setupDragAndDrop() {
    preview.addEventListener('dragover', e => {
      e.preventDefault();
      preview.classList.add('drop-active');
    });
    
    preview.addEventListener('dragleave', () => {
      preview.classList.remove('drop-active');
    });
    
    preview.addEventListener('drop', e => {
      e.preventDefault();
      preview.classList.remove('drop-active');
      
      try {
        const data = JSON.parse(e.dataTransfer.getData('application/json'));
        
        if (data.type === 'excel-field') {
          const fieldName = data.name;
          const div = createFieldElement(fieldName, true);
          div.style.top = `${e.offsetY}px`;
          div.style.left = `${e.offsetX}px`;
          div.style.fontFamily = fontSelector.value;
          div.style.fontSize = `${fontSizeInput.value}px`;
          div.style.color = fontColorInput.value;
          preview.appendChild(div);
        }
      } catch {
        // Caso não seja um campo do Excel, trata como texto livre
        const text = e.dataTransfer.getData('text/plain');
        if (text) {
          const div = createFieldElement(text);
          div.style.top = `${e.offsetY}px`;
          div.style.left = `${e.offsetX}px`;
          div.style.fontFamily = fontSelector.value;
          div.style.fontSize = `${fontSizeInput.value}px`;
          div.style.color = fontColorInput.value;
          preview.appendChild(div);
        }
      }
    });
  }

  // Torna um elemento arrastável
  function makeDraggable(el) {
    let offsetX, offsetY, isDragging = false;
    
    el.addEventListener('mousedown', e => {
      if (e.button !== 0) return; // Apenas botão esquerdo
      
      // Seleciona o elemento
      if (state.selectedElement) state.selectedElement.classList.remove('selected');
      state.selectedElement = el;
      el.classList.add('selected');
      
      // Prepara para arrastar
      offsetX = e.offsetX;
      offsetY = e.offsetY;
      isDragging = false;
      
      function mouseMoveHandler(ev) {
        isDragging = true;
        el.style.left = `${ev.pageX - preview.getBoundingClientRect().left - offsetX}px`;
        el.style.top = `${ev.pageY - preview.getBoundingClientRect().top - offsetY}px`;
        el.style.transform = 'none';
      }
      
      function mouseUpHandler() {
        document.removeEventListener('mousemove', mouseMoveHandler);
        document.removeEventListener('mouseup', mouseUpHandler);
        
        if (!isDragging) {
          // Foi apenas um clique, não um arraste
          updateStyleControls();
        }
      }
      
      document.addEventListener('mousemove', mouseMoveHandler);
      document.addEventListener('mouseup', mouseUpHandler);
    });
  }

  // Manipulação de cliques no preview
  function handlePreviewClick(e) {
    if (e.target.classList.contains('draggable')) {
      if (state.selectedElement) state.selectedElement.classList.remove('selected');
      state.selectedElement = e.target;
      state.selectedElement.classList.add('selected');
      updateStyleControls();
    } else {
      if (state.selectedElement) state.selectedElement.classList.remove('selected');
      state.selectedElement = null;
    }
  }

  // Atualiza os controles de estilo com base no elemento selecionado
  function updateStyleControls() {
    if (!state.selectedElement) return;
    
    const style = state.selectedElement.style;
    fontSelector.value = style.fontFamily || 'Arial';
    fontSizeInput.value = parseInt(style.fontSize) || 24;
    
    // Converte cor RGB para HEX se necessário
    if (style.color && style.color.startsWith('rgb')) {
      fontColorInput.value = rgbToHex(style.color);
    } else {
      fontColorInput.value = style.color || '#000000';
    }
  }

  // Atualiza o estilo do elemento selecionado
  function updateSelectedElementStyle() {
    if (state.selectedElement) {
      state.selectedElement.style.fontFamily = fontSelector.value;
      state.selectedElement.style.fontSize = `${fontSizeInput.value}px`;
      state.selectedElement.style.color = fontColorInput.value;
    }
  }

  // Remove o elemento selecionado
  function deleteSelectedElement() {
    if (state.selectedElement) {
      state.selectedElement.remove();
      state.selectedElement = null;
      showToast('Campo removido');
    }
  }

  // Reposiciona os campos automaticamente
  function autoPositionFields() {
    if (!state.excelData) {
      showToast('Faça o upload do Excel primeiro!', 'warning');
      return;
    }
    
    placeFieldsOnCertificate(state.mappedFields);
    addDescricaoTexto(true);
    showToast('Campos reposicionados automaticamente');
  }

  // Adiciona/atualiza o texto descritivo
  function addDescricaoTexto(reposicionar = false) {
    const textValue = descricaoTextarea.value;
    let desc = preview.querySelector('#descricao-cert');
    
    if (!desc) {
      desc = createFieldElement('', false);
      desc.id = 'descricao-cert';
      preview.appendChild(desc);
    }
    
    // Atualiza conteúdo e estilo
    desc.innerHTML = textValue.replace(/\n/g, '<br>');
    desc.style.position = 'absolute';
    desc.style.left = reposicionar ? '50%' : (desc.style.left || '50%');
    desc.style.top = reposicionar ? '60%' : (desc.style.top || '60%');
    desc.style.transform = 'translate(-50%, -50%)';
    desc.style.fontSize = `${fontSizeInput.value}px`;
    desc.style.fontFamily = fontSelector.value;
    desc.style.color = fontColorInput.value;
    desc.style.textAlign = 'center';
    desc.style.width = '80%';
    desc.style.whiteSpace = 'pre-wrap';
    
    if (!reposicionar) {
      showToast('Texto do certificado atualizado');
    }
  }

  // Configura o editor de texto
  function setupTextEditor() {
    const toolbar = document.createElement('div');
    toolbar.className = 'text-toolbar';
    toolbar.innerHTML = `
      <button data-command="bold" title="Negrito"><b>B</b></button>
      <button data-command="italic" title="Itálico"><i>I</i></button>
      <button data-command="underline" title="Sublinhado"><u>S</u></button>
    `;
    descricaoTextarea.parentNode.insertBefore(toolbar, descricaoTextarea);
    
    toolbar.addEventListener('click', e => {
      const command = e.target.closest('button')?.dataset.command;
      if (command) {
        document.execCommand(command, false, null);
        descricaoTextarea.focus();
      }
    });
  }

  // Converte RGB para HEX
  function rgbToHex(rgb) {
    if (!rgb) return '#000000';
    
    // Extrai os valores RGB
    const result = /^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*\d+\.*\d*)?\)$/.exec(rgb);
    if (!result) return '#000000';
    
    const r = parseInt(result[1]);
    const g = parseInt(result[2]);
    const b = parseInt(result[3]);
    
    return "#" + [r, g, b].map(x => {
      const hex = x.toString(16);
      return hex.length === 1 ? '0' + hex : hex;
    }).join('');
  }

  // Mostra notificação
  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Mostra indicador de carregamento
  function showLoading() {
    uploadArea.innerHTML = '<p style="color:#2563eb;">Processando arquivo...</p>';
  }

  // Esconde indicador de carregamento
  function hideLoading() {
    uploadArea.innerHTML = `
      <p>Arquivo carregado: ${excelUpload.files[0].name}</p>
      <button class="secondary" onclick="document.getElementById('excel-upload').click()">Trocar Arquivo</button>
    `;
  }

  // Debounce para otimizar eventos
  function debounce(func, timeout = 100) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
  }

  // Alterna a barra lateral
  function toggleSidebar() {
    document.getElementById('form-column').classList.toggle('collapsed');
  }
  </script>
</body>
</html>