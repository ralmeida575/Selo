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

    // Exibe notificação com total de certificados e opção de enviar e-mails posteriormente
    window.showNotification = function(type, quantidade, certificados = []) {
        notificationContainer.className = 'notification-container ' + (type === 'success' ? 'success' : 'error');
        notificationContent.innerHTML = '';

        // Total de certificados
        const totalDiv = document.createElement('div');
        totalDiv.style.fontWeight = 'bold';
        totalDiv.textContent = `✅ ${quantidade} certificados gerados com sucesso!`;
        notificationContent.appendChild(totalDiv);

        // Se e-mails não foram enviados, mostra opção para enviar depois
        if (certificados.length > 0 && !document.getElementById('enviar_email').checked) {
            const emailDiv = document.createElement('div');
            emailDiv.style.marginTop = '10px';
            
            const emailButton = document.createElement('button');
            emailButton.className = 'btn-enviar-email';
            emailButton.textContent = 'Enviar e-mails agora';
            emailButton.onclick = () => enviarEmailsPosteriormente(certificados);
            
            emailDiv.appendChild(emailButton);
            notificationContent.appendChild(emailDiv);
        }

        notificationContainer.style.display = 'block';
        addCloseButton(notificationContainer);
    };

    // Função para enviar e-mails posteriormente
    async function enviarEmailsPosteriormente(certificados) {
        const loading = document.createElement('div');
        loading.textContent = 'Enviando e-mails...';
        loading.style.margin = '10px 0';
        notificationContent.appendChild(loading);

        try {
            const promises = certificados.map(cert => {
                return fetch('/enviar-email-posterior', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ hash: cert.hash })
                });
            });

            const results = await Promise.all(promises);
            const successful = results.filter(r => r.ok).length;

            loading.textContent = `E-mails enviados: ${successful}/${certificados.length}`;
            
            // Remove o botão após o envio
            const emailButton = notificationContent.querySelector('.btn-enviar-email');
            if (emailButton) emailButton.remove();
            
        } catch (error) {
            loading.textContent = 'Erro ao enviar e-mails: ' + error.message;
            console.error(error);
        }
    }

    // Exibe erros (mantido igual com pequenas melhorias)
    window.showErrorCertificates = function(erros) {
        errorContent.innerHTML = '';

        if (erros?.length > 0) {
            const ul = document.createElement('ul');
            erros.forEach(erro => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>Linha ${erro.linha}:</strong> ${erro.erro} <br> 
                                <small>Nome: ${erro.nome}, Curso: ${erro.curso}</small>`;
                ul.appendChild(li);
            });
            errorContent.appendChild(ul);
        } else {
            errorContent.textContent = 'Nenhum certificado com erro.';
        }

        errorContainer.style.display = 'block';
        addCloseButton(errorContainer);
    };

    // Adiciona botão de fechamento
    function addCloseButton(container) {
        if (!container.querySelector('.close-notification')) {
            const closeButton = document.createElement('span');
            closeButton.textContent = '×';
            closeButton.className = 'close-notification';
            closeButton.onclick = () => container.style.display = 'none';
            container.appendChild(closeButton);
        }
    }

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
                
                showNotification('success', result.quantidadeCertificados, certificadosComEmail);
                
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