import os
from pdf2image import convert_from_path

filepath = r"C:\Projeto_Stars\06_2c4f8556_IR.pdf"
path_image = r"C:\Projeto_Stars"
poppler_path = r"C:\Projeto_Stars\Release-24.08.0-0\poppler-24.08.0\Library\bin"
# dirs = os.listdir(filepath)

images = convert_from_path(filepath, poppler_path=poppler_path)
i = 0 
for image in images:
    i+=1
    image.save(path_image+"\\"+str(i)+".jpeg")
