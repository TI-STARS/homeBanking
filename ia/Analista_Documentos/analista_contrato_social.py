import os
import pdfplumber
from NetFactor import stars
from google import genai
from google.genai import types
import json
from pprint import pprint
import base64
import mimetypes
import pytesseract
from PIL import Image
from pdf2image import convert_from_path

global processamento_concluido
global IDProspect
global IDFile
global pdf_path

class processando_documento():
        def iniciaClasseProcessamento(self):
            
            self.tesseract_ocr(pdf_path)
            file_encode = self.codificando_file(pdf_path) 

            texto = self.envio_file_analise(file_encode)
           
        
        def envio_file_analise(self,texto):
            try:
                prompt ='''"Você é um assistente jurídico especializado em direito societário e análise de contratos. Sua tarefa é revisar minuciosamente um contrato social com base nos seguintes critérios:
                                ## Identificação das Partes e Dados Cadastrais ##
                                    ** Verifique se constam nome completo, documentos, endereço e qualificação dos sócios.
                                    ** Confira se a atividade econômica (objeto social) está claramente definida.
                                
                                ## Tipo Societário e Regime Tributário ##
                                    ** Identifique se é LTDA, SA, EI, etc.
                                    ** Verifique menção ao regime tributário (Simples Nacional, Lucro Presumido, etc.).
                                
                                ## Capital Social ##
                                    ** Analise o valor total e a divisão de quotas/ações.
                                    ** Confira as regras de integralização (dinheiro, bens, serviços).
                                    ** Gestão e Tomada de Decisão
                                    ** Identifique os administradores e seus poderes.
                                    ** Extraia as regras para assembleias/votações (quórum, maioria necessária).
                                    ** Distribuição de Lucros e Prejuízo.
                                    ** Verifique como os lucros são distribuídos e se há retenções.
                                 
                                ## Entrada/Saída de Sócios ##
                                    ** Analise cláusulas de venda de participação, direito de preferência, tag-along, drag-along.
                                 
                                ## Dissolução e Liquidação ##
                                    ** Identifique em quais casos a empresa pode ser encerrada.
                                 
                                ## Cláusulas Especiais ##
                                    ** Destaque restrições (ex.: não concorrência), penalidades e foro escolhido para disputas.
                                 
                                ## Conformidade Legal ##
                                    ** Sinalize possíveis inconformidades com o Código Civil ou legislação tributária.
                                 
                                ## Formato da Resposta: ##
                                 
                                    ** Para cada item, liste os trechos relevantes do contrato e faça uma análise concisa.
                                    ** Aponte omissões, riscos ou inconsistências.
                                    ** Sugira melhorias quando aplicável.
                                 
                                 Exemplo de Análise:
                                 *'Item 3 – Capital Social: O contrato estabelece um capital de R$ 100.000,00, dividido em quotas iguais entre os sócios. Entretanto, não há prazo claro para integralização, o que pode gerar insegurança jurídica (sugere-se adicionar um artigo especificando o cronograma).'*
                                 
                                 Importante: Seja objetivo, técnico e evite interpretações subjetivas sem base legal."'''
                
                mimeType = texto[0]
                file_base64 = texto[1]



                client = genai.Client(api_key="AIzaSyDCzYqCYrv-VfxU0KiKih9GqdWhtb2YcSk")
                response = client.models.generate_content(
                    model = "gemini-2.0-flash",
                    contents = [
                         {
                            'role': 'user',
                            'parts': [
                                { 'text': prompt },
                                {
                                    'inlineData': {
                                        'mimeType': mimeType,
                                        'data': file_base64
                                    }
                                }
                            ]
                        }
                    ]
                )

                print(response)               

                

                return response
            
            except Exception as e:
                print(f"Erro ao processar arquivo {pdf_path}: {str(e)}")
                return None
           
        def codificando_file(seld,file):
            mime_type, _ = mimetypes.guess_type(file)
            if mime_type is None:
                mime_type = 'application/octet-stream'  
    
            with open(file, 'rb') as file:
                encoded_bytes = base64.b64encode(file.read())
                encoded_string = encoded_bytes.decode('utf-8')
                
            
            return mime_type, encoded_string
                
            
            #return mime_type, base64_content
             
        def tesseract_ocr(self,lang='por'):
            extracted_texts = []
            try:

                # pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
                # 1. Primeiro tenta extrair texto diretamente (para PDFs não digitalizados)
                with pdfplumber.open(pdf_path) as pdf:
                    for page_num, page in enumerate(pdf.pages):
                        print(f"Tentando extrair texto direto da Página {page_num + 1}...")
                        text = page.extract_text()
                        if text:  # Se extraiu texto com sucesso
                            extracted_texts.append(text)
                            print(f"Texto extraído (diretamente) da Página {page_num + 1}:\n{text[:200]}...\n")
                        else:  # Se falhar, usa OCR (PDF digitalizado/imagem)
                            print(f"Falha na extração direta. Convertendo página {page_num + 1} para imagem...")
                            images = convert_from_path(
                                pdf_path,
                                first_page=page_num + 1,
                                last_page=page_num + 1,
                                dpi=300  # Resolução para OCR
                            )
                            if images:
                                text = pytesseract.image_to_string(images[0], lang=lang)
                                extracted_texts.append(text)
                                print(f"Texto extraído (OCR) da Página {page_num + 1}:\n{text[:200]}...\n")
        
                return extracted_texts
        
            except FileNotFoundError:
                print(f"Erro: O arquivo PDF '{pdf_path}' não foi encontrado.")
                return []
            except Exception as e:
                print(f"Ocorreu um erro durante o processamento do PDF: {e}")
                return []
       

        def update_processamento_concluido(self):
            # cnxn = stars.ConexaoProspect()
            # cursor = cnxn.cursor()
            # query = "UPDATE stakeholdersProspectFile SET fileProcessDate = GETDATE(), fileProcess=1 WHERE ID = ?"
            # cursor.execute(query, IDFile)
            # cnxn.commit()
            # cursor.close()
            # cnxn.close()
            pass
    

# Buscando Arquivo no BD #
cnxn = stars.ConexaoProspect()
cursor = cnxn.cursor()
query = "SELECT * FROM stakeholdersProspectFile WHERE fileProcessDate IS NULL AND typeFile = '3'"
result = cursor.execute(query)
for row in cursor:
    processamento_concluido = True
    IDProspect = row[1] 
    IDFile = row[0]
    pdf_path= r"\\srvinetcloud\\wwwroot\\homeBanking\\php\\" + row[3].replace("/", "\\")
    inicia = processando_documento()
    inicia.iniciaClasseProcessamento()




   