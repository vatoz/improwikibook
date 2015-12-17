
all:
	php wiki.php
	- pdflatex -interaction=nonstopmode  kniha.tex
	- pdflatex -interaction=nonstopmode  kniha.tex
	- pdflatex -interaction=nonstopmode  kniha.tex
all:logo.png

logo.png:
	wget wiki.improliga.cz/skins/logo.png


draft:
	php wiki.php
	- pdflatex -interaction=nonstopmode  kniha.tex
pexeso:
	php wiki.php
	- pdflatex -interaction=nonstopmode  pexeso.tex


	
clean:
	rm cviceni.tex
	rm fauly.tex
	rm kategoriez.tex
	rm kniha.aux
	rm kniha.log
	rm kniha.pdf
	rm kniha.toc
	rm pribeh.tex
	rm rozcvicky.tex
	rm terminologie.tex
	rm zapas.tex
	rm zbytek.tex
	rm kategoriei.tex
	rm books.tex
	rm authors.tex
	rm postavy.tex
	rm faultable.tex
	rm fauly_start.tex
	rm pribeh_start.tex
	rm rozcvicky_start.tex
	rm predzapasovy.tex
	rm uvod.tex
	rm kategorie_start.tex
	rm boxtable.tex
	rm priprava.tex
	rm rozcvickyshort.text
	rm logo.png
xml:
	-rm wiki.xml
	php xml.php
