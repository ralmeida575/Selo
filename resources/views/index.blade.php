<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editor de Certificados</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    @layer components {
      .template-thumb {
        @apply w-full h-[70px] bg-cover bg-center cursor-pointer transition-all duration-300 border-2 border-transparent rounded-xl hover:shadow-lg hover:scale-105;
      }
      .template-thumb.selected {
        @apply border-blue-500 shadow-lg shadow-blue-500/20 scale-105;
      }
      .draggable {
        @apply absolute px-3 py-1 cursor-move border border-transparent select-none max-w-[80%] transition-all duration-200 text-sm backdrop-blur-sm bg-white/80 rounded-md shadow-sm;
      }
      .draggable.selected {
        @apply border-blue-500 bg-blue-50/90 shadow-lg shadow-blue-500/20 ring-1 ring-blue-500/20;
      }
      .draggable:hover {
        @apply scale-105 shadow-md;
      }
      .preview-container.drop-active {
        @apply border-2 border-dashed border-blue-500 bg-blue-50/30 backdrop-blur-sm;
      }
      .excel-column {
        @apply bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-800 px-4 py-2.5 rounded-lg text-sm mb-2 cursor-pointer hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 border border-blue-200/50 shadow-sm hover:shadow-md transform hover:scale-[1.02];
      }
      .glass-morphism {
        @apply backdrop-blur-lg bg-white/80 border border-white/20 shadow-2xl;
      }
      .card-elevated {
        @apply bg-white rounded-2xl shadow-xl border border-gray-100/50 backdrop-blur-sm;
      }
      .btn-primary {
        @apply bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98];
      }
      .btn-secondary {
        @apply bg-white hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-6 rounded-xl border border-gray-200 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98];
      }
      .input-modern {
        @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white/50 backdrop-blur-sm hover:bg-white;
      }
      .sidebar-nav-item {
        @apply flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-700 transition-all duration-200 group cursor-pointer;
      }
      .sidebar-nav-item.active {
        @apply bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg;
      }
      .column-item {
        @apply bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-800 px-4 py-2.5 rounded-lg text-sm mb-2 cursor-pointer hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 border border-blue-200/50 shadow-sm hover:shadow-md transform hover:scale-[1.02];
      }
      .drag-handle {
        @apply w-3 h-3 rounded-full bg-blue-500 mr-2 cursor-move;
      }
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
background: linear-gradient(135deg, #f8fafc 0%, #dce3f0 100%);
      min-height: 100vh;
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
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
      backdrop-filter: blur(8px);
    }

    .preview-container.drop-active {
      border: 2px dashed #3b82f6;
      background-color: rgba(239, 246, 255, 0.3);
      backdrop-filter: blur(4px);
    }

    .workflow-step {
      @apply flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 hover:bg-white/50;
    }

    .workflow-step.completed {
      @apply bg-green-50 border border-green-200;
    }

    .workflow-step.active {
      @apply bg-blue-50 border border-blue-200;
    }

    .upload-area.active {
      @apply border-blue-500 bg-gradient-to-br from-blue-50 to-indigo-50;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
  </style>
</head>
<body class="font-sans text-gray-900 flex h-screen overflow-hidden">
  <!-- ASIDE (BARRA LATERAL ESQUERDA) -->
  <aside class="w-72 glass-morphism flex flex-col h-full m-4 mr-0 rounded-2xl">
    <div class="p-6 border-b border-white/20">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-800">CertEditor</h1>
          <p class="text-sm text-gray-600 leading-tight">Editor profissional de certificados</p>
        </div>
      </div>
    </div>
    
    <!-- Menu de navegação -->
    <nav class="flex-1 p-4 space-y-2">
      <a href="#" class="sidebar-nav-item active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
        </svg>
        Upload & Design
      </a>
      <a href="#" class="sidebar-nav-item">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
        </svg>
        Analytics
      </a>
      <a href="#" class="sidebar-nav-item">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Profile
      </a>
    </nav>
    
    <div class="p-4 border-t border-white/20 text-xs text-gray-500 text-center">
      v1.2.0 • © 2025
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
            <p class="text-sm text-gray-600 mt-1">Configure seu certificado profissional</p>
          </div>
          <div class="flex items-center space-x-3">
            <button id="save-layout" class="btn-primary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              Salvar
            </button>
            <button class="btn-secondary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Reload
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Workflow horizontal no topo -->
    <div class="glass-morphism m-4 mb-0 rounded-2xl">
      <div class="p-4">
        <div class="flex items-center justify-between space-x-4">
          <div class="workflow-step completed flex-1 text-center">
            <div class="w-6 h-6 bg-green-500 text-white text-xs rounded-full flex items-center justify-center font-semibold mx-auto">✓</div>
            <span class="text-sm font-medium text-gray-700">Selecionar design</span>
          </div>
          <div class="h-px bg-gray-300 flex-1"></div>
          <div class="workflow-step active flex-1 text-center">
            <div class="w-6 h-6 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center font-semibold mx-auto">2</div>
            <span class="text-sm font-medium text-gray-700">Configurar certificado</span>
          </div>
          <div class="h-px bg-gray-300 flex-1"></div>
          <div class="workflow-step flex-1 text-center">
            <div class="w-6 h-6 bg-gray-300 text-white text-xs rounded-full flex items-center justify-center font-semibold mx-auto">3</div>
            <span class="text-sm font-medium text-gray-500">Preview e emissão</span>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="flex-1 flex overflow-hidden p-4 space-x-4">
      <!-- LATERAL ESQUERDA (CONTROLES) -->
      <div class="w-80 card-elevated p-6 overflow-y-auto">
        <div class="space-y-8">
          <!-- Upload do Excel -->
          <div>
            <div class="flex items-center space-x-2 mb-4">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <label class="text-sm font-semibold text-gray-800">Dados do Excel</label>
            </div>
            <div class="upload-area border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center cursor-pointer transition-all duration-300 hover:border-blue-500 hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 group" id="upload-area">
              <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
              </div>
              <p class="text-sm font-medium text-gray-700 mb-2">Arraste seu arquivo Excel aqui</p>
              <p class="text-xs text-gray-500 mb-4">ou clique para selecionar</p>
              <input type="file" id="excel-upload" accept=".xls,.xlsx" class="hidden" />
              <button class="btn-secondary text-sm" onclick="document.getElementById('excel-upload').click()">
                Selecionar Arquivo
              </button>
              <p class="text-xs text-gray-400 mt-3">Suporte: .xls, .xlsx</p>
            </div>
          </div>

          <!-- Campos do Excel -->
          <div>
            <div class="flex items-center space-x-2 mb-4">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <label class="text-sm font-semibold text-gray-800">Campos Disponíveis</label>
            </div>
            <div id="excel-columns" class="mapping-columns bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-4 max-h-60 overflow-y-auto">
              <div class="flex items-center justify-center h-24 text-gray-400">
                <div class="text-center">
                  <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-xs">Faça upload do Excel para carregar as colunas</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Texto do Certificado -->
          <div class="mb-6">
            <label for="descricao-certificado" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-3">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span>Texto do Certificado</span>
            </label>
            <div id="descricao-certificado" class="w-full p-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white min-h-[120px] resize-none">
CERTIFICAMOS, por meio deste, que Raphael concluiu com êxito o curso de Odontologia, cumprindo uma carga horária total de 200 horas. De referido curso foi realizado na unidade de Campinas, com a data de conclusão registrada em 20 de julho de 2025. Agradecemos ao corpo docente pela excelência na condução das atividades acadêmicas, cuja dedicação e comprometimento foram fundamentais para a formação do aluno.
            </div>
            
            <button id="add-descricao" class="w-full btn-primary mt-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Adicionar/Atualizar Texto
            </button>
          </div>
        </div>
      </div>

      <!-- ÁREA CENTRAL DE PREVIEW -->
      <div class="flex-1 flex flex-col overflow-auto">
        <div class="card-elevated p-6 h-full">
          <!-- Barra de ferramentas moderna -->
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-4 mb-6 border border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
              <!-- Controles de fonte -->
              <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                  <label class="text-xs font-semibold text-gray-600 whitespace-nowrap">Fonte</label>
                  <select id="font-selector" class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                    <option value="Work Sans">Work Sans</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Arial">Arial</option>
                  </select>
                </div>

                <div class="flex items-center space-x-2">
                  <label class="text-xs font-semibold text-gray-600">Cor</label>
                  <div class="relative">
                    <input type="color" id="font-color" value="#000000" class="w-8 h-8 p-0 border-2 border-white rounded-lg cursor-pointer shadow-sm hover:shadow-md transition-shadow">
                  </div>
                </div>

                <div class="flex items-center space-x-2 min-w-[200px]">
                  <label class="text-xs font-semibold text-gray-600 whitespace-nowrap">Tamanho</label>
                  <input type="range" id="font-size-range" min="8" max="32" value="16" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                  <input type="number" id="font-size" min="8" max="52" value="16" class="w-14 text-xs border border-gray-300 rounded-lg px-2 py-1 text-center bg-white">
                </div>
              </div>

              <!-- Botões de formatação -->
              <div class="flex space-x-1 bg-white rounded-lg p-1 border border-gray-200">
                <button data-command="bold" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors font-bold text-sm" title="Negrito">B</button>
                <button data-command="italic" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors italic text-sm" title="Itálico">I</button>
                <button data-command="underline" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors underline text-sm" title="Sublinhado">U</button>
              </div>

              <!-- Botões de ação -->
              <div class="flex space-x-2 ml-auto">
                <button id="auto-position" class="px-4 py-2 text-xs bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium" title="Auto-Posicionar">
                  Posicionar
                </button>
                <button id="delete-field" class="px-4 py-2 text-xs bg-red-50 border border-red-200 rounded-lg text-red-600 hover:bg-red-100 transition-all duration-200 font-medium" title="Remover">
                  Remover
                </button>
              </div>
            </div>
          </div>

          <!-- Seleção de Template -->
          <div class="mb-6">
            <div class="flex items-center space-x-2 mb-4">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <h3 class="text-sm font-semibold text-gray-800">Template do Certificado</h3>
            </div>
            <select id="template" class="input-modern mb-4">
              <option value="https://images.unsplash.com/photo-1606868306217-dbf5046868d2?w=800&h=600">Template Clássico</option>
              <option value="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=800&h=600">Template Moderno</option>
              <option value="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=600" selected>Template Premium</option>
            </select>
            <div id="template-thumbnails" class="grid grid-cols-3 gap-3"></div>
          </div>

          <!-- Preview do certificado -->
          <div>
            <div class="flex items-center space-x-2 mb-4">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <h3 class="text-sm font-semibold text-gray-800">Preview do Certificado</h3>
            </div>
            <div class="preview-container border-2 border-gray-200 rounded-2xl bg-white w-full aspect-[4/3] relative overflow-hidden shadow-inner hover:shadow-lg transition-shadow duration-300" id="preview" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=600'); background-size: cover; background-position: center;">
              <div class="absolute inset-0 bg-gradient-to-br from-gray-50/30 to-transparent pointer-events-none"></div>
              <!-- Elementos arrastáveis serão inseridos aqui pelo JavaScript -->
            </div>
          </div>
        </div>
      </div>

      <!-- COLUNA DIREITA (IA) -->
      <div class="w-80 card-elevated p-6 overflow-y-auto">
        <div class="flex items-center space-x-3 mb-6">
          <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">IA Assistant</h3>
            <p class="text-sm text-gray-600">Geração inteligente de texto</p>
          </div>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-3">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span>Personalização do Texto</span>
            </label>
            <textarea id="prompt" rows="4" class="input-modern resize-none" placeholder="Descreva como você gostaria que o texto do certificado fosse personalizado..."></textarea>
            
            <div class="prompt-sugestoes flex flex-wrap gap-2 mt-4">
              <button onclick="usarSugestao('Deixe o texto mais formal.')" class="text-xs px-3 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-lg border border-blue-200 hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 font-medium">
                ✨ + Formal
              </button>
              <button onclick="usarSugestao('Adicione agradecimentos ao corpo docente.')" class="text-xs px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 rounded-lg border border-green-200 hover:from-green-100 hover:to-emerald-100 transition-all duration-200 font-medium">
                🙏 + Agradecimento
              </button>
              <button onclick="usarSugestao('Resuma o texto em uma linha.')" class="text-xs px-3 py-2 bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 rounded-lg border border-purple-200 hover:from-purple-100 hover:to-pink-100 transition-all duration-200 font-medium">
                📝 + Resumo
              </button>
            </div>
          </div>
          
          <div class="space-y-3">
            <button onclick="refinarTexto()" class="w-full btn-primary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Refinar com Prompt
            </button>
            <button onclick="gerarTextoCertificado()" id="gerar-texto-btn" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
              Gerar Novo Texto
            </button>
            <button onclick="limparHistorico()" class="w-full btn-secondary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Limpar Histórico
            </button>
          </div>
          
          <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-2xl p-4">
            <div class="flex items-center space-x-2 mb-3">
              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <h4 class="text-sm font-semibold text-gray-800">Histórico de Conversas</h4>
            </div>
            <div id="chat-historico" class="max-h-60 overflow-y-auto text-sm">
              <div class="flex items-center justify-center h-20 text-gray-400">
                <div class="text-center">
                  <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <p class="text-xs">Nenhuma conversa ainda</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

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

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const response = await fetch('/certificados/ler-colunas', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': csrfToken
        // NÃO defina 'Content-Type' aqui, o FormData já faz isso
      },
      credentials: 'include' // envia o cookie da sessão junto
    });

    const text = await response.text();

    let data;
    try {
      data = JSON.parse(text);
    } catch {
      console.error('Resposta não é JSON válido:', text);
      showToast('Erro na resposta do servidor. Verifique se está autenticado.', 'error');
      excelColumns.innerHTML = '<p style="color:#ef4444;font-size:12px;text-align:center;">Erro na resposta do servidor.</p>';
      return;
    }

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
  console.log("Objeto aluno completo no início da função:", state.excelData?.dados[0]);

  if (!state.excelData || !state.excelData.dados || !state.excelData.dados.length) {
    console.error('Excel está vazio ou mal formatado');
    showToast("Nenhum dado de Excel encontrado!", "error");
    return;
  }

  const aluno = state.excelData.dados[0];
  const promptExtra = document.querySelector("#prompt")?.value?.trim() || "";

  // Pegando os campos com possíveis espaços nas chaves
  const nome = (aluno['nome'] || aluno['nome '] || '').trim();
  const curso = (aluno['curso'] || '').trim();
  const cargaHoraria = aluno['carga horaria'] || '';
  const dataConclusaoRaw = aluno['data conclusão'] || aluno['data_conclusao'] || null;
  const unidade = (aluno['unidade'] || aluno['unidade '] || '').trim();
  const cpf = aluno['CPF'] || aluno['cpf'] || '';

  // Converter data do Excel para string ISO ou null
  const dataConclusao = dataConclusaoRaw ? excelSerialToDate(dataConclusaoRaw) : null;

  console.log('dataConclusaoRaw:', dataConclusaoRaw);
  console.log('dataConclusao (string):', dataConclusao);
  console.log('unidade (string limpa):', unidade);

  const dataConclusaoValida = !!dataConclusao;
  const unidadeValida = unidade && unidade.length > 0;

  if (!dataConclusaoValida || !unidadeValida) {
    showToast("Campos obrigatórios 'Data de Conclusão' e 'Unidade' precisam estar preenchidos e válidos.", "error");
    return;
  }

 const payload = {
    nome,
    curso,
    carga_horaria: cargaHoraria,
    data_conclusao: dataConclusao,
    unidade,
    cpf,
    prompt_extra: promptExtra  
};

  console.log("📤 Enviando payload:", payload);

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const response = await fetch('/certificados/gerar-texto', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(payload),
      credentials: 'include' // envia cookie da sessão para autenticação
    });

    const text = await response.text();
    console.log("Resposta bruta do servidor:", text);

    let data;
    try {
      data = JSON.parse(text);
    } catch {
      console.error("Resposta não é JSON:", text);
      showToast("Resposta inesperada do servidor", "error");
      return;
    }

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

