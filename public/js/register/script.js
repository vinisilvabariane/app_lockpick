document.addEventListener('DOMContentLoaded', function () {
    'use strict'

    const form = document.getElementById('db-config-form')
    const feedback = document.getElementById('db-config-feedback')

    /**
     * Chama uma ação do router via fetch e valida a resposta JSON
     * @param {string} action - Ação passada para o Router
     * @param {RequestInit} [options={}] - Opções do fetch (method, headers, body, etc)
     * @returns {Promise<Object>} Retorna o payload JSON em caso de sucesso
     * @throws {Error} Quando a resposta não for válida ou success=false
     */
    const callAction = async (action, options = {}) => {
        const response = await fetch(`${window.location.pathname}?action=${action}`, options)
        const text = await response.text()
        let data
        try {
            data = JSON.parse(text)
        } catch {
            throw new Error(`Resposta inválida do servidor (HTTP ${response.status})`)
        }
        if (!response.ok || !data.success) {
            throw new Error(data.error || `HTTP ${response.status}`)
        }
        return data
    }

    /**
     Exibe um toast usando toastr ou console como fallback
     @param {string} type - Tipo do toast
     @param {string} message - Mensagem do toast
     @returns {void}
     */
    const showToast = (type, message) => {
        if (typeof toastr === 'undefined') {
            console[type === 'error' ? 'error' : 'log'](message)
            return
        }
        toastr.options = {
            closeButton: true, progressBar: true, newestOnTop: true, timeOut: 3000
        }
        toastr[type](message)
    }

    /**
     * Renderiza o retorno da API no elemento de feedback
     * @param {Object} payload - Dados retornados pela action
     * @returns {void}
     */
    const renderFeedback = (payload) => {
        if (!feedback) return
        feedback.textContent = JSON.stringify(payload, null, 2)
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault()
            const payload = {
                database_name: form.database_name.value.trim(), sql_definition: form.sql_definition.value.trim()
            }
            if (!payload.sql_definition) {
                showToast('error', 'Cole o CREATE TABLE na area DDL SQL.')
                return
            }
            callAction('save-config', {
                method: 'POST', headers: {
                    'Content-Type': 'application/json', Accept: 'application/json'
                }, body: JSON.stringify(payload)
            })
                .then((data) => {
                    renderFeedback(data)
                    showToast('success', data.details && data.details.message ? data.details.message : 'Configuracao salva com sucesso.')
                })
                .catch((err) => {
                    showToast('error', `Erro ao salvar configuracao: ${err.message}`)
                })
        })
    }
})
