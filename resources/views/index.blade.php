<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editor de Certificados</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    @layer components {
      .template-thumb {
        @apply w-full h-[70px] bg-cover bg-center cursor-pointer transition-all border-2 border-transparent rounded-md;
      }
      .template-thumb.selected {
        @apply border-blue-500 shadow-md shadow-blue-500/10;
      }
      .draggable {
        @apply absolute px-3 py-1 cursor-move border border-transparent select-none max-w-[80%] transition-all text-sm;
      }
      .draggable.selected {
        @apply border-blue-500 bg-blue-50 shadow-sm shadow-blue-500/10;
      }
      .draggable:hover {
        @apply scale-[1.02];
      }
      .preview-container.drop-active {
        @apply border-2 border-dashed border-blue-500 bg-blue-50;
      }
      #font-size-range::-webkit-slider-thumb {
        @apply appearance-none w-3 h-3 bg-blue-600 rounded-full cursor-pointer;
      }
    }

    .draggable {
      position: absolute;
      padding: 0.5rem 1rem;
      cursor: move;
      border: 1px solid transparent;
      user-select: none;
      max-width: 80%;
      transition: all 0.2s;
      font-size: 14px;
        z-index: 10;
    }

    .draggable.selected {
      border: 1px solid #3b82f6;
      background-color: rgba(239, 246, 255, 0.9);
      box-shadow: 0 1px 3px rgba(59, 130, 246, 0.1);
    }

    .preview-container.drop-active {
      border: 2px dashed #3b82f6;
      background-color: rgba(239, 246, 255, 0.5);
    }


  </style>
