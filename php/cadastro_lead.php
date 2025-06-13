<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: ../login.html?error=not_logged_in');
        exit();
    }
    require '../config.php';
    require '../DBFunctions.php';
    require '../Functions.php';
    $conn = DbConect();
    $name_user = $_SESSION['nameUser'];
    $login = $_SESSION['username'];

   
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyAGfE-AUL2a_bfWgX5XXYqS0wSKJqoto&callback=console.debug&libraries=maps,marker&v=beta"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src='../js/functions.js'></script>
    <link rel="stylesheet" href="../css/style.css">
    <title> Cadastro Lead</title>
</head>
<body>
   

    <div class="sidebar">
        <div class="logo-container" style="text-align: center; width: 100%; margin-top: 10px;">
            <img src="../img/logo_scad_2.png" style="width: 75%; height: 75%;" alt="Logo" class="logo" >
        </div>
        <div class="sidebar-header">
            <h3>Bem vindo, <?=$name_user;?></h3>
            <div class="toggle-sidebar">
                <i class="fas fa-bars"></i>
            </div>
        </div>
        
        <ul class="sidebar-nav">
            <li>
                <a href="home.php">
                    <i class="fas fa-home"></i>
                    <span class="material-symbols-outlined">home</span>&ensp;<span>Home </span>
                </a>
            </li>
            <li class="active">
                <a href="cadastro_lead.php">
                    <i class="fas fa-chart-bar"></i>
                    <span class="material-symbols-outlined">description</span>&ensp;<span>Cadastro Lead</span>
                </a>
            </li>
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-chart-bar"></i>
                    <span class="material-symbols-outlined">bar_chart</span>&ensp;<span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-icons"></i>
                    <span class="material-symbols-outlined">exit_to_app</span>&ensp;<span>Sair</span>
                </a>
            </li> 
    </div>

    <div class="container">
        <div class="header">
            <h1>Cadastro de Lead</h1>
            <div class="breadcrumb"></div>
        </div>
        <div class="cadastro-section">
        <div style="display: flex; justify-content: space-between;">   
            <div> 
                <h3>Informações basicas</h3>
            </div>
            <div>
               <button onclick="gerarProspect()" class="btn success">Gerar Prospect</button>
            </div>
        </div>            
            <div class="form-row">
                <div class="form-group">
                    <label>Razão</label>
                    <input type="text" id="razao" name="razao" placeholder="Razão Social" disabled>
                </div>
                <div class="form-group">
                    <label>CNPJ*</label>
                    <input onchange="coletaDadosCNPJ(this.value)" type="text" name='cnpj' id='cnpj' placeholder="CNPJ" require>
                </div>
                <div class="form-group">
                    <label>Natureza Juridica</label>
                    <input type="text" name='natureza_juridica' id='natureza_juridica' placeholder="Natureza Juridica" disabled >
                </div>
                <div class="form-group">
                    <label>Data de Constituição</label>
                    <input type="date" name='data_contituicao' id='data_contituicao' disabled>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <input type="text" name='situacao' id='situacao' disabled>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <input type="text" name='tipo' id='tipo' disabled>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Atividade Principal</label>
                    <input type="text" id="atividade_principal" name="atividade_principal" placeholder="Atividade Principal" disabled>
                </div>
                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" id="endereco" name="endereco" placeholder="Endereço" disabled>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Complemento</label>
                    <input type="text" id="complemento" name="complemento" placeholder="complemento" disabled>
                </div>
                <div class="form-group">
                    <label>Bairro</label>
                    <input type="text" id="bairro" name="bairro" placeholder="Nome do Bairro" disabled>
                </div>
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" id="cidade" name="cidade" placeholder="Nome do Municipio" disabled>
                </div>
                <div class="form-group">
                    <label for="uf">Estado (UF)</label>
                    <select id="uf" name="uf" disabled>
                        <option value="">-- Selecione --</option>
                        <option value="AC">Acre (AC)</option>
                        <option value="AL">Alagoas (AL)</option>
                        <option value="AP">Amapá (AP)</option>
                        <option value="AM">Amazonas (AM)</option>
                        <option value="BA">Bahia (BA)</option>
                        <option value="CE">Ceará (CE)</option>
                        <option value="DF">Distrito Federal (DF)</option>
                        <option value="ES">Espírito Santo (ES)</option>
                        <option value="GO">Goiás (GO)</option>
                        <option value="MA">Maranhão (MA)</option>
                        <option value="MT">Mato Grosso (MT)</option>
                        <option value="MS">Mato Grosso do Sul (MS)</option>
                        <option value="MG">Minas Gerais (MG)</option>
                        <option value="PA">Pará (PA)</option>
                        <option value="PB">Paraíba (PB)</option>
                        <option value="PR">Paraná (PR)</option>
                        <option value="PE">Pernambuco (PE)</option>
                        <option value="PI">Piauí (PI)</option>
                        <option value="RJ">Rio de Janeiro (RJ)</option>
                        <option value="RN">Rio Grande do Norte (RN)</option>
                        <option value="RS">Rio Grande do Sul (RS)</option>
                        <option value="RO">Rondônia (RO)</option>
                        <option value="RR">Roraima (RR)</option>
                        <option value="SC">Santa Catarina (SC)</option>
                        <option value="SP">São Paulo (SP)</option>
                        <option value="SE">Sergipe (SE)</option>
                        <option value="TO">Tocantins (TO)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Cep</label>
                    <input type="number" id="cep" name="cep" placeholder="CEP" disabled>
                </div>  
                <div class="form-group">
                    <label>Numero</label>
                    <input type="number" id="numero" name="numero" placeholder="Numero" disabled>
                </div>
            </div>
        </div>

        <div class="cadastro-section">
            <h3>
                Consulta do Serasa
            </h3>
            <table id="sociosTable">
                <thead>
                    <tr>
                        <th>Consulta</th>
                        <th>Data</th>
                    </tr>
                    <tbody id="consultaSerasaBody">
                        
                    </tbody>
                </thead>
            </table>
           
        </div>

        <div class="cadastro-section">
            <h3>
                Lista de Sócios &nbsp;
            </h3>
            <table id="sociosTable">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Qualificação</th>
                    </tr>
                    <tbody id="sociosBody">
                        
                    </tbody>
                </thead>
            </table>
        </div>
        <div class="cadastro-section">
            <h3>
                Lista de Atividades Secundárias &nbsp;
            </h3>
            <table id="cnaeTable">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Descrição</th>
                    </tr>
                    <tbody id="cnaeBody">
                        
                    </tbody>
                </thead>
            </table>
        </div>
        <form name="form_gerar_prospect" id="form_gerar_prospect" method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="cnpj_prospect" id="cnpj_prospect">
            <div class="cadastro-section">
                <h3>Upload de arquivos</h3>
                <div style="display: flex; justify-content: space-between;">
                    <div class="file-upload-group">
                        <div class="form-row">
                            <div class="file-upload-item">
                                <label>Imposto de Renda (IR)</label>
                                <input type="file" name="file_ir" id="file_ir">
                                <small>Nenhum arquivo escolhido</small>
                            </div>
                            <div class="file-upload-item">
                                <label>Faturamento</label>
                                <input type="file" name="file_faturamento" id="file_faturamento">
                                <small>Nenhum arquivo escolhido</small>
                            </div>
                            <div class="file-upload-item">
                                <label>Escolher arquivo</label>
                                <input type="file" name="file_outras" id="file_outras">
                                <small>Nenhum arquivo escolhido</small>
                            </div>
                        </div>
                    </div>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <div id="street-view" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </form>
    </div>

    

    <div id="serasaModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <iframe name="serasaPainel"  frameborder="0" style="width:100%; height:100%;"></iframe>
        </div>
    </div>

    
    <FORM method="POST" id='serasaForm' name="obConsultaTela" action="http://srvaplicacao102:8080/netFactor/jsp/nfOrgaoCreditoConsultaSerasaTela.jsp" target="serasaPainel">
    	<input type=hidden name='concconciliacao' value=''>
    	<input type=hidden name='concingDocumento' value=''>
    	<input type=hidden name='concSeq' value=''>
    	<input type=hidden name='concingNatureza' value=''>
    	<input type=hidden name='conccccCodigo' value=''>
    	<input type=hidden name='concingVencimento' value=''>
    	<input type=hidden name='concingValorDeFace' value=''>
    	<input type=hidden name='concempCodigo' value=''>
    	<input type=hidden name='concpesCnpjCpf' value=''>
    	<input type=hidden name='conceveCodigo' value=''>
    	<input type=hidden name='concidgCodigo' value=''>
    	<input type=hidden name='concingDataConciliacao' value=''>
    	<input type=hidden name='concaux' value=''>
    	<input type=hidden name='concaux2' value=''>
    	<input type="hidden" name="obhAreaCodigo" value="0">
    	<input type="hidden" name="obhNomTelaTarifaCobranca" value="Tarifa de Cobrança">
    	<input type="hidden" name="obhNomTelaAdValorem" value="Ad Valorem">
    	<input type="hidden" name="obhNomTelaRecompra" value="Recompra">
    	<input type="hidden" name="obhNomTelaDMais" value="D+">
    	<input type="hidden" name="obhSenha" value="452424">
    	<input type="hidden" name="obhEmpCodigo" value="0">
    	<input type="hidden" name="obhNomTelaOutrosValores" value="Pendencia">
    	<input type="hidden" name="obhNomTelaFiador" value="DEVEDOR(ES) SOLIDÁRIO(S)">
    	<input type="hidden" name="obhNomTelaCedente" value="Cedente">
    	<input type="hidden" name="obhNomTelaDesagio" value="Deságio">
    	<input type="hidden" name="obhUsuario" value="natal">
    	<input type="hidden" name="obhNomTelaContratoMatriz" value="ContrM">
    	<input type="hidden" name="obhNomTelaFatorDesagio" value="Fator Dia">
    	<input type="hidden" name="obhNomTelaSacado" value="Sacado">
    	<input type="hidden" name="obhNomTelaJuros" value="Juros/Multa">
    	<input type="hidden" name="obhNomTelaContratoAditivo" value="TERMO DE CESSÃO">
    	<input type=hidden name="novaConsulta" value="false">
    	<input type=hidden name="acessoRemoto" value="false">
    	<input type=hidden name="SConsCnpjCpf" value="">
    	<input type=hidden name="ID" ID='idDoSerasa' value="">
    	<input type=hidden name="noMenu" value="true">
    </form>


       
    <script>    
        let alertSystemInitialized = false;
        let map;
        let panorama;
        let marker;
        var data={};

        
        function formatarData(data) {
            if (!data) return '';
            const cleanData = data.replace(/\D/g, '');
            
            if (cleanData.length !== 8) {
                return data; 
            }
            
            const ano = cleanData.substring(0, 4);
            const mes = cleanData.substring(4, 6);
            const dia = cleanData.substring(6, 8);
            
            return `${dia}/${mes}/${ano}`;
        }


        function consultaSerasa(isSer){
             document.getElementById('idDoSerasa').value = isSer;
             document.getElementById('serasaForm').submit();
             document.getElementById('serasaModal').style.display = 'block'; 
        }

        // Fechar modal quando clicar no X
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('serasaModal').style.display = 'none';
        });
        // Fechar modal quando clicar fora do conteúdo
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('serasaModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        function gerarProspect(){
            document.getElementById('form_gerar_prospect').submit();
        }

        function coletaDadosCNPJ(cnpj){            
            if(cnpj !=''){
                const formData = new FormData();
                formData.append('cnpj', cnpj);
                fetch('coletaDadosCNPJ.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if(data[0] == 'CNPJ já cadastrado.'){
                        const message = 'CNPJ já cadastrado.';
                        const type = 'info';
                        showAlert(message, type)
                    }else{
                        if(data['bd'] == '1'){
                             // Atribuição de valores \\
                            document.getElementById('razao').value = data['razao'];
                            document.getElementById('endereco').value = data['logradouro'];
                            document.getElementById('numero').value = data['numero'];
                            document.getElementById('bairro').value = data['bairro'];
                            document.getElementById('cidade').value = data['municipio'];
                            document.getElementById('cep').value = data['cep'].replace('-', '').replace('.', '');
                            document.getElementById('uf').value = data['uf'];
                            document.getElementById('complemento').value = data['complemento'];                
                            document.getElementById('data_contituicao').value = data['abertura'];
                            document.getElementById('natureza_juridica').value = data['natureza_juridica'];
                            document.getElementById('situacao').value = data['situacao'];
                            document.getElementById('tipo').value = data['tipo'];
                            document.getElementById('atividade_principal').value = data['atividade_principal'];
                            document.getElementById('cnpj_prospect').value = data['cnpj'];
                            
                            // consultaSerasa(data['idDoSerasa']);
                            
                            const sociosBody = document.getElementById('sociosBody');
                            const cnaeBody = document.getElementById('cnaeBody');
                            const consultaSerasaBody = document.getElementById('consultaSerasaBody');

                            while (consultaSerasaBody.firstChild) {
                                consultaSerasaBody.removeChild(consultaSerasaBody.firstChild);
                            }

                            while (sociosBody.firstChild) {
                                sociosBody.removeChild(sociosBody.firstChild);
                            }

                            while (cnaeBody.firstChild) {
                                cnaeBody.removeChild(cnaeBody.firstChild);
                            }

                            if(data['idDoSerasa'] && data['idDoSerasa'].length > 0) {
                                data['idDoSerasa'].forEach(consulta => {
                                    const row = document.createElement('tr');
                                    const idConsultaCell = document.createElement('td');
                                    idConsultaCell.innerHTML = '<a onclick="consultaSerasa(\'' + consulta['IdConsulta'] + '\')">Consulta: ' + consulta['IdConsulta'] + '</a>';

                                    row.appendChild(idConsultaCell);

                                    const dataConsulta = formatarData(consulta['dataConsulta']);
                                    
                                    const dataConsultaCell = document.createElement('td');
                                    dataConsultaCell.textContent = dataConsulta;
                                    row.appendChild(dataConsultaCell);
                                    consultaSerasaBody.appendChild(row);
                                });
                            }else {
                                
                                const row = document.createElement('tr');
                                const cell = document.createElement('td');
                                cell.colSpan = 2;
                                cell.textContent = 'Nenhum dado do serasa encontrado';
                                cell.className = 'text-center text-muted';
                                row.appendChild(cell);
                                consultaSerasaBody.appendChild(row);
                            }

                            if(data['socios'] && data['socios'].length > 0) {
                                data['socios'].forEach(socio => {
                                   
                                    const row = document.createElement('tr');
                                    const nomeCell = document.createElement('td');
                                    nomeCell.textContent = socio['Nome'];
                                    row.appendChild(nomeCell);
            
                                    const qualificacaoCell = document.createElement('td');
                                    qualificacaoCell.textContent = socio['Qualificacao'];
                                    row.appendChild(qualificacaoCell);
                                    sociosBody.appendChild(row);
                                });
                            } else {
                                // Caso não haja sócios
                                const row = document.createElement('tr');
                                const cell = document.createElement('td');
                                cell.colSpan = 2;
                                cell.textContent = 'Nenhum sócio encontrado';
                                cell.className = 'text-center text-muted';
                                row.appendChild(cell);
                                sociosBody.appendChild(row);
                            }
                            
                            if(data['cnae'] && data['cnae'].length > 0) {
                                data['cnae'].forEach(cnae => {
                                    const row = document.createElement('tr');
                                    const cnaeCodCell = document.createElement('td');
                                    cnaeCodCell.textContent = cnae['codigo'];
                                    row.appendChild(cnaeCodCell);

                                    const cnaeDescCell = document.createElement('td');
                                    cnaeDescCell.textContent = cnae['descricao'];
                                    row.appendChild(cnaeDescCell);
                                    cnaeBody.appendChild(row);
                                });
                            } else {
                               
                                const row = document.createElement('tr');
                                const cell = document.createElement('td');
                                cell.colSpan = 2;
                                cell.textContent = 'Nenhum Cnae encontrado';
                                cell.className = 'text-center text-muted';
                                row.appendChild(cell);
                                cnaeBody.appendChild(row);
                            }
                        
                            if(data['latitude'] && data['longitude']) {
                                updateMap(parseFloat(data['latitude']), parseFloat(data['longitude']));
                            } else {
                                console.warn('Coordenadas não disponíveis');
                                document.getElementById('map').style.display = 'none';
                                document.getElementById('street-view').style.display = 'none';
                            }
                            enviaMensagem(data);
                           // $('#navi-chat iframe').contentWindow.run(JSON.stringify(data),0,1);
                    }else{
                   
                        const partes = data['abertura'].split('/');
                        const dia = partes[0];
                        const mes = partes[1];
                        const ano = partes[2];
                        const dataFormatada = `${ano}-${mes}-${dia}`;
        
                        document.getElementById('razao').value = data['nome'];
                        document.getElementById('endereco').value = data['logradouro'];
                        document.getElementById('numero').value = data['numero'];
                        document.getElementById('bairro').value = data['bairro'];
                        document.getElementById('cidade').value = data['municipio'];
                        document.getElementById('cep').value = data['cep'].replace('-', '').replace('.', '');
                        document.getElementById('uf').value = data['uf'];
                        document.getElementById('complemento').value = data['complemento'];                
                        document.getElementById('data_contituicao').value = dataFormatada;
                        document.getElementById('natureza_juridica').value = data['natureza_juridica'];
                        document.getElementById('situacao').value = data['situacao'];
                        document.getElementById('tipo').value = data['tipo'];
                        document.getElementById('cnpj_prospect').value = data['cnpj'];
                        document.getElementById('atividade_principal').value = data['atividade_principal'][0].code + ' - ' + data['atividade_principal'][0].text;
                        
                        const sociosBody = document.getElementById('sociosBody');
                        const cnaeBody = document.getElementById('cnaeBody');
                        const consultaSerasaBody = document.getElementById('consultaSerasaBody');

                        while (sociosBody.firstChild) {
                            sociosBody.removeChild(sociosBody.firstChild);
                        }

                        while (cnaeBody.firstChild) {
                            cnaeBody.removeChild(cnaeBody.firstChild);
                        }

                        while (cnaeBody.firstChild) {
                                cnaeBody.removeChild(cnaeBody.firstChild);
                        }

                        if(data['idDoSerasa'] && data['idDoSerasa'].length > 0) {
                                data['idDoSerasa'].forEach(consulta => {
                                    const row = document.createElement('tr');
                                    const idConsultaCell = document.createElement('td');
                                    idConsultaCell.innerHTML = '<a onclick="consultaSerasa(\'' + consulta['IdConsulta'] + '\')">Consulta: ' + consulta['IdConsulta'] + '</a>';

                                    row.appendChild(idConsultaCell);

                                    const dataConsulta = formatarData(consulta['dataConsulta']);
                                    
                                    const dataConsultaCell = document.createElement('td');
                                    dataConsultaCell.textContent = dataConsulta;
                                    row.appendChild(dataConsultaCell);
                                    consultaSerasaBody.appendChild(row);
                                });
                            }else {
                                // Caso não haja sócios
                                const row = document.createElement('tr');
                                const cell = document.createElement('td');
                                cell.colSpan = 2;
                                cell.textContent = 'Nenhum dado do serasa encontrado';
                                cell.className = 'text-center text-muted';
                                row.appendChild(cell);
                                sociosBody.appendChild(row);
                            }

                            if(data['qsa'] && data['qsa'].length > 0) {
                                data['qsa'].forEach(socio => {
                                   
                                    const row = document.createElement('tr');
            
                                    const nomeCell = document.createElement('td');
                                    nomeCell.textContent = socio['nome'];
                                    row.appendChild(nomeCell);
            
                                    const qualificacaoCell = document.createElement('td');
                                    qualificacaoCell.textContent = socio['qual'];
                                    row.appendChild(qualificacaoCell);
                                    sociosBody.appendChild(row);
                                });
                            } else {
                                // Caso não haja sócios
                                const row = document.createElement('tr');
                                const cell = document.createElement('td');
                                cell.colSpan = 2;
                                cell.textContent = 'Nenhum sócio encontrado';
                                cell.className = 'text-center text-muted';
                                row.appendChild(cell);
                                sociosBody.appendChild(row);
                            }

                            if(data['atividades_secundarias'] && data['atividades_secundarias'].length > 0) {
                                data['atividades_secundarias'].forEach(cnae => {
                                    const row = document.createElement('tr');
                                    const cnaeCodCell = document.createElement('td');
                                    cnaeCodCell.textContent = cnae['code'];
                                    row.appendChild(cnaeCodCell);

                                    const cnaeDescCell = document.createElement('td');
                                    cnaeDescCell.textContent = cnae['text'];
                                    row.appendChild(cnaeDescCell);
                                    cnaeBody.appendChild(row);
                                });
                            } else {
                               
                                const row = document.createElement('tr');
                                const cell = document.createElement('td');
                                cell.colSpan = 2;
                                cell.textContent = 'Nenhum Cnae encontrado';
                                cell.className = 'text-center text-muted';
                                row.appendChild(cell);
                                cnaeBody.appendChild(row);
                            }


                        if(data['latitude'] && data['longitude']) {
                            updateMap(parseFloat(data['latitude']), parseFloat(data['longitude']));
                        } else {
                            console.warn('Coordenadas não disponíveis');
                            document.getElementById('map').style.display = 'none';
                            document.getElementById('street-view').style.display = 'none';
                        }
                        enviaMensagem(data);
                    }                    
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                const message = 'Ocorreu um erro ao Buscar dados do CNPJ.';
                const type = 'erro';
                    showAlert(message, type)
                });
            }
        }

        document.getElementById('cnpj').addEventListener('input', function(e) {
            const valorAtual = desformatarCNPJ(e.target.value);
            if (valorAtual.length <= 14) {
                e.target.value = formatarCNPJ(valorAtual);
            }
        });

        function updateMap(latitude, longitude) {
            const position = { lat: latitude, lng: longitude };
        
            if (map) {
                map.setCenter(position);
                marker.setPosition(position);
                panorama.setPosition(position);
                return;
            }
        
            // Caso contrário, inicialize tudo
            map = new google.maps.Map(document.getElementById("map"), {
                center: position,
                zoom: 14,
            });

            panorama = new google.maps.StreetViewPanorama(
                document.getElementById("street-view"),
                {
                    position: position,
                    pov: { heading: 34, pitch: 10 },
                }
            );

            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: "Localização",
            });

            map.addListener("click", (event) => {
                panorama.setPosition(event.latLng);
                marker.setPosition(event.latLng);
            });
        }

        loadNaviWidgetScript(function(){
            var mensageiroSrc = 'http://srvinetcloud/inet/defaultSM.php?fra=php/mensageiro-navi.php&TID=Cadlead&u=ZxCvBnM';
            inicializaWidgetNavi(mensageiroSrc);
        });
        window.initialize = initialize;

        function enviaMensagem(data){
            $('#navi-chat iframe')[0].contentWindow.run('Faça uma analise do seguinte CNPJ: '+JSON.stringify(data),0,1);
        }
       
        
    </script>
    <!-- Atualize seu script de carregamento da API para incluir o callback -->
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdZUSIyaY-RihUMfNkR5o4cBTcoxLxdiw&callback=initialize&libraries=maps,marker&v=beta"></script>
<?

    # Gravando dados em banco, e vinculando arquivos e cnpj em banco #
     if(isset($_POST['cnpj_prospect'])){
        $cnpj_prospect = $_POST['cnpj_prospect'];
        $cnpj_prospect = desformataCNPJ($cnpj_prospect);
        $saveSuccess = true;
        $file_ir = '';
        $file_faturamento = '';
        $file_outras = '';
        
        $query = "SELECT * FROM stakeholdersProspect WHERE cnpj = '$cnpj_prospect'";
        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
        if(DbNumRows($result)){
            ?>
            <script>
                const message = 'Esta empresa já pertence a outro usuário.';
                const type = 'erro';
                showAlert(message, type);
            </script>
            <?
        }else{
        

            if(isset($_FILES['file_ir']) && $_FILES['file_ir']['error'] === UPLOAD_ERR_OK){
                $nomeArquivo = gerarNomeUnicoParaArquivo();
                $tmp_name = $_FILES['file_ir']['tmp_name'];
                $file_ir = $nomeArquivo . '_IR.pdf';
                $destino = 'uploads/' . $file_ir;
                if(!move_uploaded_file($tmp_name, $destino)){
                    $saveSuccess = false;
                }
            } 

            if(isset($_FILES['file_faturamento']) && $_FILES['file_faturamento']['error'] === UPLOAD_ERR_OK){
                $nomeArquivo = gerarNomeUnicoParaArquivo();
                $tmp_name = $_FILES['file_faturamento']['tmp_name'];
                $file_faturamento = $nomeArquivo . '_Faturamento.pdf';
                $destino = 'uploads/' . $file_faturamento;
                if(!move_uploaded_file($tmp_name, $destino)){
                    $saveSuccess = false;
                }
            }

            if(isset($_FILES['file_outras']) && $_FILES['file_outras']['error'] === UPLOAD_ERR_OK){
                $nomeArquivo = gerarNomeUnicoParaArquivo();
                $tmp_name = $_FILES['file_outras']['tmp_name'];
                $file_outras = $nomeArquivo . '_Outras.pdf';
                $destino = 'uploads/' . $file_outras;
                if(!move_uploaded_file($tmp_name, $destino)){
                    $saveSuccess = false;
                }
            }

            $query = "INSERT INTO stakeholdersProspect VALUES ('$cnpj_prospect','$login','$file_ir', '$file_faturamento','1',GETDATE())";
            $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
            if(!DbNumRows($result) > 0){
                $saveSuccess = false;
            }
            
            $query = "SELECT TOP 1 ID AS [IDProspect] FROM stakeholdersProspect WHERE cnpj = '$cnpj_prospect' ORDER BY id DESC";
            $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
            if(DbNumRows($result) > 0){
                extract(DBFetchAssoc($result));
            }
           
            if($IDProspect > 0){
                $query = "INSERT INTO stakeholdersProspectFile (IDProspect,fileName,filePath) VALUES ('$IDProspect','$file_ir','$destino')";
                //print("<pre>".$query."</pre>");
                $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                if(!DbNumRows($result) > 0){
                    $saveSuccess = false;
                }
            }

            if($saveSuccess){
                ?>
                <script>
                    const message = 'CNPJ Cadastrado Com sucesso, estaremos processando os documentos.';
                    const type = 'success';
                    showAlert(message, type);
                </script>
                <?
            }else{
                ?>
                <script>
                    const message = 'Erro ao cadastrar CNPJ.';
                    const type = 'erro';
                    showAlert(message, type);
                </script>
                <?
            }   
        }
    }
?>


</body>
</html>