// Funções para a IA
function usarSugestao(sugestao) {
  document.getElementById('prompt').value = sugestao;
}

function refinarTexto() {
  const prompt = document.getElementById('prompt').value;
  const historico = document.getElementById('chat-historico');

  if (!prompt) {
    alert('Por favor, digite um prompt para refinar o texto.');
    return;
  }

  const novoItem = document.createElement('div');
  novoItem.classList.add('p-2', 'border-b', 'border-gray-200');
  novoItem.innerHTML = `<strong>Você:</strong> ${prompt}`;
  historico.appendChild(novoItem);

  // Simulando resposta da IA
  setTimeout(() => {
    const resposta = document.createElement('div');
    resposta.classList.add('p-2', 'border-b', 'border-gray-200', 'bg-blue-50');
    resposta.innerHTML = '<strong>IA:</strong> Texto refinado com sucesso!';
    historico.appendChild(resposta);
    historico.scrollTop = historico.scrollHeight;

    // Atualizar o campo de texto com um exemplo refinado
    document.getElementById('descricao-certificado').innerHTML =
      'CERTIFICAMOS, por meio deste documento formal, que o aluno Raphael concluiu com êxito o curso de Odontologia, cumprindo integralmente a carga horária de 200 horas. O curso foi realizado na unidade de Campinas, com término em 20 de julho de 2025. Agradecemos especialmente ao corpo docente pela excelência acadêmica e dedicação na formação dos alunos.';
  }, 1000);
}

  
  function limparHistorico() {
    document.getElementById('chat-historico').innerHTML = '';
    document.getElementById('prompt').value = '';
  }
</script>
</body>
</html>