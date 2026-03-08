/**
 * Controle do painel lateral (aside)
 * - Abre/fecha via botão toggle
 * - Abre via botão informativo
 * - Fecha via botão interno ou clique fora (desktop)
 */
const toggleAsideBtn = document.getElementById('toggleAside')
const asidePanel = document.getElementById('aside-panel')
const closeAsideBtn = document.getElementById('closeAside')

/**
 * Alterna o estado do painel lateral
 * @returns {void}
 */
const toggleAside = () => {
    if (!asidePanel) return
    asidePanel.classList.toggle('open')
}

/**
 * Fecha o painel lateral
 * @returns {void}
 */
const closeAside = () => {
    if (!asidePanel) return
    asidePanel.classList.remove('open')
}

if (toggleAsideBtn && asidePanel) {
    toggleAsideBtn.addEventListener('click', toggleAside)
}

const openAsideInfoBtn = document.getElementById('openAsideInfo')
if (openAsideInfoBtn && toggleAsideBtn) {
    openAsideInfoBtn.addEventListener('click', toggleAside)
}

if (closeAsideBtn && asidePanel) {
    closeAsideBtn.addEventListener('click', closeAside)
}

/**
 * Fecha o aside ao clicar fora dele (apenas desktop)
 */
document.addEventListener('click', (event) => {
    if (window.innerWidth <= 768) return
    if (!asidePanel || !toggleAsideBtn) return
    if (
        !asidePanel.contains(event.target) &&
        !toggleAsideBtn.contains(event.target)
    ) {
        closeAside()
    }
})