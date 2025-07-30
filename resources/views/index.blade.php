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
    .sidebar { width: 80px; background: #fff; display: flex; flex-direction: column; align-items: center; padding: 20px 0; box-shadow: 2px 0 6px rgba(0,0,0,0.1); }
    .sidebar img { width: 32px; height: 32px; margin: 20px 0; cursor: pointer; }
    .sidebar img.logo { width: 40px; height: 40px; margin-bottom: 40px; }

    /* ==== MAIN ==== */
    .main { flex: 1; display: flex; flex-direction: row; }

    /* ==== LATERAL ESQUERDA ==== */
    .form-column { width: 300px; background: #fff; padding: 20px; border-right: 1px solid #ddd; overflow-y: auto; }
    h1 { font-size: 20px; margin-bottom: 15px; }
    label { display: block; margin-top: 15px; font-size: 14px; }
    select, input[type="color"], input[type="file"], input[type="number"] { 
      width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px;
    }

    /* Colunas do Excel */
    .mapping-columns { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; background: #f8f8f8; padding: 8px; border-radius: 6px; min-height: 60px; }
    .column-item { padding: 6px 10px; background: #e5e7eb; border-radius: 4px; cursor: grab; font-size: 13px; }

    /* ==== PREVIEW ==== */
    .preview-column { 
      flex: 1; 
      display: flex; 
      flex-direction: column; 
      padding: 20px;
      overflow: auto;
    }
    
    .preview-wrapper {
      width: 800px;
      margin: 0 auto; /* Centraliza o preview */
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .preview-container { 
      position: relative; 
      width: 100%; 
      height: 565px; 
      border: 1px solid #ccc; 
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center; 
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      background-color: white;
    }
    
    .draggable { 
      position: absolute; 
      padding: 4px 8px; 
      cursor: move; 
      border: 1px dashed transparent; 
      user-select: none; 
      white-space: nowrap;
    }
    .draggable.selected { 
      border: 1px dashed #2563eb; 
      background: rgba(37,99,235,0.05); 
    }

    /* Controles de estilo dentro do mesmo container */
    .style-controls { 
      margin-top: 20px; 
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
      padding: 8px 12px; 
      border-radius: 4px; 
      cursor: pointer; 
      font-weight: bold; 
      flex: 1;
    }
    button:hover { 
      background: #1d4ed8; 
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
    <div class="form-column">
      <h1>Editor de Certificado</h1>
      <label>Upload do Excel:</label>
      <input type="file" id="excel-upload" accept=".xls,.xlsx" />

      <label>Campos do Excel (arraste para o certificado):</label>
      <div id="excel-columns" class="mapping-columns">
        <p style="color:#666;font-size:12px;">Faça upload do Excel para carregar as colunas...</p>
      </div>
    </div>

    <!-- PREVIEW + CONTROLES UNIFICADOS -->
    <div class="preview-column">
      <div class="preview-wrapper">
        <!-- Controles de estilo -->
        <div class="style-controls">
          <div class="style-row">
            <div class="style-group">
              <label>Selecione Template:</label>
              <select id="template">
                <option value="/storage/templates/template_certificado_1.jpg">Graduação Odontologia</option>
                <option value="/storage/templates/template_certificado_2.jpg">Pós-Odontologia</option>
                <option value="/storage/templates/template_certificado_3.jpg">SLMandic</option>
              </select>
            </div>
          </div>
          
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
  <button id="auto-position">Auto-Posicionar</button>
  <button id="delete-field">Remover Campo</button>
  <button id="save-layout">Salvar Layout</button>
</div>

<!-- Nova área de texto para descrição -->
<div style="margin-top: 15px;">
  <label for="descricao-certificado">Texto do Certificado:</label>
  <textarea id="descricao-certificado" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
Certificamos que [NOME] concluiu o curso de [CURSO], com carga horária de [CARGA HORARIA], na [UNIDADE].
  </textarea>
  <button id="add-descricao" style="margin-top: 8px; width:100%;">Adicionar/Atualizar Texto</button>
</div>

        <!-- Container do preview do certificado -->
        <div class="preview-container" id="preview"></div>
      </div>
    </div>
  </div>

  <script>
  const templateSelect = document.getElementById('template');
  const preview = document.getElementById('preview');
  const fontSelector = document.getElementById('font-selector');
  const fontSizeInput = document.getElementById('font-size');
  const fontColorInput = document.getElementById('font-color');
  const deleteFieldBtn = document.getElementById('delete-field');
  const excelUpload = document.getElementById('excel-upload');
  const excelColumns = document.getElementById('excel-columns');
  let selectedElement = null;

  // Posições pré-definidas para os campos
  const fieldPositions = {
    nome: { x: '50%', y: '40%', align: 'center', fontSize: 36 },
    curso: { x: '50%', y: '55%', align: 'center', fontSize: 24 },
    'carga horaria': { x: '30%', y: '70%', align: 'left', fontSize: 18 },
    'data conclusao': { x: '70%', y: '70%', align: 'right', fontSize: 18 },
    unidade: { x: '30%', y: '80%', align: 'left', fontSize: 18 },
    cpf: { x: '70%', y: '80%', align: 'right', fontSize: 18 }
  };

  // Atualiza o template no fundo do preview
  function atualizarPreview() {
    preview.style.backgroundImage = `url('${templateSelect.value}')`;
  }
  document.addEventListener('DOMContentLoaded', atualizarPreview);
  templateSelect.addEventListener('change', atualizarPreview);

  // Detecta colunas e faz o mapeamento automático
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

  // Insere campos automaticamente no preview
  function placeFieldsOnCertificate(mappedFields) {
    Object.keys(mappedFields).forEach(fieldName => {
      const layoutKey = mappedFields[fieldName];
      const position = fieldPositions[layoutKey];
      if (position) {
        const fieldElement = document.createElement('div');
        fieldElement.innerText = `*${fieldName}`;
        fieldElement.className = 'draggable';
        fieldElement.style.position = 'absolute';
        fieldElement.style.left = position.x;
        fieldElement.style.top = position.y;
        fieldElement.style.transform = 'translate(-50%, -50%)';
        fieldElement.style.fontSize = position.fontSize + 'px';
        fieldElement.style.fontFamily = fontSelector.value;
        fieldElement.style.color = fontColorInput.value;
        fieldElement.style.textAlign = position.align;
        preview.appendChild(fieldElement);
        makeDraggable(fieldElement);
      }
    });
  }

  // Upload Excel e carregamento de colunas
  excelUpload.addEventListener('change', async () => {
    const file = excelUpload.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    excelColumns.innerHTML = '<p style="color:#666;font-size:12px;">Carregando colunas...</p>';

    try {
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
        excelColumns.innerHTML = '';
        const mapped = mapExcelFields(data.colunas);
        if (data.status === 'success' && data.colunas?.length) {
  excelColumns.innerHTML = '';
  data.colunas.forEach(col => {
    const div = document.createElement('div');
    div.className = 'column-item';
    div.textContent = col;
    div.draggable = true;
    div.addEventListener('dragstart', e => e.dataTransfer.setData('text/plain', col));
    excelColumns.appendChild(div);
  });

  // 🔥 Salva o mapeamento e já auto-posiciona na tela
  window.lastMappedFields = mapExcelFields(data.colunas);
  placeFieldsOnCertificate(window.lastMappedFields);

} else {
  excelColumns.innerHTML = '<p style="color:red;">Nenhuma coluna encontrada no arquivo.</p>';
}


        // Adiciona botões de arrastar manual
        data.colunas.forEach(col => {
          const div = document.createElement('div');
          div.className = 'column-item';
          div.textContent = col;
          div.draggable = true;
          div.addEventListener('dragstart', e => e.dataTransfer.setData('text/plain', col));
          excelColumns.appendChild(div);
        });

        // Insere automaticamente os campos mapeados
        placeFieldsOnCertificate(mapped);
      } else {
        excelColumns.innerHTML = '<p style="color:red;">Nenhuma coluna encontrada no arquivo.</p>';
      }
    } catch (err) {
      console.error(err);
      excelColumns.innerHTML = '<p style="color:red;">Erro ao processar o upload.</p>';
    }
  });

  // Funções de arraste e estilo
  preview.addEventListener('dragover', e => e.preventDefault());
  preview.addEventListener('drop', e => {
    e.preventDefault();
    const fieldName = e.dataTransfer.getData('text/plain');
    const div = document.createElement('div');
    div.className = 'draggable';
    div.textContent = `*${fieldName}`;
    div.style.top = `${e.offsetY}px`;
    div.style.left = `${e.offsetX}px`;
    div.style.fontFamily = fontSelector.value;
    div.style.fontSize = `${fontSizeInput.value}px`;
    div.style.color = fontColorInput.value;
    preview.appendChild(div);
    makeDraggable(div);
  });

  preview.addEventListener('click', e => {
    if (e.target.classList.contains('draggable')) {
      if (selectedElement) selectedElement.classList.remove('selected');
      selectedElement = e.target;
      selectedElement.classList.add('selected');
      fontSelector.value = selectedElement.style.fontFamily;
      fontSizeInput.value = parseInt(selectedElement.style.fontSize);
      fontColorInput.value = rgbToHex(selectedElement.style.color);
    } else {
      if (selectedElement) selectedElement.classList.remove('selected');
      selectedElement = null;
    }
  });

  fontSelector.addEventListener('change', () => { if (selectedElement) selectedElement.style.fontFamily = fontSelector.value; });
  fontSizeInput.addEventListener('input', () => { if (selectedElement) selectedElement.style.fontSize = `${fontSizeInput.value}px`; });
  fontColorInput.addEventListener('input', () => { if (selectedElement) selectedElement.style.color = fontColorInput.value; });

  deleteFieldBtn.addEventListener('click', () => { if (selectedElement) { selectedElement.remove(); selectedElement = null; } });

  function makeDraggable(el) {
    let offsetX, offsetY;
    el.addEventListener('mousedown', e => {
      if (!el.classList.contains('selected')) {
        if (selectedElement) selectedElement.classList.remove('selected');
        selectedElement = el;
        el.classList.add('selected');
      }
      offsetX = e.offsetX; offsetY = e.offsetY;
      function mouseMoveHandler(ev) {
        el.style.left = `${ev.pageX - preview.offsetLeft - offsetX}px`;
        el.style.top = `${ev.pageY - preview.offsetTop - offsetY}px`;
      }
      function mouseUpHandler() {
        document.removeEventListener('mousemove', mouseMoveHandler);
        document.removeEventListener('mouseup', mouseUpHandler);
      }
      document.addEventListener('mousemove', mouseMoveHandler);
      document.addEventListener('mouseup', mouseUpHandler);
    });
  }

  function rgbToHex(rgb) {
    const result = rgb.match(/\d+/g).map(Number);
    return "#" + result.map(x => x.toString(16).padStart(2, '0')).join('');
  }
  // Botão Auto-Posicionar: reposiciona apenas os campos mapeados
document.getElementById('auto-position').addEventListener('click', () => {
  if (!window.lastMappedFields) return alert("Faça o upload do Excel primeiro!");

  // Remove apenas campos de Excel (que começam com '*')
  preview.querySelectorAll('.draggable').forEach(el => {
    if (el.innerText.startsWith('*')) el.remove();
  });

  // Reinsere os campos conforme o mapeamento padrão
  placeFieldsOnCertificate(window.lastMappedFields);

  // Reposiciona o texto descritivo (se existir)
  addDescricaoTexto(true);
});

// Quando o Excel for carregado e mapeado, salva o mapeamento globalmente
// Dentro do trecho onde mapeamos as colunas:
const mapped = mapExcelFields(data.colunas);

window.lastMappedFields = mapped; // <-- Aqui garantimos a referência global

// Função para adicionar ou atualizar o texto descritivo
document.getElementById('add-descricao').addEventListener('click', () => addDescricaoTexto());

function addDescricaoTexto(reposicionar = false) {
  const textValue = document.getElementById('descricao-certificado').value;
  let desc = preview.querySelector('#descricao-cert');

  if (!desc) {
    // Criar se não existir
    desc = document.createElement('div');
    desc.id = 'descricao-cert';
    desc.className = 'draggable';
    preview.appendChild(desc);
    makeDraggable(desc);
  }

  // Atualizar conteúdo e estilo
  desc.innerText = textValue;
  desc.style.position = 'absolute';
  desc.style.left = '50%';
  desc.style.top = '65%';
  desc.style.transform = 'translate(-50%, -50%)';
  desc.style.fontSize = '20px';
  desc.style.fontFamily = fontSelector.value;
  desc.style.color = fontColorInput.value;
  desc.style.textAlign = 'center';
  desc.style.width = '80%';
  desc.style.whiteSpace = 'pre-wrap';

  // Se reposicionar=true, força voltar ao centro
  if (reposicionar) {
    desc.style.left = '50%';
    desc.style.top = '65%';
    desc.style.transform = 'translate(-50%, -50%)';
  }
}

</script>

</body>
</html>