</head>
<body class="font-sans text-gray-900 bg-gray-50 flex h-screen overflow-hidden">
  <!-- SIDEBAR -->
  <div class="w-16 bg-white flex flex-col items-center py-6 border-r border-gray-200 z-10">
    <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" class="w-8 h-8 mb-8" alt="Logo">
    <div class="flex flex-col items-center space-y-5">
      <button class="p-2 rounded-lg hover:bg-blue-50 transition-all group" title="Gerador">
        <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" class="w-5 h-5 group-hover:scale-110 transition-transform">
      </button>
      <button class="p-2 rounded-lg hover:bg-blue-50 transition-all group" title="Relatórios">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" class="w-5 h-5 group-hover:scale-110 transition-transform">
      </button>
      <button class="p-2 rounded-lg hover:bg-blue-50 transition-all group" title="Sair">
        <img src="https://cdn-icons-png.flaticon.com/512/992/992680.png" class="w-5 h-5 group-hover:scale-110 transition-transform">
      </button>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <div class="flex flex-1 overflow-hidden">
      <!-- LATERAL ESQUERDA -->
      <div class="w-72 bg-white p-6 border-r border-gray-200 overflow-y-auto">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
          <h1 class="text-lg font-semibold text-gray-900">Editor de Certificado</h1>
        </div>
        
        <div class="space-y-6">
          <!-- Upload do Excel -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload do Excel:</label>
            <div class="upload-area border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer transition-colors hover:border-blue-500 hover:bg-blue-50" id="upload-area">
              <p class="text-sm text-gray-600 mb-3">Arraste seu arquivo Excel aqui ou</p>
              <input type="file" id="excel-upload" accept=".xls,.xlsx" class="hidden" />
              <button class="secondary bg-white border border-gray-300 text-blue-600 font-medium rounded-lg px-4 py-2 text-sm hover:bg-gray-50 transition-colors" onclick="document.getElementById('excel-upload').click()">
                Selecionar Arquivo
              </button>
              <p class="text-xs text-gray-500 mt-3">Formatos suportados: .xls, .xlsx</p>
            </div>
          </div>

          <!-- Campos do Excel -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Campos do Excel (arraste para o certificado):</label>
            <div id="excel-columns" class="mapping-columns bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-80 overflow-y-auto">
              <p class="text-xs text-gray-500 text-center w-full">Faça upload do Excel para carregar as colunas...</p>
            </div>
          </div>

          <!-- Seleção de Template -->
          <div>
            <label for="template" class="block text-sm font-medium text-gray-700 mb-2">Selecione Template:</label>
            <select id="template" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
              <option value="/storage/templates/template_certificado_1.jpg">Graduação Odontologia</option>
              <option value="/storage/templates/template_certificado_2.jpg">Pós-Odontologia</option>
              <option value="/storage/templates/template_certificado_3.jpg">SLMandic</option>
            </select>
            <div id="template-thumbnails" class="mt-2 flex flex-col gap-2"></div>
          </div>
        </div>
      </div>

      <!-- ÁREA CENTRAL DE PREVIEW (RESTAURADA COMPLETAMENTE) -->
      <div class="flex-1 flex flex-col overflow-auto bg-gray-50 p-6">
        <div class="max-w-4xl w-full mx-auto bg-white rounded-xl shadow-sm p-6">
          <!-- Barra de ferramentas -->
          <div class="flex flex-wrap items-center gap-3 bg-gray-50 rounded-lg p-3 mb-6">
            <!-- Controles de fonte -->
            <div class="flex items-center gap-2">
              <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Fonte</label>
              <select id="font-selector" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="Work Sans">Work Sans</option>
                <option value="Roboto">Roboto</option>
                <option value="Arial">Arial</option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Cor</label>
              <input type="color" id="font-color" value="#000000" class="w-6 h-6 p-0 border-none rounded-full cursor-pointer">
            </div>

            <div class="flex items-center gap-2 flex-1 min-w-[150px]">
              <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Tamanho</label>
              <input type="range" id="font-size-range" min="8" max="32" value="16" class="flex-1 h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer">
              <input type="number" id="font-size" min="8" max="52" value="16" class="w-12 text-xs border border-gray-300 rounded px-2 py-1 text-center">
            </div>

            <!-- Botões de formatação -->
            <div class="flex gap-1">
              <button data-command="bold" class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-100 transition-colors" title="Negrito">
                <b class="text-sm">B</b>
              </button>
              <button data-command="italic" class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-100 transition-colors" title="Itálico">
                <i class="text-sm">I</i>
              </button>
              <button data-command="underline" class="w-7 h-7 flex items-center justify-center border border-gray-300 rounded bg-white hover:bg-gray-100 transition-colors" title="Sublinhado">
                <u class="text-sm">S</u>
              </button>
            </div>

            <!-- Botões de ação -->
            <div class="flex gap-2 ml-auto">
              <button id="auto-position" class="compact-button px-3 py-1 text-xs border border-gray-300 rounded bg-white hover:bg-gray-100 transition-colors" title="Auto-Posicionar">
                Posicionar
              </button>
              <button id="delete-field" class="compact-button danger px-3 py-1 text-xs border border-red-200 rounded bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Remover">
                Remover
              </button>
              <button id="save-layout" class="compact-button secondary px-3 py-1 text-xs border border-gray-300 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors" title="Salvar">
                Salvar
              </button>
            </div>
          </div>

          <!-- Texto do Certificado -->
          <div>
            <label for="descricao-certificado" class="block text-sm font-medium text-gray-700 mb-2">Texto do Certificado:</label>
            <div id="descricao-certificado" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
