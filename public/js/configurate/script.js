document.addEventListener('DOMContentLoaded', function () {
    'use strict'

    const registrySelect = document.getElementById('cfg-registry-select')
    const btnRefreshRegistries = document.getElementById('btn-refresh-registries')
    const btnLoadConfiguration = document.getElementById('btn-load-configuration')
    const btnSaveConfiguration = document.getElementById('btn-save-configuration')
    const btnResetConfiguration = document.getElementById('btn-reset-configuration')
    const columnsTbody = document.getElementById('cfg-columns-tbody')
    const cfgForm = document.getElementById('cfg-form')
    const cfgEmptyState = document.getElementById('cfg-empty-state')
    const cfgSelectedSummary = document.getElementById('cfg-selected-summary')
    const cfgEmailDomain = document.getElementById('cfg-email-domain')
    const cfgEmailPrefixSource = document.getElementById('cfg-email-prefix-source')

    let currentRegistryList = []
    let currentLoadedConfiguration = null
    let currentLoadedRegistryId = null

    const showToast = (type, message) => {
        if (typeof toastr === 'undefined') {
            console[type === 'error' ? 'error' : 'log'](message)
            return
        }
        toastr.options = {
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
            timeOut: 3000
        }
        toastr[type](message)
    }

    const callAction = async (action, options = {}) => {
        const response = await fetch(`${window.location.pathname}?action=${action}`, options)
        const text = await response.text()
        let data
        try {
            data = JSON.parse(text)
        } catch {
            throw new Error(`Resposta invalida do servidor (HTTP ${response.status})`)
        }
        if (!response.ok || !data.success) {
            throw new Error(data.error || `HTTP ${response.status}`)
        }
        return data
    }

    const sanitize = (value) => String(value || '').replace(/"/g, '&quot;')

    const setEmptyState = (show) => {
        if (!cfgEmptyState || !cfgForm) return
        cfgEmptyState.classList.toggle('d-none', !show)
        cfgForm.classList.toggle('d-none', show)
    }

    const updateSummary = (registry, updatedAt) => {
        if (!cfgSelectedSummary) return
        if (!registry) {
            cfgSelectedSummary.classList.add('d-none')
            cfgSelectedSummary.textContent = ''
            return
        }
        cfgSelectedSummary.classList.remove('d-none')
        cfgSelectedSummary.textContent = `Tabela selecionada: ${registry.database_name}.${registry.table_name}` +
            (updatedAt ? ` | Ultima atualizacao da configuracao: ${updatedAt}` : ' | Configuracao ainda nao salva.')
    }

    const renderRegistrySelect = (items) => {
        if (!registrySelect) return
        const options = ['<option value="">Selecione uma tabela...</option>']
        items.forEach((item) => {
            const status = item.has_configuration ? 'Configurada' : 'Sem configuracao'
            const label = `${item.database_name}.${item.table_name} (${status})`
            options.push(`<option value="${item.id}">${sanitize(label)}</option>`)
        })
        registrySelect.innerHTML = options.join('')
    }

    const populateEmailPrefixSourceOptions = (columns, selectedValue) => {
        if (!cfgEmailPrefixSource) return
        const options = ['<option value="">Selecione...</option>']
        if (Array.isArray(columns)) {
            columns.forEach((column) => {
                const name = String(column.name || '').trim()
                if (name === '') return
                const selected = selectedValue === name ? 'selected' : ''
                options.push(`<option value="${sanitize(name)}" ${selected}>${sanitize(name)}</option>`)
            })
        }
        cfgEmailPrefixSource.innerHTML = options.join('')
    }

    const renderSettings = (settings, columns) => {
        const emailDomain = settings && settings.email_domain
            ? String(settings.email_domain)
            : ''
        const emailPrefixSource = settings && settings.email_prefix_source
            ? String(settings.email_prefix_source)
            : ''

        if (cfgEmailDomain) {
            cfgEmailDomain.value = emailDomain
        }
        populateEmailPrefixSourceOptions(columns, emailPrefixSource)
    }

    const renderColumns = (columns) => {
        if (!columnsTbody) return
        if (!Array.isArray(columns) || columns.length === 0) {
            columnsTbody.innerHTML = '<tr><td colspan="8" class="text-muted">Nenhuma coluna encontrada.</td></tr>'
            return
        }

        const autoSourceOptions = [
            { value: 'none', label: 'Nenhuma' },
            { value: 'name_input', label: 'Nome informado' },
            { value: 'login_from_name', label: 'Login do nome' },
            { value: 'email_from_name', label: 'E-mail do nome' },
            { value: 'current_datetime', label: 'Data/hora atual' },
            { value: 'static_value', label: 'Valor estatico' }
        ]

        const html = columns.map((column, index) => {
            const autoOptions = autoSourceOptions.map((option) => {
                const selected = String(column.auto_source || '') === option.value ? 'selected' : ''
                return `<option value="${option.value}" ${selected}>${option.label}</option>`
            }).join('')

            const manualInputChecked = column.is_manual_input ? 'checked' : ''
            const defaultModeChecked = column.is_default_input ? 'checked' : ''
            const automaticChecked = column.is_automatic ? 'checked' : ''
            const insertAsNull = column.insert_as_null ? 'selected' : ''
            const insertAsNotNull = !column.insert_as_null ? 'selected' : ''
            const nullable = column.nullable !== false
            const nullSelectDisabled = nullable ? '' : 'disabled'
            const nullHint = nullable ? '' : 'title="Coluna NOT NULL nao permite insercao nula."'

            return `
                <tr data-index="${index}" data-nullable="${nullable ? '1' : '0'}">
                    <td class="fw-semibold">${sanitize(column.name)}</td>
                    <td><small>${sanitize(column.type || 'TEXT')}</small></td>
                    <td><input type="checkbox" class="form-check-input cfg-col-manual-input" ${manualInputChecked}></td>
                    <td><input type="checkbox" class="form-check-input cfg-col-default-mode" ${defaultModeChecked}></td>
                    <td>
                        <input type="checkbox" class="form-check-input cfg-col-automatic" ${automaticChecked}>
                    </td>
                    <td>
                        <select class="form-select form-select-sm cfg-col-insert-null" ${nullSelectDisabled} ${nullHint}>
                            <option value="0" ${insertAsNotNull}>Nao</option>
                            <option value="1" ${insertAsNull}>Sim</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm cfg-col-default"
                               value="${sanitize(column.default_value || '')}">
                    </td>
                    <td>
                        <select class="form-select form-select-sm cfg-col-auto-source">
                            ${autoOptions}
                        </select>
                    </td>
                </tr>
            `
        }).join('')

        columnsTbody.innerHTML = html
        bindModeBehavior()
    }

    const bindModeBehavior = () => {
        if (!columnsTbody) return
        const rows = columnsTbody.querySelectorAll('tr[data-index]')
        rows.forEach((row) => {
            const manualInput = row.querySelector('.cfg-col-manual-input')
            const defaultMode = row.querySelector('.cfg-col-default-mode')
            const automatic = row.querySelector('.cfg-col-automatic')
            const insertNull = row.querySelector('.cfg-col-insert-null')
            const autoSource = row.querySelector('.cfg-col-auto-source')
            const defaultInput = row.querySelector('.cfg-col-default')
            if (!manualInput || !defaultMode || !automatic || !autoSource || !defaultInput || !insertNull) return

            const syncState = () => {
                const isInsertNull = insertNull.value === '1'
                const nullable = String(row.getAttribute('data-nullable') || '0') === '1'

                if (!manualInput.checked && !defaultMode.checked && !automatic.checked) {
                    defaultMode.checked = true
                }

                if (manualInput.checked) {
                    if (nullable) {
                        insertNull.value = '0'
                    }
                    insertNull.disabled = true
                    autoSource.value = 'none'
                    autoSource.disabled = true
                    defaultInput.value = ''
                    defaultInput.disabled = true
                    return
                }

                if (defaultMode.checked) {
                    if (nullable) {
                        insertNull.value = '0'
                    }
                    insertNull.disabled = true
                    autoSource.value = 'none'
                    autoSource.disabled = true
                    defaultInput.disabled = false
                    return
                }

                if (nullable) {
                    insertNull.disabled = false
                }
                if (isInsertNull) {
                    autoSource.value = 'none'
                    autoSource.disabled = true
                    defaultInput.value = ''
                    defaultInput.disabled = true
                    return
                }
                autoSource.disabled = false
                if (autoSource.value === 'none') {
                    autoSource.value = 'name_input'
                }
                defaultInput.value = ''
                defaultInput.disabled = true
            }

            manualInput.addEventListener('change', () => {
                if (manualInput.checked) {
                    defaultMode.checked = false
                    automatic.checked = false
                }
                syncState()
            })
            defaultMode.addEventListener('change', () => {
                if (defaultMode.checked) {
                    manualInput.checked = false
                    automatic.checked = false
                }
                syncState()
            })
            automatic.addEventListener('change', () => {
                if (automatic.checked) {
                    manualInput.checked = false
                    defaultMode.checked = false
                }
                syncState()
            })
            insertNull.addEventListener('change', syncState)
            syncState()
        })
    }

    const collectColumns = () => {
        if (!columnsTbody || !currentLoadedConfiguration) return []
        const original = Array.isArray(currentLoadedConfiguration.columns)
            ? currentLoadedConfiguration.columns
            : []
        const rows = columnsTbody.querySelectorAll('tr[data-index]')
        const collected = []

        rows.forEach((row) => {
            const idx = Number(row.getAttribute('data-index'))
            const base = original[idx] || {}
            const manualInput = row.querySelector('.cfg-col-manual-input')
            const defaultMode = row.querySelector('.cfg-col-default-mode')
            const automatic = row.querySelector('.cfg-col-automatic')
            const insertNull = row.querySelector('.cfg-col-insert-null')
            const defaultInput = row.querySelector('.cfg-col-default')
            const autoSource = row.querySelector('.cfg-col-auto-source')
            const nullable = base.nullable !== false
            const insertAsNull = nullable && insertNull ? String(insertNull.value || '0') === '1' : false

            collected.push({
                name: String(base.name || ''),
                type: String(base.type || ''),
                is_manual_input: manualInput ? manualInput.checked : false,
                is_default_input: defaultMode ? defaultMode.checked : true,
                is_automatic: automatic ? automatic.checked : false,
                insert_as_null: insertAsNull,
                default_value: defaultInput ? String(defaultInput.value || '').trim() : '',
                auto_source: autoSource ? String(autoSource.value || 'none') : 'none',
                nullable: nullable
            })
        })

        return collected
    }

    const validateConfigurationBeforeSave = () => {
        if (!columnsTbody) return true
        const rows = columnsTbody.querySelectorAll('tr[data-index]')

        for (const row of rows) {
            const nameCell = row.querySelector('td')
            const fieldName = nameCell ? String(nameCell.textContent || '').trim() : 'campo'
            const automatic = row.querySelector('.cfg-col-automatic')
            const insertNull = row.querySelector('.cfg-col-insert-null')
            const autoSource = row.querySelector('.cfg-col-auto-source')

            if (!automatic || !insertNull || !autoSource) continue

            const isAutomatic = automatic.checked
            const isInsertNull = String(insertNull.value || '0') === '1'
            const source = String(autoSource.value || 'none')

            if (isAutomatic && !isInsertNull && source === 'none') {
                autoSource.focus()
                showToast('error', `Selecione a Origem da Informacao para o campo "${fieldName}".`)
                return false
            }
        }

        return true
    }

    const buildConfigurationPayload = () => {
        return {
            settings: {
                email_domain: cfgEmailDomain ? String(cfgEmailDomain.value || '').trim() : '',
                email_prefix_source: cfgEmailPrefixSource ? String(cfgEmailPrefixSource.value || '').trim() : ''
            },
            columns: collectColumns()
        }
    }

    const applyLoadedData = (data) => {
        const configuration = data && data.configuration ? data.configuration : { columns: [] }
        const registry = data && data.registry ? data.registry : null
        const meta = data && data.configuration_meta ? data.configuration_meta : null
        currentLoadedConfiguration = configuration
        renderSettings(configuration.settings || {}, configuration.columns || [])
        renderColumns(configuration.columns || [])
        updateSummary(registry, meta ? meta.updated_at : null)
        setEmptyState(false)
    }

    const loadRegistries = async () => {
        try {
            const response = await callAction('list-tables')
            const items = response.details && Array.isArray(response.details.data)
                ? response.details.data
                : []
            currentRegistryList = items
            renderRegistrySelect(items)
        } catch (error) {
            currentRegistryList = []
            renderRegistrySelect([])
            showToast('error', `Erro ao listar tabelas: ${error.message}`)
        }
    }

    const loadConfiguration = async () => {
        const selected = registrySelect ? Number(registrySelect.value || 0) : 0
        if (selected <= 0) {
            showToast('error', 'Selecione uma tabela para carregar configuracao.')
            return
        }

        try {
            const response = await callAction(`load-configuration&registry_id=${selected}`)
            const data = response.details && response.details.data ? response.details.data : null
            if (!data) {
                throw new Error('Dados de configuracao nao encontrados.')
            }
            currentLoadedRegistryId = selected
            applyLoadedData(data)
            showToast('success', 'Configuracao carregada com sucesso.')
        } catch (error) {
            setEmptyState(true)
            updateSummary(null, null)
            showToast('error', `Erro ao carregar configuracao: ${error.message}`)
        }
    }

    const saveConfiguration = async () => {
        if (!currentLoadedRegistryId || !currentLoadedConfiguration) {
            showToast('error', 'Carregue uma tabela antes de salvar.')
            return
        }
        if (!validateConfigurationBeforeSave()) {
            return
        }

        try {
            const payload = {
                registry_id: currentLoadedRegistryId,
                configuration: buildConfigurationPayload()
            }

            const response = await callAction('save-configuration', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify(payload)
            })

            const data = response.details && response.details.data ? response.details.data : null
            if (!data) {
                throw new Error('Resposta de salvamento sem dados.')
            }
            applyLoadedData(data)
            await loadRegistries()
            if (registrySelect) {
                registrySelect.value = String(currentLoadedRegistryId)
            }
            showToast('success', response.details.message || 'Configuracao salva com sucesso.')
        } catch (error) {
            showToast('error', `Erro ao salvar configuracao: ${error.message}`)
        }
    }

    const resetToLoaded = () => {
        if (!currentLoadedConfiguration) {
            showToast('error', 'Nenhuma configuracao carregada para restaurar.')
            return
        }
        renderSettings(currentLoadedConfiguration.settings || {}, currentLoadedConfiguration.columns || [])
        renderColumns(currentLoadedConfiguration.columns || [])
        showToast('success', 'Formulario restaurado para o ultimo estado carregado.')
    }

    if (btnRefreshRegistries) {
        btnRefreshRegistries.addEventListener('click', loadRegistries)
    }
    if (btnLoadConfiguration) {
        btnLoadConfiguration.addEventListener('click', loadConfiguration)
    }
    if (btnSaveConfiguration) {
        btnSaveConfiguration.addEventListener('click', saveConfiguration)
    }
    if (btnResetConfiguration) {
        btnResetConfiguration.addEventListener('click', resetToLoaded)
    }

    loadRegistries()
})
