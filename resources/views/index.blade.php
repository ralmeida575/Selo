<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editor de Certificados</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #dce3f0 100%);
      min-height: 100vh;
    }

    .template-thumb {
      @apply w-full h-32 bg-cover bg-center cursor-pointer transition-all duration-300 border-2 border-transparent rounded-xl hover:shadow-lg hover:scale-105 relative overflow-hidden;
    }
    
    .template-thumb.selected {
      @apply border-amber-500 shadow-lg shadow-amber-500/20 scale-105;
    }
    
    .template-overlay {
      @apply absolute bottom-2 left-2 right-2 text-white text-xs font-semibold bg-black/50 backdrop-blur-sm rounded-lg px-2 py-1 text-center;
    }
    
    .draggable {
      @apply absolute px-3 py-1 cursor-move border border-transparent select-none max-w-[80%] transition-all duration-200 text-sm backdrop-blur-sm bg-white/80 rounded-md shadow-sm;
    }
    
    .draggable.selected {
      @apply border-amber-600 bg-amber-50/90 shadow-lg shadow-amber-500/20;
    }
    
    .excel-column {
      @apply bg-gradient-to-r from-slate-50 to-amber-50 text-slate-800 px-4 py-2.5 rounded-lg text-sm mb-2 cursor-pointer hover:from-amber-100 hover:to-amber-50 transition-all duration-200 border border-amber-200/50 shadow-sm hover:shadow-md;
    }
    
    .glass-morphism {
      @apply backdrop-blur-lg bg-white/80 border border-white/20 shadow-2xl;
    }
    
    .card-elevated {
      @apply bg-white rounded-2xl shadow-xl border border-gray-100/50;
    }
    
    .btn-primary {
      @apply bg-gradient-to-r from-slate-900 to-slate-800 hover:from-slate-800 hover:to-slate-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98];
    }
    
    .btn-secondary {
      @apply bg-white hover:bg-slate-50 text-slate-700 font-medium py-2.5 px-6 rounded-xl border border-gray-200 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02];
    }
    
    .workflow-step {
      @apply flex flex-col items-center space-y-2 p-3 rounded-xl transition-all duration-200;
    }
    
    .workflow-step.active {
      @apply bg-amber-50 border border-amber-200;
    }
    
    .workflow-step.completed {
      @apply bg-green-50 border border-green-200;
    }
    
    .step-container {
      @apply transition-all duration-500 ease-in-out;
    }
    
    .step-container.hidden {
      @apply opacity-0 pointer-events-none transform scale-95;
    }
    
    .upload-area.active {
      @apply border-amber-500 bg-gradient-to-br from-amber-50 to-amber-100;
    }

    .preview-container.drop-active {
      @apply border-2 border-dashed border-amber-500 bg-amber-50/30;
    }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  </style>
