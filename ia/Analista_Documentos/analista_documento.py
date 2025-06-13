import os
import pdfplumber
from NetFactor import stars
from google import genai
from google.genai import types
import json
from pprint import pprint

global processamento_concluido
global IDProspect
global IDFile
global pdf_path

class processando_documento():
        def iniciaClasseProcessamento(self):
            texto = self.extrair_texto_pdf(pdf_path)
            process = self.insert_fileContent(texto)
            
            # Caso o arquivo tenha sido processado com sucesso, vou enviar para o analisador de documentos #
            if process == True:
                print(f"Arquivo {pdf_path} processado com sucesso")
                response = self.analiser_fileContent(texto)
                if(response is not None):
                    try:
                        if isinstance(response, dict):
                            pprint(response) 
                            self.insert_declarante_response(response) 
                            self.insert_bens_reponse(response) 
                            self.insert_onus_response(response)
                            if processamento_concluido == True:
                                self.update_processamento_concluido()
                        else:
                            print(f"JSON inválido: {response}")
                    except ValueError as e:
                        print(f"JSON inválido: {e}")
                        return False             
            else:
                print(f"Arquivo {pdf_path} não possui texto para processar")

        def extrair_texto_pdf(self,pdf_path):
            try:
             
                if not os.path.exists(pdf_path):
                    raise FileNotFoundError(f"Arquivo não encontrado: {pdf_path}")

                if not pdf_path.lower().endswith('.pdf'):
                    raise ValueError(f"Arquivo não é um PDF: {pdf_path}")

              
                if os.path.getsize(pdf_path) == 0:
                    raise ValueError(f"Arquivo PDF está vazio: {pdf_path}")

        
                with pdfplumber.open(pdf_path) as pdf:
                    texto = ""
                    for page in pdf.pages:
                        texto += page.extract_text() or "" 
                    return(texto)

            except Exception as e:
                print(f"Erro ao processar arquivo {pdf_path}: {str(e)}")
                return ""

        def insert_fileContent(self,texto):
            if texto != "":
                cnxn2 = stars.ConexaoProspect()
                cursor2 = cnxn2.cursor()
                query = "UPDATE stakeholdersProspectFile SET fileContent = ? WHERE fileProcessDate IS NULL AND idProspect = ?"
                cursor2.execute(query, texto, IDProspect)
                cnxn2.commit()
                cursor2.close()
                cnxn2.close()
                return True
            else:
                print("Não há texto para inserir")
                return False

        def analiser_fileContent(self,texto):
            try:
                client = genai.Client(api_key="AIzaSyDCzYqCYrv-VfxU0KiKih9GqdWhtb2YcSk")

                response = client.models.generate_content(
                    model="gemini-2.0-flash",
                    contents='''Analise o documento fornecido e identifique seu tipo, seguindo esta classificação:  
                                - **1** para IRRF (Imposto de Renda Retido na Fonte)  
                            - **2** para Faturamento  
                            - **3** para Contrato Social  

                            Analise documentos de Declaração de Bens e Direitos do IR, extraindo:
                                1. Identificação do declarante (CPF/CNPJ)
                                2. Listagem de bens com:
                                   - Classificação por tipo (Imóvel, Veículo, Investimento, etc.)
                                   - Valores comparativos anualizados
                                   - Metadados específicos por categoria
                                3. Estruture os dados conforme schema JSON fornecido, aplicando:
                                   - `null` para campos ausentes
                                   - Formatação padrão para valores monetários/datas
                                   - Tipagem automática baseada no conteúdo
                            **Retorne um JSON estruturado conforme o exemplo abaixo**, preenchendo os campos com base no documento. Se um campo não for aplicável ou não existir, retorne `null`.  

                            ### Requisitos:  
                            1. **Formato de dados**:  
                               - Datas: `"YYYY-MM-DD"` (ex: `"1990-05-20"`).  
                               - Valores monetários: números (ex: `2500.50`).  
                               - Booleanos: `1` (sim) ou `0` (não).  

                            2. **Estrutura do JSON**:  
                            ```json
                            {
                              "tipo_documento": 1,
                              "dados_declarante": {
                                "nome": "string ou null",
                                "data_nascimento_constituicao": "data ou null",
                                "Documento_declarante_cpf_cnpj": "string ou null",
                                "titulo_eleitoral": "string ou null",
                                "conjuge": "string ou null",
                                "cpf_conjuge": "string ou null",
                                "alteracao_dados_cadastrais": "1, 0 ou null",
                                "endereco": {
                                  "logradouro": "string ou null",
                                  "complemento": "string ou null",
                                  "cidade": "string ou null",
                                  "estado": "string ou null",
                                  "cep": "string ou null"
                                },
                                "natureza_ocupacao": "string ou null",
                                "ocupacao_principal": "string ou null"
                              },
                              "dados_declaracao": {
                                "exercicio_fiscal": "string ou null",
                                "rendimentos_tributacao_exclusiva": [
                                  {"codigo": "string (ex: '01')", "descricao": "string", "valor": "number"}
                                ],
                                "dividas_e_onus_reais":[
                                {
                                  "codigo": "number ou null",
                                  "discriminacao": "string ou null",
                                  "situacao_ano_anterior": "number ou null",
                                  "situacao_atual": "number ou null"
                                  "valor_pago": "number ou null"                                   
                                 }
                                ],
                               
                                "declaracao_de_bens_e_direitos":[
                                  {
                                    "grupo": "string",
                                    "codigo": "string",
                                    "discriminacao": "string",
                                    "situacao_ano_anterior": "number",
                                    "situacao_atual": "number"
                                  
                                  }
                                ] 
                              },
                            }
                Documento: ''' +texto,
                )

                # Removendo os marcadores ```json e \n no início e fim
                json_str = response.text.strip("```json\n").strip("\n```")

                #transformando o response em json
                json_response = json.loads(json_str)

                return json_response
            
            except Exception as e:
                print(f"Erro ao processar arquivo {pdf_path}: {str(e)}")
                return None
           
        def insert_declarante_response(self,json_response):
            try:
                dados = json_response
                # Extraindo os dados do declarante
                declarante = dados['dados_declarante']

                # Lista dos campos que queremos extrair
                campos = [
                    'Documento_declarante_cpf_cnpj',
                    'nome',
                    'data_nascimento_constituicao',
                    'endereco',
                    'natureza_ocupacao',
                    'ocupacao_principal',
                    'titulo_eleitoral',
                    'alteracao_dados_cadastrais',
                    'conjuge',
                    'cpf_conjuge'
                ]

                # Percorrendo os campos e imprimindo
                print("DADOS DO DECLARANTE:")
                print("=" * 40)

                #inserindo os dados do declarante no banco de dados
                for campo in campos:
                    valor = declarante.get(campo, "Campo não encontrado")
                    
                    # Formatação especial para o endereço
                    if campo == 'endereco' and isinstance(valor, dict):
                        print("\nENDEREÇO:")
                        for chave_end, valor_end in valor.items():
                            match chave_end:
                                case 'logradouro':
                                    logradouro = valor_end
                                case 'complemento':
                                    complemento = valor_end
                                case 'cidade':
                                    cidade = valor_end
                                case 'estado':
                                    estado = valor_end
                                case 'cep':
                                    cep = valor_end
                        
                        print(f"{chave_end.replace('_', ' ').title()}: {valor_end}")
                    else:
                        match campo:
                            case 'Documento_declarante_cpf_cnpj':
                                cpfCnpj = valor
                            case 'data_nascimento_constituicao':
                                dataNascimentoConstituicao = valor
                            case 'nome':
                                nome = valor
                            case 'natureza_ocupacao':
                                naturezaOcupacao = valor
                            case 'ocupacao_principal':
                                ocupacaoPrincipal = valor
                            case 'titulo_eleitoral':
                                tituloEleitor = valor
                            case 'alteracao_dados_cadastrais':
                                alteracaoCadastral = valor
                            case 'conjuge':
                                conjulgue = valor
                            case 'cpf_conjuge':
                                cpfConjuge = valor

                        print(f"{campo.replace('_', ' ').title()}: {valor}")
                
                    print("-" * 40)
            
                '''
                    create table prospectFileIrDeclarante(
                    	ID INT IDENTITY(1,1) PRIMARY KEY,
                    	IDFile int,
                    	nome varchar(255),
                    	dataNascimentoConstituicao date,
                    	cpfCnpj varchar(15),
                    	tituloEleitor varchar(15),
                    	conjulgue char(1),
                    	alteracaoCadastral char(1),
                    	logradouro varchar(200),
                    	complemento varchar(200),
                    	municipio varchar(200),
                    	uf char(2),
                    	cep varchar(8),
                    	naturezaOcupacao varchar(255),
                    	ocupacaoPrincipal varchar(255)
                    )
                '''
                if(conjulgue == "Sim"):
                    conjulgue = 1
                else:
                    conjulgue = 0

                cep = cep.replace("-", "")
                if(cpfConjuge != "" and cpfConjuge != None):
                    cpfConjuge = cpfConjuge.replace("-", "").replace(".", "")

                #inserindo os dados do declarante no banco de dados
                cnxn = stars.ConexaoProspect()
                cursor = cnxn.cursor()
                query = "INSERT INTO prospectFileIrDeclarante (IDFile, nome, dataNascimentoConstituicao, cpfCnpj, tituloEleitor, conjulgue, cpfConjulgue, alteracaoCadastral, logradouro, complemento, municipio, uf, cep, naturezaOcupacao, ocupacaoPrincipal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                print(query)
                cursor.execute(query, IDFile, nome, dataNascimentoConstituicao, cpfCnpj, tituloEleitor, conjulgue, cpfConjuge, alteracaoCadastral, logradouro, complemento, cidade, estado, cep, naturezaOcupacao, ocupacaoPrincipal)
                cnxn.commit()
                cursor.close()
                cnxn.close()
            except Exception as e:
                print(f"Erro ao inserir dados: {e}")
                processamento_concluido = False

        def insert_bens_reponse(self,json_response):
            try:
                dados = json_response
                # pprint(dados)

                for item in dados['dados_declaracao']['declaracao_de_bens_e_direitos']:
                    print("-" * 40)
                    print(item)
                    grupo = item['grupo']
                    codigo = item['codigo']
                    descricao = item['discriminacao']
                    situacaoAnoAnterior = item['situacao_ano_anterior']
                    situacaoAtual = item['situacao_atual']
                
                    print(f"Grupo: {grupo}")
                    print(f"Codigo: {codigo}")
                    print(f"Descrição: {descricao}")
                    print(f"Situação Ano Anterior: {situacaoAnoAnterior}")
                    print(f"Situação Atual: {situacaoAtual}")              
                    

                    # Inserindo no banco de dados #
                    cnxn = stars.ConexaoProspect()
                    cursor = cnxn.cursor()
                    query = "INSERT INTO prospectFileIrBens (IDFile, grupo, codigo, descricao, valorAnterior, valorAtual) VALUES (?, ?, ?, ?, ?, ?)"
                    cursor.execute(query, IDFile, grupo, codigo, descricao, situacaoAnoAnterior, situacaoAtual)
                    cnxn.commit()
                    cursor.close()
                    cnxn.close()

                    print("-" * 40)
            except Exception as e:
                print(f"Erro ao inserir dados: {e}")
                processamento_concluido = False

        def insert_onus_response(self,json_response):
            try:
                dados = json_response
                # pprint(dados)
                if(dados['dados_declaracao']['dividas_e_onus_reais'] is not None):
                    for item in dados['dados_declaracao']['dividas_e_onus_reais']:
                        print("-" * 40)
                        print(item)
                        codigo = item['codigo']
                        descricao = item['discriminacao']
                        situacaoAnoAnterior = item['situacao_ano_anterior']
                        situacaoAtual = item['situacao_atual']
                        valorPago = item['valor_pago']
                        grupo = "99"

                        print(f"Codigo: {codigo}")
                        print(f"Descrição: {descricao}")
                        print(f"Situação Ano Anterior: {situacaoAnoAnterior}")
                        print(f"Situação Atual: {situacaoAtual}")              
                        print(f"Valor Pago: {valorPago}")

                        #Inserindo no banco de dados #
                        cnxn = stars.ConexaoProspect()
                        cursor = cnxn.cursor()
                        query = "INSERT INTO prospectFileIrBens (IDFile, grupo, codigo, descricao, valorAnterior, valorAtual, valorPago) VALUES (?, ?, ?, ?, ?, ?, ?)"
                        cursor.execute(query, IDFile, grupo, codigo, descricao, situacaoAnoAnterior, situacaoAtual, valorPago)
                        cnxn.commit()
                        cursor.close()
                        cnxn.close()

                        print("-" * 40)
            except Exception as e:
                print(f"Erro ao inserir dados: {e}")
                processamento_concluido = False

        def update_processamento_concluido(self):
            cnxn = stars.ConexaoProspect()
            cursor = cnxn.cursor()
            query = "UPDATE stakeholdersProspectFile SET fileProcessDate = GETDATE(), fileProcess=1 WHERE ID = ?"
            cursor.execute(query, IDFile)
            cnxn.commit()
            cursor.close()
            cnxn.close()
    

# Buscando Arquivo no BD #
cnxn = stars.ConexaoProspect()
cursor = cnxn.cursor()
query = "SELECT * FROM stakeholdersProspectFile WHERE fileProcessDate IS NULL AND typeFile = 1"
result = cursor.execute(query)
for row in cursor:
    processamento_concluido = True
    IDProspect = row[1] 
    IDFile = row[0]
    pdf_path= r"\\srvinetcloud\\wwwroot\\homeBanking\\php\\" + row[3].replace("/", "\\")
    inicia = processando_documento()
    inicia.iniciaClasseProcessamento()




   