CERTIFICAMOS, por meio deste, que Raphael concluiu con êxito o curso de Odontologia, cumprindo uma carga horária total de 200 horas. De referido curso foi realizado na unidade de Campinas, com a data de conclusão registrada em 20 de julho de 2025. Agradecemos ao corpo docente pela excelência na condução das atividades acadêmicas, cuja dedicação e comprometimento foram fundamentais para a formação do aluno.
  </div>
            <input type="hidden" name="descricao" id="descricao-certificado-input" value="">
            
            <button id="add-descricao" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Adicionar/Atualizar Texto
            </button>
          </div>

          <!-- Preview do certificado -->
          <div class="mt-6">
            <div class="preview-container border border-gray-200 rounded-lg bg-white w-full aspect-[4/3] relative overflow-hidden" id="preview" style="background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
          </div>
        </div>
      </div>

      <!-- NOVA COLUNA DIREITA (APENAS PROMPT IA) -->
      <div class="w-64 bg-white p-6 border-l border-gray-200 overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">IA — Geração de Texto</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Personalizar texto do certificado:</label>
            <textarea id="prompt" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Digite aqui como quer que o certificado seja gerado..."></textarea>
            
            <div class="prompt-sugestoes flex gap-2 mt-2 flex-wrap">
              <button onclick="usarSugestao('Deixe o texto mais formal.')" class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-md border border-gray-300 hover:bg-gray-200 transition-colors">
                + Formal
              </button>
              <button onclick="usarSugestao('Adicione agradecimentos ao corpo docente.')" class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-md border border-gray-300 hover:bg-gray-200 transition-colors">
                + Agradecimento
              </button>
              <button onclick="usarSugestao('Resuma o texto em uma linha.')" class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-md border border-gray-300 hover:bg-gray-200 transition-colors">
                + Resumo
              </button>
            </div>
          </div>
          
          <div class="space-y-2">
            <button onclick="refinarTexto()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
              Refinar com esse prompt
            </button>
            <button onclick="gerarTextoCertificado()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
              Gerar Texto
            </button>
            <button onclick="limparHistorico()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg border border-gray-300 transition-colors">
              Novo Texto
            </button>
          </div>
          
          <div id="chat-historico" class="mt-4 border border-gray-200 rounded-lg p-3 bg-gray-50 max-h-48 overflow-y-auto text-sm"></div>
        </div>
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
    excelData: null,
    historico: []
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

  // Ativar edição no campo de descrição
  descricaoTextarea.setAttribute("contenteditable", "true");

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
    renderTemplateThumbnails();
  });

