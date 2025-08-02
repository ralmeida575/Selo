<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editor de Certificados</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      /* Cores */
      --primary-50: #eff6ff;
      --primary-100: #dbeafe;
      --primary-500: #3b82f6;
      --primary-600: #2563eb;
      --primary-700: #1d4ed8;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-900: #111827;
      --red-500: #ef4444;
      --red-600: #dc2626;
      --green-500: #10b981;
      --yellow-500: #f59e0b;
      
      /* Espaçamentos */
      --space-1: 4px;
      --space-2: 8px;
      --space-3: 12px;
      --space-4: 16px;
      --space-5: 20px;
      --space-6: 24px;
      --space-8: 32px;
      
      /* Bordas */
      --radius-sm: 4px;
      --radius-md: 6px;
      --radius-lg: 8px;
      --radius-xl: 12px;
      
      /* Sombras */
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--gray-900);
      background: var(--gray-100);
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

    /* ==== SIDEBAR ==== */
    .sidebar {
      width: 72px;
      background: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: var(--space-6) 0;
      border-right: 1px solid var(--gray-200);
      z-index: 10;
    }

    .sidebar img {
      width: 24px;
      height: 24px;
      margin: var(--space-5) 0;
      padding: var(--space-2);
      border-radius: var(--radius-md);
      cursor: pointer;
      transition: all 0.2s ease;
      filter: grayscale(100%) opacity(0.7);
    }

    .sidebar img:hover {
      filter: grayscale(0) opacity(1);
      background: var(--primary-50);
    }

    .sidebar img.logo {
      width: 32px;
      height: 32px;
      filter: none;
      margin-bottom: var(--space-8);
    }

    /* ==== MAIN ==== */
    .main {
      flex: 1;
      display: flex;
      flex-direction: row;
      overflow: hidden;
    }

    /* ==== LATERAL ESQUERDA ==== */
    .form-column {
      width: 300px;
      background: white;
      padding: var(--space-6);
      border-right: 1px solid var(--gray-200);
      overflow-y: auto;
      transition: width 0.3s;
    }

    .form-column.collapsed {
      width: 60px;
      padding: var(--space-3);
      overflow: hidden;
    }

    .form-column.collapsed > * {
      display: none;
    }

    .form-column.collapsed .toggle-sidebar {
      display: block;
      margin: var(--space-2) auto;
    }

    h1 {
      font-size: 18px;
      font-weight: 600;
      color: var(--gray-900);
      margin-bottom: var(--space-6);
      padding-bottom: var(--space-3);
      border-bottom: 1px solid var(--gray-200);
    }

    label {
      display: block;
      font-size: 13px;
      font-weight: 500;
      color: var(--gray-600);
      margin: var(--space-5) 0 var(--space-2);
    }

    select, input[type="color"], input[type="file"], input[type="number"], textarea {
      width: 100%;
      padding: var(--space-3);
      margin-top: var(--space-1);
      border: 1px solid var(--gray-300);
      border-radius: var(--radius-md);
      font-size: 13px;
      transition: all 0.2s;
      background: white;
    }

    select:focus, input:focus, textarea:focus {
      outline: none;
      border-color: var(--primary-500);
      box-shadow: 0 0 0 3px var(--primary-50);
    }

    /* Upload area */
    .upload-area {
      border: 2px dashed var(--gray-300);
      border-radius: var(--radius-xl);
      padding: var(--space-6);
      text-align: center;
      margin-bottom: var(--space-4);
      transition: all 0.2s;
      background: var(--gray-50);
      cursor: pointer;
    }

    .upload-area:hover {
      background: var(--primary-50);
      border-color: var(--primary-500);
    }

    .upload-area.active {
      background: var(--primary-50);
      border-color: var(--primary-600);
    }

    .upload-area p {
      font-size: 14px;
      color: var(--gray-600);
      margin-bottom: var(--space-3);
    }

    /* Colunas do Excel */
    .mapping-columns {
      display: flex;
      flex-direction: column;
      gap: var(--space-1);
      margin-top: var(--space-2);
      background: var(--gray-50);
      padding: var(--space-3);
      border-radius: var(--radius-lg);
      min-height: 80px;
      max-height: 300px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid var(--gray-200);
    }

    .column-item {
      width: 100%;
      padding: var(--space-2) var(--space-3);
      background: white;
      border-radius: var(--radius-md);
      cursor: grab;
      font-size: 12px;
      font-weight: 500;
      transition: all 0.2s;
      user-select: none;
      box-sizing: border-box;
      text-align: left;
      text-transform: uppercase;
      border-left: 3px solid var(--primary-600);
      color: var(--gray-700);
    }

    .column-item:hover {
      background: var(--gray-200);
      transform: translateY(-1px);
    }

    .column-item:active {
      cursor: grabbing;
    }

    /* ==== PREVIEW ==== */
    .preview-column {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: var(--space-6);
      overflow: auto;
      background: var(--gray-100);
    }

    .preview-wrapper {
      width: 800px;
      margin: 0 auto;
      background: white;
      padding: var(--space-6);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-md);
    }

    .preview-container {
      position: relative;
      width: 100%;
      height: 565px;
      border: 1px solid var(--gray-200);
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      background-color: white;
      transition: all 0.3s;
    }

    .preview-container.drop-active {
      border: 2px dashed var(--primary-600);
      background-color: var(--primary-50);
    }

    .draggable {
      position: absolute;
      padding: var(--space-1) var(--space-3);
      cursor: move;
      border: 1px dashed transparent;
      user-select: none;
      max-width: 80%;
      transition: all 0.2s;
      font-size: 14px;
    }

    .draggable.selected {
      border: 1px dashed var(--primary-600);
      background: rgba(59, 130, 246, 0.05);
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .draggable:hover {
      transform: scale(1.02);
    }

    /* === CONTROLES COMPACTOS === */
    .compact-toolbar {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      padding: var(--space-3);
      background: var(--gray-50);
      border-radius: var(--radius-lg);
      margin-bottom: var(--space-5);
      flex-wrap: wrap;
    }

    .font-control-group {
      display: flex;
      align-items: center;
      gap: var(--space-2);
    }

    .font-control-group label {
      font-size: 12px;
      color: var(--gray-600);
      font-weight: 500;
      white-space: nowrap;
      margin: 0;
    }

    #font-selector, #font-size {
      padding: var(--space-1);
      font-size: 12px;
      border: 1px solid var(--gray-300);
      border-radius: var(--radius-sm);
      min-width: 80px;
    }

    #font-size-range {
      -webkit-appearance: none;
      width: 80px;
      height: 4px;
      background: var(--gray-300);
      border-radius: 2px;
      outline: none;
    }

    #font-size-range::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 14px;
      height: 14px;
      background: var(--primary-600);
      border-radius: 50%;
      cursor: pointer;
    }

    #font-color {
      width: 24px;
      height: 24px;
      padding: 0;
      border: none;
      border-radius: 50%;
      cursor: pointer;
    }

    .text-format-buttons {
      display: flex;
      gap: var(--space-1);
    }

    .text-format-buttons button {
      width: 28px;
      height: 28px;
      padding: 0;
      font-size: 12px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--gray-300);
      background: white;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .text-format-buttons button:hover {
      background: var(--gray-100);
    }

    .compact-buttons {
      display: flex;
      gap: var(--space-2);
      margin-left: auto;
    }

    /* Botões principais */
    button {
      background: var(--primary-600);
      color: white;
      border: none;
      padding: var(--space-3) var(--space-4);
      border-radius: var(--radius-md);
      cursor: pointer;
      font-weight: 500;
      font-size: 13px;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--space-1);
    }

    button:hover {
      background: var(--primary-700);
      transform: translateY(-1px);
    }

    button:active {
      transform: translateY(0);
    }

    button.secondary {
      background: var(--gray-100);
      color: var(--gray-600);
      border: 1px solid var(--gray-300);
    }

    button.secondary:hover {
      background: var(--gray-200);
    }

    button.danger {
      background: var(--red-600);
    }

    button.danger:hover {
      background: #b91c1c;
    }

    .compact-button {
      padding: var(--space-1) var(--space-2);
      font-size: 12px;
      min-width: 70px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      border: 1px solid var(--gray-300);
      background: white;
      transition: all 0.2s;
    }

    .compact-button:hover {
      background: var(--gray-100);
    }

    .compact-button.secondary {
      background: var(--gray-100);
    }

    .compact-button.danger {
      background: #fee2e2;
      color: var(--red-600);
      border-color: #fca5a5;
    }

    .compact-button.danger:hover {
      background: #fecaca;
    }

    /* Editor de texto */
    #descricao-certificado {
      width: 100%;
      padding: var(--space-3);
      border: 1px solid var(--gray-300);
      border-radius: var(--radius-md);
      resize: vertical;
      min-height: 100px;
      font-size: 13px;
      line-height: 1.5;
      margin-top: var(--space-2);
    }

    /* Template thumbnails */
    .template-thumbnails {
      display: flex;
      flex-direction: column;
      gap: var(--space-2);
      margin-top: var(--space-3);
    }

    .template-thumb {
      width: 100%;
      height: 70px;
      background-size: cover;
      background-position: center;
      border: 2px solid transparent;
      border-radius: var(--radius-md);
      cursor: pointer;
      transition: all 0.2s;
    }

    .template-thumb.selected {
      border-color: var(--primary-600);
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    /* Toast notifications */
    .toast {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: var(--space-3) var(--space-5);
      background: var(--green-500);
      color: white;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      animation: slideIn 0.3s ease-out;
      z-index: 100;
      font-size: 13px;
    }

    .toast.error {
      background: var(--red-500);
    }

    .toast.warning {
      background: var(--yellow-500);
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
      padding: var(--space-1);
      display: none;
    }

    .toggle-sidebar svg {
      width: 24px;
      height: 24px;
      stroke: var(--gray-500);
    }

    /* Prompt sugestões */
    .prompt-sugestoes {
      display: flex;
      gap: var(--space-2);
      margin: var(--space-2) 0;
      flex-wrap: wrap;
    }

    .prompt-sugestoes button {
      padding: var(--space-1) var(--space-2);
      font-size: 12px;
      background: var(--gray-100);
      color: var(--gray-600);
      border-radius: var(--radius-sm);
      border: 1px solid var(--gray-300);
    }

    .prompt-sugestoes button:hover {
      background: var(--gray-200);
    }

    /* Chat histórico */
    #chat-historico {
      margin-top: var(--space-3);
      border: 1px solid var(--gray-200);
      border-radius: var(--radius-md);
      padding: var(--space-3);
      background: var(--gray-50);
      max-height: 200px;
      overflow-y: auto;
    }

    #chat-historico .mensagem-usuario {
      background-color: #e6f2ff;
      padding: var(--space-2);
      border-radius: var(--radius-sm);
      margin-bottom: var(--space-1);
      font-size: 13px;
    }

    #chat-historico .mensagem-ia {
      background-color: white;
      padding: var(--space-2);
      border-radius: var(--radius-sm);
      margin-bottom: var(--space-1);
      font-size: 13px;
      border: 1px solid var(--gray-200);
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
      .preview-wrapper {
        width: 100%;
      }
      .form-column {
        width: 280px;
      }
    }

    @media (max-width: 992px) {
      .main {
        flex-direction: column;
      }
      .form-column {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid var(--gray-200);
      }
      .preview-column {
        padding: var(--space-4);
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
        <p style="font-size: 12px; color: var(--gray-500); margin-top: var(--space-2);">Formatos suportados: .xls, .xlsx</p>
      </div>

      <label>Campos do Excel (arraste para o certificado):</label>
      <div id="excel-columns" class="mapping-columns">
        <p style="color: var(--gray-500); font-size: 12px; text-align: center; width: 100%;">Faça upload do Excel para carregar as colunas...</p>
      </div>
      
      <div style="margin-top: var(--space-5);">
        <label for="template">Selecione Template:</label>
        <select id="template">
          <option value="/storage/templates/template_certificado_1.jpg">Graduação Odontologia</option>
          <option value="/storage/templates/template_certificado_2.jpg">Pós-Odontologia</option>
          <option value="/storage/templates/template_certificado_3.jpg">SLMandic</option>
        </select>
        <div id="template-thumbnails" class="template-thumbnails"></div>
      </div>
    </div>

    <!-- PREVIEW + CONTROLES COMPACTOS -->
    <div class="preview-column">
      <div class="preview-wrapper">
        <!-- Nova barra de controles compacta -->
        <div class="compact-toolbar">
          <div class="font-control-group">
            <label for="font-selector">Fonte</label>
            <select id="font-selector">
              <option value="Work Sans">Work Sans</option>
              <option value="Roboto">Roboto</option>
              <option value="Arial">Arial</option>
            </select>
          </div>

          <div class="font-control-group">
            <label for="font-color">Cor</label>
            <input type="color" id="font-color" value="#000000">
          </div>

          <div class="font-control-group">
            <label for="font-size">Tamanho</label>
            <input type="range" id="font-size-range" min="8" max="52" value="24">
            <input type="number" id="font-size" min="8" max="52" value="24" style="width: 50px;">
          </div>
          
          <div class="text-format-buttons">
            <button data-command="bold" title="Negrito"><b>B</b></button>
            <button data-command="italic" title="Itálico"><i>I</i></button>
            <button data-command="underline" title="Sublinhado"><u>S</u></button>
          </div>
          
          <div class="compact-buttons">
            <button id="auto-position" class="compact-button" title="Auto-Posicionar">Posicionar</button>
            <button id="delete-field" class="compact-button danger" title="Remover">Remover</button>
            <button id="save-layout" class="compact-button secondary" title="Salvar">Salvar</button>
          </div>
        </div>

        <!-- Editor de texto -->
        <div style="margin-top: var(--space-5);">
          <label for="prompt">Personalizar texto do certificado:</label>
          <textarea id="prompt" placeholder="Digite aqui como quer que o certificado seja gerado..."></textarea>
          
          <div class="prompt-sugestoes">
            <button onclick="usarSugestao('Deixe o texto mais formal.')">+ Formal</button>
            <button onclick="usarSugestao('Adicione agradecimentos ao corpo docente.')">+ Agradecimento</button>
            <button onclick="usarSugestao('Resuma o texto em uma linha.')">+ Resumo</button>
          </div>
          
          <div style="display: flex; gap: var(--space-2); margin-top: var(--space-3);">
            <button onclick="refinarTexto()">Refinar com esse prompt</button>
            <button onclick="gerarTextoCertificado()">Gerar Texto</button>
            <button onclick="limparHistorico()" class="secondary">Novo Texto</button>
          </div>
          
          <div id="chat-historico"></div>
          
          <label for="descricao-certificado">Texto do Certificado:</label>
          <textarea id="descricao-certificado" rows="4">
CERTIFICAMOS, por meio deste, que Raphael concluiu con êxito o curso de Odontologia, cumprindo uma carga horária total de 200 horas. De referido curso foi realizado na unidade de Campinas, com a data de conclusão registrada em 20 de julho de 2025. Agradecemos ao corpo docente pela excelência na condução das atividades acadêmicas, cuja dedicação e comprometimento foram fundamentais para a formação do aluno.
          </textarea>
          
          <button id="add-descricao" style="margin-top: var(--space-3); width:100%;">
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
    renderTemplateThumbnails(); // 🔥 Chama a função que cria as miniaturas
});