</head>
<body class="font-sans text-gray-900 flex h-screen overflow-hidden">
  
  <!-- SIDEBAR -->
  <aside class="w-72 glass-morphism flex flex-col h-full m-4 mr-0 rounded-2xl">
    <div class="p-4 flex items-center justify-center">
      <div class="w-36 h-36 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center">
        <span class="text-white font-bold text-2xl">CERT</span>
      </div>
    </div>
    
    <nav class="flex-1 p-4 space-y-2">
      <div class="flex items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-slate-900 to-slate-800 rounded-xl shadow-lg">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Editor de Certificados
      </div>
    </nav>
    
    <div class="p-4 border-t border-white/20 text-xs text-gray-500 text-center">
      Sistema v2.0
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="flex-1 flex flex-col overflow-hidden">
    
    <!-- TOP BAR -->
    <div class="glass-morphism m-4 mb-0 rounded-2xl">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Editor de Certificados</h2>
            <p class="text-sm text-gray-600 mt-1" id="step-description">Escolha um template para começar</p>
          </div>
          <div class="flex items-center space-x-3">
            <button id="prev-step" class="btn-secondary hidden">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Voltar
            </button>
            <button id="next-step" class="btn-primary hidden">
              Próximo
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- WORKFLOW -->
    <div class="glass-morphism m-4 mb-0 rounded-2xl">
      <div class="p-4">
        <div class="flex items-center justify-between space-x-4">
          <div class="workflow-step active flex-1 text-center" data-step="1">
            <div class="w-8 h-8 bg-amber-600 text-white text-sm rounded-full flex items-center justify-center font-semibold mx-auto">1</div>
            <span class="text-sm font-medium text-gray-700">Template</span>
          </div>
          <div class="h-px bg-gray-300 flex-1"></div>
          <div class="workflow-step flex-1 text-center" data-step="2">
            <div class="w-8 h-8 bg-gray-300 text-white text-sm rounded-full flex items-center justify-center font-semibold mx-auto">2</div>
            <span class="text-sm font-medium text-gray-500">Dados</span>
          </div>
          <div class="h-px bg-gray-300 flex-1"></div>
          <div class="workflow-step flex-1 text-center" data-step="3">
            <div class="w-8 h-8 bg-gray-300 text-white text-sm rounded-full flex items-center justify-center font-semibold mx-auto">3</div>
            <span class="text-sm font-medium text-gray-500">Configuração</span>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 flex overflow-hidden p-4 space-x-4">
      
      <!-- STEP 1: TEMPLATE SELECTION -->
      <div id="step-1" class="step-container flex-1 card-elevated p-8">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Escolha um Template</h3>
          <p class="text-gray-600">Selecione o design para seu certificado</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
          <div class="template-thumb selected" data-template="1" style="background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="template-overlay">Template Clássico</div>
          </div>
          
          <div class="template-thumb" data-template="2" style="background-image: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="template-overlay">Template Moderno</div>
          </div>
          
          <div class="template-thumb" data-template="3" style="background-image: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="template-overlay">Template Acadêmico</div>
          </div>
          
          <div class="template-thumb" data-template="4" style="background-image: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="template-overlay">Template Corporativo</div>
          </div>
          
          <div class="template-thumb" data-template="5" style="background-image: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="template-overlay">Template Elegante</div>
          </div>
          
          <div class="template-thumb" data-template="6" style="background-image: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
            <div class="template-overlay">Template Minimalista</div>
          </div>
        </div>

        <div class="text-center mt-8">
          <button id="select-template" class="btn-primary text-lg px-8 py-3">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Confirmar Template
          </button>
        </div>
      </div>

      <!-- STEP 2: FILE UPLOAD -->
      <div id="step-2" class="step-container hidden flex-1">
        <div class="w-full max-w-2xl mx-auto card-elevated p-8">
          <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Upload dos Dados</h3>
            <p class="text-gray-600">Envie o arquivo Excel com os dados dos certificados</p>
          </div>

          <div class="upload-area border-2 border-dashed border-gray-300 rounded-2xl p-12 text-center cursor-pointer transition-all duration-300 hover:border-amber-500 hover:bg-gradient-to-br hover:from-amber-50 hover:to-amber-100" id="upload-area">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-amber-100 to-amber-50 rounded-2xl flex items-center justify-center">
              <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
              </svg>
            </div>
            <p class="text-lg font-medium text-gray-700 mb-2">Arraste seu arquivo Excel aqui</p>
            <p class="text-sm text-gray-500 mb-6">ou clique para selecionar</p>
            <input type="file" id="excel-upload" accept=".xls,.xlsx" class="hidden" />
            <button class="btn-secondary text-sm" onclick="document.getElementById('excel-upload').click()">
              Selecionar Arquivo
            </button>
          </div>

          <div id="file-info" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center space-x-3">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-green-800" id="file-name"></p>
                <p class="text-xs text-green-600" id="file-details"></p>
              </div>
            </div>
          </div>

          <div class="text-center mt-8">
            <button id="process-excel" class="btn-primary text-lg px-8 py-3 hidden">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Processar Dados
            </button>
          </div>
        </div>
      </div>

      <!-- STEP 3: CERTIFICATE CONFIG -->
      <div id="step-3" class="step-container hidden flex-1 flex space-x-4">
        
        <!-- LEFT PANEL -->
        <div class="w-80 card-elevated p-6 overflow-y-auto">
          <div class="space-y-6">
            
            <!-- Excel Fields -->
            <div>
              <label class="flex items-center space-x-2 text-sm font-semibold text-gray-800 mb-4">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Campos Disponíveis</span>
              </label>
              <div id="excel-columns" class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-4 max-h-60 overflow-y-auto">
                <div class="excel-column" draggable="true" data-column="nome">Nome do Aluno</div>
                <div class="excel-column" draggable="true" data-column="curso">Nome do Curso</div>
                <div class="excel-column" draggable="true" data-column="carga_horaria">Carga Horária</div>
                <div class="excel-column" draggable="true" data-column="data_conclusao">Data de Conclusão</div>
                <div class="excel-column" draggable="true" data-column="unidade">Unidade</div>
              </div>
            </div>
            
            <!-- Certificate Text -->
            <div>
              <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-3">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Texto do Certificado</span>
              </label>
              <textarea id="certificate-text" rows="6" class="w-full p-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all duration-200 bg-white resize-none" placeholder="Digite o texto do certificado...">CERTIFICAMOS que [NOME] concluiu com êxito o curso de [CURSO], com carga horária de [CARGA_HORARIA] horas, na unidade [UNIDADE], em [DATA_CONCLUSAO].</textarea>
              
              <button id="add-text" class="w-full btn-primary mt-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Adicionar Texto
              </button>
            </div>
          </div>
        </div>

        <!-- CENTER PANEL - PREVIEW -->
        <div class="flex-1 card-elevated p-6">
          
          <!-- Toolbar -->
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-4 mb-6 border border-gray-200">
            <div class="flex items-center gap-4 flex-wrap">
              
              <div class="flex items-center space-x-2">
                <label class="text-xs font-semibold text-gray-600">Fonte</label>
                <select id="font-family" class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 bg-white">
                  <option value="Inter">Inter</option>
                  <option value="Arial">Arial</option>
                  <option value="Georgia">Georgia</option>
                  <option value="Times New Roman">Times New Roman</option>
                </select>
              </div>

              <div class="flex items-center space-x-2">
                <label class="text-xs font-semibold text-gray-600">Cor</label>
                <input type="color" id="font-color" value="#000000" class="w-8 h-8 border-2 border-white rounded-lg cursor-pointer">
              </div>

              <div class="flex items-center space-x-2">
                <label class="text-xs font-semibold text-gray-600">Tamanho</label>
                <input type="range" id="font-size" min="12" max="48" value="16" class="w-20">
                <span id="font-size-display" class="text-xs w-8 text-center">16</span>
              </div>

              <div class="flex space-x-1 bg-white rounded-lg p-1">
                <button id="bold-btn" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 font-bold">B</button>
                <button id="italic-btn" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 italic">I</button>
                <button id="underline-btn" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 underline">U</button>
              </div>

              <div class="ml-auto flex space-x-2">
                <button id="auto-position" class="btn-secondary text-xs px-3 py-2">Posicionar</button>
                <button id="delete-field" class="px-3 py-2 text-xs bg-red-50 border border-red-200 rounded-lg text-red-600 hover:bg-red-100">Remover</button>
              </div>
            </div>
          </div>

          <!-- Certificate Preview -->
          <div>
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center">
              <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              Preview do Certificado
            </h3>
            <div id="certificate-preview" class="preview-container border-2 border-gray-200 rounded-2xl bg-white w-full aspect-[4/3] relative overflow-hidden shadow-inner hover:shadow-lg transition-shadow duration-300" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            </div>
          </div>
        </div>

        <!-- RIGHT PANEL - AI ASSISTANT -->
        <div class="w-80 card-elevated p-6">
          <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-r from-slate-900 to-slate-700 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">IA Assistant</h3>
              <p class="text-sm text-gray-600">Geração de texto inteligente</p>
            </div>
          </div>
          
          <div class="space-y-4">
            <div>
              <label class="text-sm font-semibold text-gray-700 mb-3 block">Personalização</label>
              <textarea id="ai-prompt" rows="3" class="w-full p-3 border border-gray-300 rounded-xl text-sm resize-none" placeholder="Descreva como personalizar o texto..."></textarea>
            </div>
            
            <div class="space-y-2">
              <button id="generate-text" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Gerar Texto
              </button>
            </div>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-4 max-h-60 overflow-y-auto">
              <h4 class="text-sm font-semibold text-gray-800 mb-3">Histórico</h4>
              <div id="ai-history" class="text-sm text-gray-500 text-center py-8">
                Nenhuma conversa ainda
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <script>
    // Estado global da aplicação
    const AppState = {
      currentStep: 1,
      selectedTemplate: 1,
      selectedElement: null,
      fieldElements: [],
      excelData: null,
      isDragging: false
    };

    const templates = {
      1: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      2: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
      3: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
      4: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
      5: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
      6: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'
    };

    const stepDescriptions = {
      1: "Escolha um template para começar",
      2: "Faça upload do arquivo Excel",
      3: "Configure os campos do certificado"
    };

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
      initializeApp();
    });

    function initializeApp() {
      setupEventListeners();
      updateWorkflow();
      showStep(1);
    }

    function setupEventListeners() {
      // Template selection
      document.querySelectorAll('.template-thumb').forEach(thumb => {
        thumb.addEventListener('click', function() {
          document.querySelectorAll('.template-thumb').forEach(t => t.classList.remove('selected'));
          this.classList.add('selected');
          AppState.selectedTemplate = parseInt(this.dataset.template);
        });
      });

      // Step navigation
      document.getElementById('select-template').addEventListener('click', () => goToStep(2));
      document.getElementById('prev-step').addEventListener('click', () => goToStep(AppState.currentStep - 1));
      document.getElementById('next-step').addEventListener('click', () => goToStep(AppState.currentStep + 1));

      // File upload
      setupFileUpload();

      // Certificate configuration
      setupCertificateEditor();
    }

    function goToStep(step) {
      if (step < 1 || step > 3) return;
      
      AppState.currentStep = step;
      showStep(step);
      updateWorkflow();
      updateNavButtons();
      updateDescription();
      
      if (step === 3) {
        updatePreviewBackground();
      }
    }

    function showStep(step) {
      // Hide all steps
      document.querySelectorAll('.step-container').forEach(container => {
        container.classList.add('hidden');
      });

      // Show current step
      const currentStep = document.getElementById(`step-${step}`);
      if (currentStep) {
        currentStep.classList.remove('hidden');
      }
    }

    function updateWorkflow() {
      document.querySelectorAll('.workflow-step').forEach((stepEl, index) => {
        const stepNumber = index + 1;
        const circle = stepEl.querySelector('div');
        const text = stepEl.querySelector('span');
        
        if (stepNumber < AppState.currentStep) {
          // Completed
          stepEl.classList.remove('active');
          stepEl.classList.add('completed');
          circle.classList.remove('bg-gray-300', 'bg-amber-600');
          circle.classList.add('bg-green-600');
          circle.innerHTML = '✓';
          text.classList.remove('text-gray-500');
          text.classList.add('text-gray-700');
        } else if (stepNumber === AppState.currentStep) {
          // Active
          stepEl.classList.remove('completed');
          stepEl.classList.add('active');
          circle.classList.remove('bg-gray-300', 'bg-green-600');
          circle.classList.add('bg-amber-600');
          circle.innerHTML = stepNumber;
          text.classList.remove('text-gray-500');
          text.classList.add('text-gray-700');
        } else {
          // Inactive
          stepEl.classList.remove('active', 'completed');
          circle.classList.remove('bg-amber-600', 'bg-green-600');
          circle.classList.add('bg-gray-300');
          circle.innerHTML = stepNumber;
          text.classList.remove('text-gray-700');
          text.classList.add('text-gray-500');
        }
      });
    }

    function updateNavButtons() {
      const prevBtn = document.getElementById('prev-step');
      const nextBtn = document.getElementById('next-step');
      
      if (AppState.currentStep === 1) {
        prevBtn.classList.add('hidden');
        nextBtn.classList.add('hidden');
      } else if (AppState.currentStep === 2) {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.add('hidden');
      } else {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.add('hidden');
      }
    }

    function updateDescription() {
      const descEl = document.getElementById('step-description');
      if (descEl) {
        descEl.textContent = stepDescriptions[AppState.currentStep];
      }
    }

    function updatePreviewBackground() {
      const preview = document.getElementById('certificate-preview');
      if (preview) {
        preview.style.background = templates[AppState.selectedTemplate];
      }
    }

    function setupFileUpload() {
      const fileInput = document.getElementById('excel-upload');
      const uploadArea = document.getElementById('upload-area');
      const fileInfo = document.getElementById('file-info');
      const processBtn = document.getElementById('process-excel');

      // Click to select file
      uploadArea.addEventListener('click', () => fileInput.click());

      // File selection
      fileInput.addEventListener('change', handleFileSelection);

      // Drag and drop
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
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          fileInput.files = files;
          handleFileSelection();
        }
      });

      // Process file
      processBtn.addEventListener('click', processExcelFile);
    }

    function handleFileSelection() {
      const fileInput = document.getElementById('excel-upload');
      const file = fileInput.files[0];
      
      if (!file) return;

      // Validate file
      const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
      if (!validTypes.includes(file.type) && !file.name.match(/\.(xls|xlsx)$/)) {
        showToast('Por favor, selecione um arquivo Excel válido', 'error');
        return;
      }

      if (file.size > 10 * 1024 * 1024) {
        showToast('Arquivo muito grande. Máximo 10MB', 'error');
        return;
      }

      // Show file info
      const fileName = document.getElementById('file-name');
      const fileDetails = document.getElementById('file-details');
      const fileInfo = document.getElementById('file-info');
      const processBtn = document.getElementById('process-excel');
      
      fileName.textContent = file.name;
      fileDetails.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
      
      fileInfo.classList.remove('hidden');
      processBtn.classList.remove('hidden');
    }

    function processExcelFile() {
      const btn = document.getElementById('process-excel');
      const originalText = btn.innerHTML;
      
      btn.disabled = true;
      btn.innerHTML = '<div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></div>Processando...';
      
      // Simulate processing
      setTimeout(() => {
        // Mock data
        const mockColumns = ['Nome', 'Curso', 'Carga Horária', 'Data Conclusão', 'Unidade'];
        AppState.excelData = { columns: mockColumns };
        
        // Update Excel columns display
        updateExcelColumns(mockColumns);
        
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        showToast('Arquivo processado com sucesso!');
        
        setTimeout(() => goToStep(3), 1000);
      }, 2000);
    }

    function updateExcelColumns(columns) {
      const container = document.getElementById('excel-columns');
      container.innerHTML = '';
      
      columns.forEach(column => {
        const div = document.createElement('div');
        div.className = 'excel-column';
        div.textContent = column;
        div.draggable = true;
        div.dataset.column = column.toLowerCase().replace(/\s+/g, '_');
        
        // Drag start
        div.addEventListener('dragstart', (e) => {
          e.dataTransfer.setData('text/plain', column);
          e.dataTransfer.setData('application/json', JSON.stringify({
            type: 'excel-field',
            name: column
          }));
        });
        
        container.appendChild(div);
      });
    }

    function setupCertificateEditor() {
      const preview = document.getElementById('certificate-preview');
      
      // Drop zone setup
      preview.addEventListener('dragover', (e) => {
        e.preventDefault();
        preview.classList.add('drop-active');
      });
      
      preview.addEventListener('dragleave', () => {
        preview.classList.remove('drop-active');
      });
      
      preview.addEventListener('drop', handleDrop);
      
      // Style controls
      setupStyleControls();
      
      // Text addition
      document.getElementById('add-text').addEventListener('click', addCertificateText);
      
      // AI features
      document.getElementById('generate-text').addEventListener('click', generateAIText);
      
      // Field manipulation
      document.getElementById('auto-position').addEventListener('click', autoPositionFields);
      document.getElementById('delete-field').addEventListener('click', deleteSelectedField);
    }

    function handleDrop(e) {
      e.preventDefault();
      e.currentTarget.classList.remove('drop-active');
      
      if (AppState.isDragging) return;
      
      try {
        const data = JSON.parse(e.dataTransfer.getData('application/json'));
        if (data.type === 'excel-field') {
          createFieldElement(data.name, e.clientX, e.clientY);
        }
      } catch (error) {
        console.log('Drop data not JSON, trying plain text');
        const text = e.dataTransfer.getData('text/plain');
        if (text) {
          createFieldElement(text, e.clientX, e.clientY);
        }
      }
    }

    function createFieldElement(fieldName, x, y) {
      const preview = document.getElementById('certificate-preview');
      const rect = preview.getBoundingClientRect();
      
      const element = document.createElement('div');
      element.className = 'draggable';
      element.textContent = `[${fieldName.toUpperCase()}]`;
      element.dataset.field = fieldName;
      
      // Position element
      element.style.left = `${x - rect.left}px`;
      element.style.top = `${y - rect.top}px`;
      element.style.fontSize = '16px';
      element.style.fontFamily = 'Inter';
      element.style.color = '#000000';
      
      preview.appendChild(element);
      makeElementDraggable(element);
      
      AppState.fieldElements.push(element);
    }

    function makeElementDraggable(element) {
      let isDragging = false;
      let startX, startY, offsetX, offsetY;

      element.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return;
        
        // Select element
        selectElement(element);
        
        isDragging = false;
        startX = e.clientX;
        startY = e.clientY;
        
        const rect = element.getBoundingClientRect();
        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;
        
        AppState.isDragging = true;

        function mouseMoveHandler(e) {
          if (!isDragging && (Math.abs(e.clientX - startX) > 5 || Math.abs(e.clientY - startY) > 5)) {
            isDragging = true;
          }
          
          if (isDragging) {
            const preview = document.getElementById('certificate-preview');
            const previewRect = preview.getBoundingClientRect();
            
            let newX = e.clientX - previewRect.left - offsetX;
            let newY = e.clientY - previewRect.top - offsetY;
            
            // Constrain to preview area
            newX = Math.max(0, Math.min(newX, previewRect.width - element.offsetWidth));
            newY = Math.max(0, Math.min(newY, previewRect.height - element.offsetHeight));
            
            element.style.left = `${newX}px`;
            element.style.top = `${newY}px`;
          }
        }

        function mouseUpHandler() {
          document.removeEventListener('mousemove', mouseMoveHandler);
          document.removeEventListener('mouseup', mouseUpHandler);
          AppState.isDragging = false;
        }

        document.addEventListener('mousemove', mouseMoveHandler);
        document.addEventListener('mouseup', mouseUpHandler);
      });
    }

    function selectElement(element) {
      // Remove selection from other elements
      AppState.fieldElements.forEach(el => el.classList.remove('selected'));
      
      // Select current element
      element.classList.add('selected');
      AppState.selectedElement = element;
      
      // Update style controls
      updateStyleControlValues();
    }

    function setupStyleControls() {
      const fontFamily = document.getElementById('font-family');
      const fontSize = document.getElementById('font-size');
      const fontSizeDisplay = document.getElementById('font-size-display');
      const fontColor = document.getElementById('font-color');
      const boldBtn = document.getElementById('bold-btn');
      const italicBtn = document.getElementById('italic-btn');
      const underlineBtn = document.getElementById('underline-btn');

      fontFamily.addEventListener('change', updateSelectedElementStyle);
      fontSize.addEventListener('input', (e) => {
        fontSizeDisplay.textContent = e.target.value;
        updateSelectedElementStyle();
      });
      fontColor.addEventListener('input', updateSelectedElementStyle);
      
      boldBtn.addEventListener('click', () => toggleStyle('fontWeight', 'bold', 'normal'));
      italicBtn.addEventListener('click', () => toggleStyle('fontStyle', 'italic', 'normal'));
      underlineBtn.addEventListener('click', () => toggleStyle('textDecoration', 'underline', 'none'));
    }

    function updateSelectedElementStyle() {
      if (!AppState.selectedElement) return;
      
      const fontFamily = document.getElementById('font-family').value;
      const fontSize = document.getElementById('font-size').value;
      const fontColor = document.getElementById('font-color').value;
      
      AppState.selectedElement.style.fontFamily = fontFamily;
      AppState.selectedElement.style.fontSize = `${fontSize}px`;
      AppState.selectedElement.style.color = fontColor;
    }

    function updateStyleControlValues() {
      if (!AppState.selectedElement) return;
      
      const style = window.getComputedStyle(AppState.selectedElement);
      
      document.getElementById('font-family').value = AppState.selectedElement.style.fontFamily || 'Inter';
      document.getElementById('font-size').value = parseInt(style.fontSize) || 16;
      document.getElementById('font-size-display').textContent = parseInt(style.fontSize) || 16;
      document.getElementById('font-color').value = rgbToHex(style.color) || '#000000';
    }

    function toggleStyle(property, activeValue, inactiveValue) {
      if (!AppState.selectedElement) return;
      
      const currentValue = AppState.selectedElement.style[property];
      AppState.selectedElement.style[property] = currentValue === activeValue ? inactiveValue : activeValue;
    }

    function addCertificateText() {
      const textArea = document.getElementById('certificate-text');
      const text = textArea.value.trim();
      
      if (!text) {
        showToast('Digite um texto primeiro', 'warning');
        return;
      }
      
      const preview = document.getElementById('certificate-preview');
      let textElement = preview.querySelector('#certificate-main-text');
      
      if (!textElement) {
        textElement = document.createElement('div');
        textElement.id = 'certificate-main-text';
        textElement.className = 'draggable';
        textElement.style.position = 'absolute';
        textElement.style.left = '50%';
        textElement.style.top = '60%';
        textElement.style.transform = 'translate(-50%, -50%)';
        textElement.style.textAlign = 'center';
        textElement.style.width = '80%';
        textElement.style.fontSize = '14px';
        textElement.style.fontFamily = 'Inter';
        textElement.style.color = '#000000';
        textElement.style.lineHeight = '1.6';
        textElement.style.whiteSpace = 'pre-wrap';
        
        preview.appendChild(textElement);
        makeElementDraggable(textElement);
        AppState.fieldElements.push(textElement);
      }
      
      textElement.innerHTML = text;
      showToast('Texto adicionado ao certificado');
    }

    function autoPositionFields() {
      const positions = [
        { x: '50%', y: '35%', transform: 'translate(-50%, -50%)' }, // Nome
        { x: '50%', y: '45%', transform: 'translate(-50%, -50%)' }, // Curso
        { x: '30%', y: '75%', transform: 'translate(-50%, -50%)' }, // Carga horária
        { x: '70%', y: '75%', transform: 'translate(-50%, -50%)' }, // Data
        { x: '50%', y: '85%', transform: 'translate(-50%, -50%)' }  // Unidade
      ];
      
      AppState.fieldElements.forEach((element, index) => {
        if (positions[index]) {
          const pos = positions[index];
          element.style.left = pos.x;
          element.style.top = pos.y;
          element.style.transform = pos.transform;
        }
      });
      
      showToast('Campos reposicionados automaticamente');
    }

    function deleteSelectedField() {
      if (!AppState.selectedElement) {
        showToast('Selecione um campo primeiro', 'warning');
        return;
      }
      
      AppState.selectedElement.remove();
      AppState.fieldElements = AppState.fieldElements.filter(el => el !== AppState.selectedElement);
      AppState.selectedElement = null;
      
      showToast('Campo removido');
    }

    function generateAIText() {
      const btn = document.getElementById('generate-text');
      const prompt = document.getElementById('ai-prompt').value;
      const originalText = btn.innerHTML;
      
      btn.disabled = true;
      btn.innerHTML = '<div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></div>Gerando...';
      
      setTimeout(() => {
        const generatedText = `Certificamos que [NOME] concluiu com êxito o curso de [CURSO], 
com carga horária de [CARGA_HORARIA] horas, realizado na unidade [UNIDADE], 
em [DATA_CONCLUSAO].

Este certificado atesta a participação e aproveitamento do participante.`;

        document.getElementById('certificate-text').value = generatedText;
        
        // Add to history
        addToAIHistory(prompt || 'Gerar texto padrão', generatedText.substring(0, 100) + '...');
        
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        showToast('Texto gerado com sucesso!');
      }, 2000);
    }

    function addToAIHistory(prompt, response) {
      const history = document.getElementById('ai-history');
      
      if (history.textContent.includes('Nenhuma conversa ainda')) {
        history.innerHTML = '';
      }
      
      const entry = document.createElement('div');
      entry.className = 'mb-3 p-2 bg-white rounded-lg text-xs';
      entry.innerHTML = `
        <div class="font-semibold text-gray-800 mb-1">Prompt:</div>
        <div class="text-gray-600 mb-2">${prompt}</div>
        <div class="font-semibold text-gray-800 mb-1">Resposta:</div>
        <div class="text-gray-600">${response}</div>
      `;
      
      history.appendChild(entry);
      history.scrollTop = history.scrollHeight;
    }

    // Utility functions
    function rgbToHex(rgb) {
      if (!rgb) return '#000000';
      const match = rgb.match(/\d+/g);
      if (!match) return '#000000';
      
      return '#' + match.slice(0, 3).map(x => {
        const hex = parseInt(x).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
      }).join('');
    }

    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
      };

      toast.className = `
        fixed top-4 right-4 z-50 px-6 py-3 text-white font-medium rounded-lg shadow-lg
        transform translate-x-full opacity-0 transition-all duration-300 ease-out
        ${colors[type] || colors.success}
      `;

      toast.textContent = message;
      document.body.appendChild(toast);

      setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
      }, 10);

      setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }

    </script>
    </body>
</html>