function renderTemplateThumbnails() {
  const thumbnailsContainer = document.getElementById('template-thumbnails');
  if (!thumbnailsContainer || !templateSelect) return;

  thumbnailsContainer.innerHTML = ''; // Limpa antes de recriar

  Array.from(templateSelect.options).forEach(option => {
    const templatePath = option.value;

   const thumb = document.createElement('div');
thumb.className = `
  w-full 
  aspect-[16/6] 
  bg-contain bg-no-repeat bg-center 
  border-2 border-transparent rounded-md 
  cursor-pointer transition-transform duration-200 
  hover:scale-[1.02]
`;
thumb.style.backgroundImage = `url('${templatePath}')`;

    // Clique seleciona template e atualiza preview
    thumb.addEventListener('click', () => {
      templateSelect.value = option.value;
      atualizarPreview();

      // Remove seleção anterior
      document.querySelectorAll('.template-thumb.selected').forEach(el => el.classList.remove('selected'));
thumb.classList.add('border-blue-600');
    });

    // Se for o selecionado atual, marca como ativo
    if (templateSelect.value === option.value) {
      thumb.classList.add('selected');
    }

    thumbnailsContainer.appendChild(thumb);
  });
}


  function setupEventListeners() {
    templateSelect.addEventListener('change', atualizarPreview);
    fontSelector.addEventListener('change', updateSelectedElementStyle);
    fontSizeInput.addEventListener('input', debounce(updateSelectedElementStyle, 50));
    fontColorInput.addEventListener('input', debounce(updateSelectedElementStyle, 50));
    deleteFieldBtn.addEventListener('click', deleteSelectedElement);
    autoPositionBtn.addEventListener('click', autoPositionFields);
    addDescricaoBtn.addEventListener('click', () => addDescricaoTexto());

    const fontSizeRange = document.getElementById('font-size-range');
    if (fontSizeRange) {
      fontSizeRange.addEventListener('input', () => {
        fontSizeInput.value = fontSizeRange.value;
        updateSelectedElementStyle();
      });
      fontSizeInput.addEventListener('input', () => {
        fontSizeRange.value = fontSizeInput.value;
        updateSelectedElementStyle();
      });
    }

    excelUpload.addEventListener('change', handleFileUpload);

    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.classList.add('active');
    });

    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('active'));

    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.classList.remove('active');
      if (e.dataTransfer.files.length) {
        excelUpload.files = e.dataTransfer.files;
        handleFileUpload();
      }
    });

    preview.addEventListener('click', handlePreviewClick);

    const gerarTextoBtn = document.getElementById('gerar-texto-btn');
    if (gerarTextoBtn) gerarTextoBtn.addEventListener('click', gerarTextoCertificado);
  }

  function atualizarPreview() {
    state.currentTemplate = templateSelect.value;
    preview.style.backgroundImage = `url('${state.currentTemplate}')`;
    preview.style.backgroundSize = "contain";
    preview.style.backgroundRepeat = "no-repeat";
    preview.style.backgroundPosition = "center";
    showToast('Template atualizado com sucesso');
  }

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
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
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
        excelColumns.innerHTML = '<p style="color:#ef4444;font-size:12px;text-align:center;">Nenhuma coluna encontrada.</p>';
        showToast('Nenhuma coluna encontrada', 'error');
      }
    } catch (err) {
      console.error(err);
      excelColumns.innerHTML = '<p style="color:#ef4444;font-size:12px;text-align:center;">Erro ao processar o arquivo.</p>';
      showToast('Erro ao processar arquivo', 'error');
    } finally {
      hideLoading();
    }
  }

  function mapExcelFields(columns) {
    const mapped = {};
    columns.forEach(col => {
      const key = col.toLowerCase().trim();
      if (key.includes('nome')) mapped[col] = 'nome';
    });
    return mapped;
  }

  function renderExcelColumns(columns) {
    const fragment = document.createDocumentFragment();
    columns.forEach(col => {
      const div = document.createElement('div');
      div.className = 'column-item';
      div.textContent = col;
      div.draggable = true;
      div.dataset.columnName = col;
      div.addEventListener('dragstart', e => {
        e.dataTransfer.setData('application/json', JSON.stringify({ type: 'excel-field', name: col }));
      });
      fragment.appendChild(div);
    });
    excelColumns.innerHTML = '';
    excelColumns.appendChild(fragment);
  }

  function placeFieldsOnCertificate(mappedFields) {
    preview.querySelectorAll('.draggable[data-field-type="excel-field"]').forEach(el => el.remove());
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

  function applyPositionStyles(element, position) {
    element.style.left = position.x;
    element.style.top = position.y;
    element.style.transform = 'translate(-50%, -50%)';
    element.style.fontSize = position.fontSize + 'px';
    element.style.fontFamily = fontSelector.value;
    element.style.color = fontColorInput.value;
    element.style.textAlign = position.align;
  }

  function setupDragAndDrop() {
    preview.addEventListener('dragover', (e) => {
      e.preventDefault();
      preview.classList.add('drop-active');
    });
    preview.addEventListener('dragleave', () => preview.classList.remove('drop-active'));
    preview.addEventListener('drop', (e) => {
      e.preventDefault();
      preview.classList.remove('drop-active');
      if (state.draggingElement) return;
      const data = e.dataTransfer.getData('application/json');
      if (data) {
        try {
          const fieldData = JSON.parse(data);
          if (fieldData.type === 'excel-field') {
            const fieldElement = createFieldElement(fieldData.name, true);
            positionElementAt(fieldElement, e.clientX, e.clientY);
            preview.appendChild(fieldElement);
          }
        } catch (err) {
          console.error('Error parsing drop data:', err);
        }
      }
    });
  }

  function positionElementAt(element, clientX, clientY) {
    const previewRect = preview.getBoundingClientRect();
    element.style.left = `${clientX - previewRect.left}px`;
    element.style.top = `${clientY - previewRect.top}px`;
  }

  function makeDraggable(el) {
    let isDragging = false;
    let startX, startY, offsetX, offsetY;

    el.addEventListener('mousedown', (e) => {
      if (e.button !== 0) return;
      if (state.selectedElement) state.selectedElement.classList.remove('selected');
      state.selectedElement = el;
      el.classList.add('selected');
      isDragging = false;
      startX = e.clientX;
      startY = e.clientY;
      offsetX = e.offsetX;
      offsetY = e.offsetY;
      state.draggingElement = true;

      function moveHandler(e) {
        if (!isDragging && (Math.abs(e.clientX - startX) > 5 || Math.abs(e.clientY - startY) > 5)) {
          isDragging = true;
        }
        if (isDragging) {
          const previewRect = preview.getBoundingClientRect();
          el.style.left = `${e.clientX - previewRect.left - offsetX}px`;
          el.style.top = `${e.clientY - previewRect.top - offsetY}px`;
          el.style.transform = 'none';
        }
      }

      function upHandler() {
        document.removeEventListener('mousemove', moveHandler);
        document.removeEventListener('mouseup', upHandler);
        state.draggingElement = false;
        if (!isDragging) updateStyleControls();
      }

      document.addEventListener('mousemove', moveHandler);
      document.addEventListener('mouseup', upHandler, { once: true });
    });

    el.setAttribute('draggable', 'false');
    el.addEventListener('dragstart', (e) => e.preventDefault());
  }

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

  function updateStyleControls() {
    if (!state.selectedElement) return;
    const style = state.selectedElement.style;
    fontSelector.value = style.fontFamily || 'Arial';
    fontSizeInput.value = parseInt(style.fontSize) || 24;
    fontColorInput.value = style.color.startsWith('rgb') ? rgbToHex(style.color) : style.color || '#000000';
  }

  function updateSelectedElementStyle() {
    if (state.selectedElement) {
      state.selectedElement.style.fontFamily = fontSelector.value;
      state.selectedElement.style.fontSize = `${fontSizeInput.value}px`;
      state.selectedElement.style.color = fontColorInput.value;
    }
  }

  function deleteSelectedElement() {
    if (state.selectedElement) {
      state.selectedElement.remove();
      state.selectedElement = null;
      showToast('Campo removido');
    }
  }

  function autoPositionFields() {
    if (!state.excelData) {
      showToast('Faça o upload do Excel primeiro!', 'warning');
      return;
    }
    placeFieldsOnCertificate(state.mappedFields);
    addDescricaoTexto(true);
    showToast('Campos reposicionados automaticamente');
  }

  function addDescricaoTexto(reposicionar = false) {
    const textValue = descricaoTextarea.innerHTML.trim();
    if (!textValue) {
      showToast("Digite um texto antes de adicionar!", "warning");
      return;
    }
    let desc = preview.querySelector('#descricao-cert');
    if (!desc) {
      desc = createFieldElement('', false);
      desc.id = 'descricao-cert';
      preview.appendChild(desc);
    }
    desc.innerHTML = textValue; // Mantém formatação
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
    if (!reposicionar) showToast('Texto do certificado atualizado');
  }

  // ✅ Toolbar de texto corrigida
  function setupTextEditor() {
    const toolbar = document.createElement("div");
    toolbar.className = "flex gap-2 mb-2";
    toolbar.innerHTML = `
      <button data-command="bold"><b>B</b></button>
      <button data-command="italic"><i>I</i></button>
      <button data-command="underline"><u>S</u></button>
    `;
    descricaoTextarea.parentNode.insertBefore(toolbar, descricaoTextarea);
    document.querySelectorAll('[data-command]').forEach(btn => {
      btn.addEventListener('click', () => {
        descricaoTextarea.focus();
        document.execCommand(btn.dataset.command, false, null);
      });
    });
  }

  function rgbToHex(rgb) {
    if (!rgb) return '#000000';
    const result = /^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*\d+\.*\d*)?\)$/.exec(rgb);
    if (!result) return '#000000';
    return "#" + [result[1], result[2], result[3]].map(x => {
      const hex = parseInt(x).toString(16);
      return hex.length === 1 ? '0' + hex : hex;
    }).join('');
  }

