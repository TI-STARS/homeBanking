var queryString = location.search.slice(1);
var partes = queryString.split('&');
var _get = {};
partes.forEach(function (parte) {
    var chaveValor = parte.split('=');
    var chave = chaveValor[0];
    var valor = chaveValor[1];
    _get[chave] = valor;
});


/**
 * Formata um CNPJ adicionando pontos, barra e traço
 * @param {string} cnpj 
 * @returns {string} 
 */
function formatarCNPJ(cnpj) {
    const numeros = cnpj.replace(/\D/g, '');

    if (numeros.length !== 14) {
        return cnpj; 
    }
    
    return numeros.replace(
        /^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,
        '$1.$2.$3/$4-$5'
    );
}
/**
 * Remove a formatação de um CNPJ (pontos, barra e traço)
 * @param {string} cnpj
 * @returns {string} 
 */
function desformatarCNPJ(cnpj) {
    return cnpj.replace(/\D/g, '');
}



/**
 * Formata um CEP no padrão XXXXX-XXX
 * @param {string} cep - CEP sem formatação (apenas números)
 * @returns {string} CEP formatado (XXXXX-XXX)
 */
function formatarCEP(cep) {
    // Remove tudo que não é dígito
    const numeros = cep.replace(/\D/g, '');
    
    // Verifica se tem 8 dígitos
    if (numeros.length !== 8) {
        return cep; // Retorna original se não for CEP válido
    }
    
    // Aplica a formatação
    return numeros.replace(/^(\d{5})(\d{3})$/, '$1-$2');
}

/**
 * Remove a formatação de um CEP (remove o traço)
 * @param {string} cep - CEP formatado (XXXXX-XXX)
 * @returns {string} CEP sem formatação (apenas números)
 */
function desformatarCEP(cep) {
    // Remove todos os caracteres não numéricos
    return cep.replace(/\D/g, '');
}



function createAlertStyles() {
    if (document.getElementById('alertStyles')) return;
    const style = document.createElement('style');
    style.id = 'alertStyles';
    style.textContent = `
        .custom-alert-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); z-index: 999999; display: flex;
            justify-content: center; align-items: center; opacity: 0;
            visibility: hidden; transition: all 0.3s ease; backdrop-filter: blur(5px);
        }
        .custom-alert-overlay.show { opacity: 1; visibility: visible; }
        .custom-alert {
            background: white; padding: 30px; border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 400px; width: 90%;
            text-align: center; transform: scale(0.7) translateY(-50px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .custom-alert-overlay.show .custom-alert { transform: scale(1) translateY(0); }
        .custom-alert-icon { font-size: 48px; margin-bottom: 20px; display: block; }
        .custom-alert.success .custom-alert-icon { color: #28a745; }
        .custom-alert.warning .custom-alert-icon { color: #ffc107; }
        .custom-alert.error .custom-alert-icon { color: #dc3545; }
        .custom-alert.info .custom-alert-icon { color: #007bff; }
        .custom-alert-title { font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #333; }
        .custom-alert-message { font-size: 16px; color: #666; margin-bottom: 25px; line-height: 1.5; }
        .custom-alert-button {
            background: linear-gradient(45deg, #667eea, #764ba2); color: white; border: none;
            padding: 12px 30px; border-radius: 25px; cursor: pointer; font-size: 16px;
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .custom-alert-button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
    `;
    document.head.appendChild(style);
}

function createAlertHTML() {
    if (document.getElementById('alertOverlay')) return;
    const alertHTML = `
        <div class="custom-alert-overlay" id="alertOverlay">
            <div class="custom-alert" id="customAlert">
                <span class="custom-alert-icon" id="alertIcon">ℹ️</span>
                <div class="custom-alert-title" id="alertTitle">Alerta</div>
                <div class="custom-alert-message" id="alertMessage"></div>
                <button class="custom-alert-button" onclick="hideAlert()">OK</button>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHTML);
}

function initializeAlertSystem() {
    if (alertSystemInitialized) return;
    createAlertStyles();
    createAlertHTML();
    
    // Event listeners
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideAlert();
    });
    
    const overlay = document.getElementById('alertOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) hideAlert();
        });
    }
    
    alertSystemInitialized = true;
}

// FUNÇÃO PRINCIPAL
function showAlert(message, type = 'info') {
    initializeAlertSystem();
    
    const overlay = document.getElementById('alertOverlay');
    const alert = document.getElementById('customAlert');
    const icon = document.getElementById('alertIcon');
    const title = document.getElementById('alertTitle');
    const messageEl = document.getElementById('alertMessage');

    messageEl.textContent = message;
    alert.className = 'custom-alert ' + type;

    switch(type) {
        case 'success': icon.textContent = '✅'; title.textContent = 'Sucesso'; break;
        case 'warning': icon.textContent = '⚠️'; title.textContent = 'Atenção'; break;
        case 'error': icon.textContent = '❌'; title.textContent = 'Erro'; break;
        case 'info': default: icon.textContent = 'ℹ️'; title.textContent = 'Informação'; break;
    }

    overlay.classList.add('show');
    setTimeout(() => {
        const button = overlay.querySelector('.custom-alert-button');
        if (button) button.focus();
    }, 300);
}

function hideAlert() {
    const overlay = document.getElementById('alertOverlay');
    if (overlay) overlay.classList.remove('show');
}

// Tornar funções globais
window.showAlert = showAlert;
window.hideAlert = hideAlert;

function carregamentoJavaScript() {
    console.log("Javascript Carregado com sucesso.");
}


function alternarTable(){
    const tableSocios = document.getElementById('sociosTable');
    const icone1 = document.getElementById('icone1');
 
    if (tableSocios.classList.contains('expanded')) {
        tableSocios.classList.remove('expanded');
        tableSocios.classList.add('collapsed');
        icone1.textContent = 'expand_more'; 
    } else {
        tableSocios.classList.remove('collapsed');
        tableSocios.classList.add('expanded');
        icone1.textContent = 'expand_less'; 
    }
}

carregamentoJavaScript();

function loadNaviWidgetScript(callback) {
    var mensageiroSrc = 'http://srvinetcloud/inet/defaultSM.php?fra=php/mensageiro-navi.php&TID=Cadlead&u=ZxCvBnM';
    if (window.inicializaWidgetNavi) {
        // Já carregado
        if (callback) callback(mensageiroSrc);
        return;
    }
    var script = document.createElement('script');
    script.src = 'http://srvinetcloud/inet/js/navi-widget.js'; // ajuste o caminho se necessário
    script.onload = function() {
        if (callback) callback(mensageiroSrc);
    };
    document.head.appendChild(script);
}

function createPopupWin(pageURL, pageTitle, popupWinWidth, popupWinHeight) {
	var left = (screen.width - popupWinWidth) / 2 ;
	var top = (screen.height - popupWinHeight) / 2 ;
	if(popupWinWidth==0){
		var myWindow = window.open(pageURL, pageTitle, 'resizable=no, width=' + screen.availWidth + ', height=' + screen.availHeight + ', top=0, left=0');
	}
	else {
		var myWindow = window.open(pageURL, pageTitle, 'resizable=yes, width=' + popupWinWidth + ', height=' + popupWinHeight + ', top=' + top + ', left=' + left);
	}
}