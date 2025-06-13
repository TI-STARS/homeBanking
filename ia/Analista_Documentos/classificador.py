import os
import pdfplumber
from NetFactor import stars
from google import genai
from google.genai import types
import json
from pprint import pprint

global IDProspect
global pdf_path

class classificador_documentos():
        def inicia_classificacao(self):
            # Carregar o modelo de classificação
            texto = self.extrair_texto_pdf(pdf_path)

            if texto is not None or len(texto) > 300:
                # Chama classificação de documentos #
                dados = self.classificar_documento(texto)

                if dados is not None:
                    # Atualiza o tipo de documento no banco de dados #
                    self.update_tipo_documento(dados)

    
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
            
        def classificar_documento(self,texto):
            client = genai.Client(api_key="AIzaSyDCzYqCYrv-VfxU0KiKih9GqdWhtb2YcSk")

            response = client.models.generate_content(
                model="gemini-2.0-flash",
                contents='''Analise o documento fornecido e identifique seu tipo, seguindo esta classificação:  
                            - **1** para IR (Declaração de Imposto de Renda)  
                            - **2** para Faturamento  
                            - **3** para Contrato Social  
                            - **4** para Outros

                            Seja minuncioso na analise do documento, pois a classificação é muito importante para o sistema.
                            Não classifique o documento como outros, a menos que seja muito claro que não se encaixa nas outras categorias.
                            
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
                              "resumo_documento": "Resumo do documento"
                            }
                Documento: ''' +texto,
            )
             # Removendo os marcadores ```json e \n no início e fim
            json_str = response.text.strip("```json\n").strip("\n```")

            #transformando o response em json
            json_response = json.loads(json_str)
            
            return json_response
           
       
        def update_tipo_documento(self,dados):
            tipo_documento = dados['tipo_documento']
            resumo_documento = dados['resumo_documento']

            # Atualiza o tipo de documento no banco de dados #
            cnxn = stars.ConexaoProspect()
            cursor = cnxn.cursor()
            query = "UPDATE stakeholdersProspectFile SET typeFile = ?, typeResumo = ? WHERE IDProspect = ?"
            cursor.execute(query, (tipo_documento, resumo_documento, IDProspect))
            cnxn.commit()

# Buscando Arquivo no BD #
cnxn = stars.ConexaoProspect()
cursor = cnxn.cursor()
query = "SELECT * FROM stakeholdersProspectFile WHERE fileProcessDate IS NULL AND typeFile IS NULL "
result = cursor.execute(query)
for row in cursor:
    IDProspect = row[1] 
    pdf_path= r"\\srvinetcloud\\wwwroot\\homeBanking\\php\\" + row[3].replace("/", "\\")
    inicia = classificador_documentos()
    inicia.inicia_classificacao()




   