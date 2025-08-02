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
      /* Estilos customizados que não existem no Tailwind */
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
  background-color: rgba(255, 255, 255, 0.8);
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
      <div class="w-72 bg-white p-6 border-r border-gray-200 overflow-y-auto transition-all duration-300" id="form-column">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
          <h1 class="text-lg font-semibold text-gray-900">Editor de Certificado</h1>
          <button class="toggle-sidebar hidden p-1 text-gray-500 hover:text-gray-700" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
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
            <div id="template-thumbnails" class="template-thumbnails mt-3 space-y-2"></div>
          </div>
        </div>
      </div>

      <!-- ÁREA DE PREVIEW -->
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
              <input type="range" id="font-size-range" min="8" max="52" value="24" class="flex-1 h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer">
              <input type="number" id="font-size" min="8" max="52" value="24" class="w-12 text-xs border border-gray-300 rounded px-2 py-1 text-center">
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

          <!-- Editor de texto -->
          <div class="space-y-4">
            <div>
              <label for="prompt" class="block text-sm font-medium text-gray-700 mb-2">Personalizar texto do certificado:</label>
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
              
              <div class="flex gap-3 mt-3">
                <button onclick="refinarTexto()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                  Refinar com esse prompt
                </button>
                <button onclick="gerarTextoCertificado()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                  Gerar Texto
                </button>
                <button onclick="limparHistorico()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg border border-gray-300 transition-colors flex items-center justify-center gap-2">
                  Novo Texto
                </button>
              </div>
              
              <div id="chat-historico" class="mt-4 border border-gray-200 rounded-lg p-3 bg-gray-50 max-h-48 overflow-y-auto"></div>
            </div>

            <div>
              <label for="descricao-certificado" class="block text-sm font-medium text-gray-700 mb-2">Texto do Certificado:</label>
              <textarea id="descricao-certificado" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
CERTIFICAMOS, por meio deste, que Raphael concluiu con êxito o curso de Odontologia, cumprindo uma carga horária total de 200 horas. De referido curso foi realizado na unidade de Campinas, com a data de conclusão registrada em 20 de julho de 2025. Agradecemos ao corpo docente pela excelência na condução das atividades acadêmicas, cuja dedicação e comprometimento foram fundamentais para a formação do aluno.
              </textarea>
              <input type="hidden" name="descricao" id="descricao-certificado-input" value="">
              
              <button id="add-descricao" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Adicionar/Atualizar Texto
              </button>
            </div>
          </div>

          <!-- Preview do certificado -->
          <div class="mt-6">
<div class="preview-container border border-gray-200 rounded-lg bg-white w-full aspect-[4/3] relative overflow-hidden" id="preview" style="background-size: contain; background-repeat: no-repeat; background-position: center;"></div>          </div>
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

  // Adicione esta função modificada (mantendo toda a lógica original)
function setupDragAndDrop() {
  const preview = document.getElementById('preview'); // ID original mantido
  const excelColumns = document.getElementById('excel-columns'); // ID original mantido

  // 1. Configuração dos listeners originais
  preview.addEventListener('dragover', function(e) {
    e.preventDefault();
    preview.classList.add('border-blue-500', 'bg-blue-50'); // Classes do Tailwind
  });

  preview.addEventListener('dragleave', function() {
    preview.classList.remove('border-blue-500', 'bg-blue-50');
  });

  preview.addEventListener('drop', function(e) {
    e.preventDefault();
    preview.classList.remove('border-blue-500', 'bg-blue-50');
    
    // Lógica original de tratamento do drop
    try {
      const data = JSON.parse(e.dataTransfer.getData('application/json'));
      if (data.type === 'excel-field') {
        const fieldName = data.name;
        const div = createFieldElement(fieldName, true);
        div.style.top = `${e.offsetY}px`;
        div.style.left = `${e.offsetX}px`;
        preview.appendChild(div);
        makeDraggable(div); // Chama a função original
      }
    } catch {
      const text = e.dataTransfer.getData('text/plain');
      if (text) {
        const div = createFieldElement(text);
        div.style.top = `${e.offsetY}px`;
        div.style.left = `${e.offsetX}px`;
        preview.appendChild(div);
        makeDraggable(div); // Chama a função original
      }
    }
  });

  // 2. Atualização dos itens arrastáveis
  if (excelColumns) {
    new MutationObserver(function() {
      document.querySelectorAll('.column-item').forEach(function(item) {
        item.draggable = true;
        item.addEventListener('dragstart', function(e) {
          e.dataTransfer.setData('text/plain', e.target.textContent);
          e.dataTransfer.setData('application/json', JSON.stringify({
            type: 'excel-field',
            name: e.target.textContent
          }));
        });
      });
    }).observe(excelColumns, { childList: true });
  }
}

// Atualize a inicialização para garantir que seja chamada
document.addEventListener('DOMContentLoaded', function() {
  // ... seu código existente ...
  setupDragAndDrop(); // Adicione esta linha
});

  function makeDraggable(el) {
  let offsetX, offsetY, isDragging = false;
  
  el.addEventListener('mousedown', e => {
    if (e.button !== 0) return; // Apenas botão esquerdo
    
    // Seleciona o elemento
    if (state.selectedElement) state.selectedElement.classList.remove('selected');
    state.selectedElement = el;
    el.classList.add('selected');
    
    // Prepara para arrastar
    const rect = el.getBoundingClientRect();
    const previewRect = preview.getBoundingClientRect();
    
    offsetX = e.clientX - rect.left + previewRect.left;
    offsetY = e.clientY - rect.top + previewRect.top;
    isDragging = false;
    
    function mouseMoveHandler(ev) {
      isDragging = true;
      
      // Calcula a nova posição relativa ao preview
      let newX = ev.clientX - offsetX;
      let newY = ev.clientY - offsetY;
      
      // Limita ao container
      newX = Math.max(0, Math.min(newX, previewRect.width - rect.width));
      newY = Math.max(0, Math.min(newY, previewRect.height - rect.height));
      
      el.style.left = `${newX}px`;
      el.style.top = `${newY}px`;
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