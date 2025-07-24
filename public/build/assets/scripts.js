document.addEventListener('DOMContentLoaded', function() {
    // Elementos globais
    const notificationContainer = document.getElementById('notification-container');
    const notificationContent = document.getElementById('notification-content');
    const errorContainer = document.getElementById('error-container');
    const errorContent = document.getElementById('error-content');
    const messageElement = document.querySelector(".message");
    const loadingElement = document.querySelector("#loading");
    const form = document.querySelector("form");
    const emailCheckbox = document.getElementById('enviar_email');
    
    // Adiciona o checkbox de envio de e-mail se não existir
    if (!emailCheckbox && form) {
        const emailDiv = document.createElement('div');
        emailDiv.className = 'form-group';
        emailDiv.innerHTML = `
            <label for="enviar_email">
                <input type="checkbox" id="enviar_email" name="enviar_email" checked>
                Enviar e-mail para os participantes
            </label>
        `;
        form.insertBefore(emailDiv, form.querySelector('.form-group:last-child'));
    }

window.showNotification = function(quantidadeCertificados, certificados = []) {
  notificationContent.innerHTML = `
    <div class="result-panel">
      <div class="result-header">
        <span>✅ Processamento concluído</span>
        <span class="success-badge">${quantidadeCertificados} Certificados</span>
      </div>
      <p>Foram gerados com sucesso.</p>
    </div>
  `;
  
  // Mantenha a lógica do e-mail se necessário
  if (certificados.length > 0 && !document.getElementById('enviar_email').checked) {
    const emailDiv = document.createElement('div');
    emailDiv.innerHTML = `
      <div class="notification-detail">E-mails pendentes de envio</div>
      <button class="btn-enviar-email" onclick="enviarEmailsPosteriormente(${JSON.stringify(certificados)})">
        Enviar e-mails agora
      </button>
    `;
    notificationContent.appendChild(emailDiv);
  }
  
  notificationContainer.style.display = 'block';
};

window.showErrorCertificates = function(erros) {
  errorContent.innerHTML = `
    <div class="result-panel">
      <div class="result-header">
        <span>⚠️ Erros encontrados</span>
        <span class="error-badge">${erros.length} erro(s)</span>
      </div>
      
      ${erros.map(erro => `
        <div class="error-item">
          <div class="error-title">Linha ${erro.linha}: ${erro.erro}</div>
          <div class="error-detail">
            <span>Nome:</span>
            <span>${erro.nome}</span>
          </div>
          <div class="error-detail">
            <span>Curso:</span>
            <span>${erro.curso}</span>
          </div>
        </div>
      `).join('')}
    </div>
  `;
  errorContainer.style.display = 'block';
};
    // Envia o formulário (atualizado para incluir a opção de e-mail)
    window.enviarFormulario = async function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        const enviarEmail = document.getElementById('enviar_email').checked;
        formData.append('enviar_email', document.getElementById('enviar_email').checked ? '1' : '0');
        const token = document.querySelector('meta[name="csrf-token"]').content;

        messageElement.style.display = 'none';
        loadingElement.style.display = 'block';

        try {
            const response = await fetch("/gerar-certificados", {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": token }
            });

            const result = await response.json();
            loadingElement.style.display = 'none';
            
            if (response.ok) {
                messageElement.textContent = result.mensagem || 'Operação concluída!';
                messageElement.className = "message success-message";
                
                // Filtra apenas certificados com e-mail válido
                const certificadosComEmail = result.certificados?.filter(c => c.email) || [];
                
                if (response.ok) {
    messageElement.textContent = result.mensagem || 'Operação concluída!';
    messageElement.className = "message success-message";   
    
    const certificadosComEmail = result.certificados?.filter(c => c.email) || [];
    
    // CORREÇÃO AQUI - Remova o primeiro parâmetro 'success' que não é usado
    showNotification(result.quantidadeCertificados, certificadosComEmail);
    
    if (result.erros?.length > 0) {
        showErrorCertificates(result.erros);
    }
}
                
                if (result.erros?.length > 0) {
                    showErrorCertificates(result.erros);
                }
            } else {
                messageElement.textContent = result.erro || 'Erro ao processar.';
                messageElement.className = "message error";
                if (result.erros?.length > 0) {
                    showErrorCertificates(result.erros);
                }
            }
        } catch (error) {
            loadingElement.style.display = 'none';
            messageElement.textContent = 'Erro na conexão. Por favor, tente novamente.';
            messageElement.className = "message error";
            console.error(error);
        } finally {
            messageElement.style.display = 'block';
        }
    };

    // Atualiza o nome do arquivo selecionado
    window.updateFileName = function() {
        const fileInput = document.getElementById('file');
        const fileName = document.getElementById('file-name');
        fileName.textContent = fileInput.files[0]?.name || 'Selecione um arquivo...';
    };

    // Event listeners
    if (document.getElementById('file')) {
        document.getElementById('file').addEventListener('change', updateFileName);
    }
    
    if (form) {
        form.addEventListener('submit', window.enviarFormulario);
    }
});