function renderTemplateThumbnails() {
  const thumbnailsContainer = document.getElementById('template-thumbnails');
  const templateSelect = document.getElementById('template'); // Garantir que existe
  
  if (!thumbnailsContainer || !templateSelect) {
    console.error('Elementos não encontrados!');
    return;
  }

  thumbnailsContainer.innerHTML = '';

  Array.from(templateSelect.options).forEach(option => {
    const templatePath = option.value;
    const thumb = document.createElement('div');
    thumb.className = 'template-thumb';
    thumb.style.backgroundImage = `url('${templatePath}')`;
    thumb.title = option.text;

    // Tratamento de erro
    thumb.addEventListener('error', () => {
      thumb.style.backgroundImage = 'none';
      thumb.innerHTML = `<span>${option.text}</span>`;
      thumb.style.backgroundColor = '#f0f0f0';
    });

    if (templateSelect.value === option.value) {
      thumb.classList.add('selected');
    }

    thumb.addEventListener('click', () => {
      templateSelect.value = option.value;
      atualizarPreview();
      document.querySelectorAll('.template-thumb.selected').forEach(el => el.classList.remove('selected'));
      thumb.classList.add('selected');
    });

    thumbnailsContainer.appendChild(thumb);
  });
}

  // Configura todos os event listeners
  function setupEventListeners() {
    templateSelect.addEventListener('change', atualizarPreview);
    fontSelector.addEventListener('change', updateSelectedElementStyle);
    fontSizeInput.addEventListener('input', debounce(updateSelectedElementStyle, 50));
    fontColorInput.addEventListener('input', debounce(updateSelectedElementStyle, 50));
    deleteFieldBtn.addEventListener('click', deleteSelectedElement);
    autoPositionBtn.addEventListener('click', autoPositionFields);
    addDescricaoBtn.addEventListener('click', () => addDescricaoTexto());

    // 🔥 Sincronização entre o range e o input numérico de tamanho da fonte
  const fontSizeRange = document.getElementById('font-size-range');
  if (fontSizeRange) {
    fontSizeRange.addEventListener('input', () => {
      fontSizeInput.value = fontSizeRange.value;
      updateSelectedElementStyle(); // atualiza em tempo real
    });
    fontSizeInput.addEventListener('input', () => {
      fontSizeRange.value = fontSizeInput.value;
      updateSelectedElementStyle();
    });
  }
    
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
    
    const gerarTextoBtn = document.getElementById('gerar-texto-btn');
if (gerarTextoBtn) {
  gerarTextoBtn.addEventListener('click', gerarTextoCertificado);
}

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
    


// Agora estilizar cada botão individualmente:
const buttons = toolbar.querySelectorAll('button');
buttons.forEach(button => {
  button.style.all = 'unset'; // reseta estilos padrões do botão
  button.style.cursor = 'pointer';
  button.style.padding = '4px 7px';
  button.style.fontWeight = 'bold';
  button.style.fontSize = '14px';
  button.style.textAlign = 'center';
  button.style.borderRadius = '2px';
  button.style.userSelect = 'none';
  button.style.border = '1px solid transparent';
  button.style.transition = 'background-color 0.2s, border-color 0.2s';

  // Adicionar efeito hover e active usando eventos JS (porque :hover CSS não funciona aqui)
  button.addEventListener('mouseenter', () => {
    button.style.backgroundColor = '#ddd';
    button.style.borderColor = '#999';
  });
  button.addEventListener('mouseleave', () => {
    button.style.backgroundColor = 'transparent';
    button.style.borderColor = 'transparent';
  });
  button.addEventListener('mousedown', () => {
    button.style.backgroundColor = '#bbb';
    button.style.borderColor = '#666';
  });
  button.addEventListener('mouseup', () => {
    button.style.backgroundColor = '#ddd';
    button.style.borderColor = '#999';
  });
});

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

  function excelSerialToDate(serial) {
  const excelEpoch = new Date(1899, 11, 30);
  const date = new Date(excelEpoch.getTime() + serial * 86400000);
  return date.toISOString().split('T')[0]; // Exemplo: 2025-08-01
}

 async function gerarTextoCertificado() {
  if (!state.excelData || !state.excelData.dados || !state.excelData.dados.length) {
    console.error('Excel está vazio ou mal formatado');
    showToast("Nenhum dado de Excel encontrado!", "error");
    return;
  }

  const aluno = state.excelData.dados[0];
  console.log("aluno:", aluno);

  const promptExtra = document.querySelector("#prompt")?.value?.trim() || "";

  const payload = {
    nome: aluno['nome ']?.trim(),
    curso: aluno['curso']?.trim(),
    carga_horaria: aluno['carga horaria']?.toString(),
    data_conclusao: excelSerialToDate(aluno['data conclusão']),
    unidade: aluno['unidade ']?.trim(),
    historico: state.historico || [],
    prompt_extra: promptExtra
  };

  console.log("📤 Enviando payload:", payload);

  try {
    const response = await fetch("/certificados/gerar-texto", {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(payload),
    });

    console.log("Status da resposta:", response.status);

    const textResponse = await response.text();
    console.log("Resposta bruta do servidor:", textResponse);

    let data;
    try {
      data = JSON.parse(textResponse);
    } catch {
      throw new Error("Resposta não é JSON válido");
    }

    console.log("📥 Resposta JSON:", data);

    if (data.status === "success") {
      document.querySelector("#descricao-certificado").value = data.texto;
      addDescricaoTexto();
      showToast("Texto do certificado gerado com sucesso!");
      document.querySelector("#prompt").value = "";

      // 🔁 Atualiza o histórico para continuar a conversa
      if (data.historico) {
      state.historico = data.historico;
      atualizarHistoricoUI(state.historico);
    }
    } else {
      showToast("Erro: " + data.mensagem, "error");
    }
  } catch (err) {
    console.error("❌ Erro na requisição:", err);
    showToast("Falha na comunicação com o servidor", "error");
  }
}

function refinarTexto() {
  gerarTextoCertificado(); // mesma função, ela já usa o histórico
}

function limparHistorico() {
  state.historico = [];
  document.querySelector("#prompt").value = "";
  document.querySelector("#descricao-certificado").value = "";
}

function usarSugestao(sugestao) {
  document.querySelector("#prompt").value = sugestao;
}

function atualizarHistoricoUI(historico) {
  if (!historico || !Array.isArray(historico)) {
    console.warn("Histórico ausente ou inválido:", historico);
    return;
  }

  const div = document.querySelector("#chat-historico");
  if (!div) return;

  div.innerHTML = historico
    .filter(msg => msg.role !== "system")
    .map(msg => {
      const classe = msg.role === "user" ? "mensagem-usuario" : "mensagem-ia";
      ;
    })
    .join("");
}

  </script>
</body>
</html>