function showToast(message, type = 'success') {
  const toast = document.createElement('div');

  // Classes comuns
  toast.className = `
    fixed top-4 right-4
    min-w-[220px] px-5 py-3
    text-white font-semibold rounded-lg shadow-lg
    cursor-default select-none
    transform translate-x-24 opacity-0
    transition-all duration-300 ease-out
    pointer-events-auto
    z-50
    `;

  // Classes por tipo
  if (type === 'success') {
    toast.classList.add('bg-green-500');
  } else if (type === 'error') {
    toast.classList.add('bg-red-400');
  } else if (type === 'warning') {
    toast.classList.add('bg-yellow-400', 'text-black');
  } else {
    toast.classList.add('bg-gray-700');
  }

  toast.textContent = message;
  document.body.appendChild(toast);

  // Força reflow para animar (remover translate-x e opacity)
  requestAnimationFrame(() => {
    toast.classList.remove('translate-x-24', 'opacity-0');
  });

  // Após 3 segundos começa a sumir
  setTimeout(() => {
    toast.classList.add('translate-x-24', 'opacity-0');
    toast.addEventListener('transitionend', () => toast.remove());
  }, 3000);
}


  function showLoading() {
    uploadArea.innerHTML = '<p style="color:#2563eb;">Processando arquivo...</p>';
  }

  function hideLoading() {
    uploadArea.innerHTML = `
      <p>Arquivo carregado: ${excelUpload.files[0].name}</p>
      <button id="btn-trocar-arquivo" class="secondary">Trocar Arquivo</button>
    `;
    uploadArea.addEventListener('click', () => {
      excelUpload.click();
    });
  }

  function debounce(func, timeout = 100) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
  }

  function toggleSidebar() {
    document.getElementById('form-column').classList.toggle('collapsed');
  }

  function excelSerialToDate(serial) {
    const excelEpoch = new Date(1899, 11, 30);
    const date = new Date(excelEpoch.getTime() + serial * 86400000);
    return date.toISOString().split('T')[0];
  }

  async function gerarTextoCertificado() {
    if (!state.excelData || !state.excelData.dados || !state.excelData.dados.length) {
      console.error('Excel está vazio ou mal formatado');
      showToast("Nenhum dado de Excel encontrado!", "error");
      return;
    }
    const aluno = state.excelData.dados[0];
    const promptExtra = document.querySelector("#prompt")?.value?.trim() || "";
    const payload = {
      nome: aluno['nome']?.trim() || aluno['nome ']?.trim(),
      curso: aluno['curso']?.trim(),
      carga_horaria: aluno['carga horaria'],
      data_conclusao: aluno['data conclusao'] ? excelSerialToDate(aluno['data conclusao']) : null,
      unidade: aluno['unidade'],
      cpf: aluno['cpf'],
      prompt: promptExtra
    };
    console.log("📤 Enviando payload:", payload);
    try {
      const response = await fetch('/certificados/gerar-texto', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(payload),
      });
      console.log("Status da resposta:", response.status);
      const data = await response.json();
      console.log("Resposta bruta do servidor:", data);
      if (data.status === "success" && data.texto) {
        descricaoTextarea.innerHTML = data.texto;
        addDescricaoTexto();
        showToast("Texto gerado com sucesso!");
      } else {
        console.error("Erro ao gerar texto:", data);
        showToast("Erro ao gerar texto", "error");
      }
    } catch (err) {
      console.error("Erro na requisição:", err);
      showToast("Erro na requisição de texto", "error");
    }
  }
</script>


</body>
</html>