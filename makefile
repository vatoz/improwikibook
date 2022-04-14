
all:
	php wiki.php
	vlna cviceni.tex fauly.tex kategoriez.tex pribeh.tex  rozcvicky.tex  terminologie.tex  
	vlna zapas.tex  zbytek.tex  kategoriei.tex  books.tex  authors.tex  postavy.tex
	vlna  faultable.tex  fauly_start.tex m pribeh_start.tex  rozcvicky_start.tex 
	vlna predzapasovy.tex  uvod.tex kategorie_start.tex  boxtable.tex kniha.tex
	vlna priprava.tex rozcvickyshort.tex longformy.tex longformy.tex manual.tex stavba.tex
	- pdflatex -interaction=nonstopmode  kniha.tex
	- pdflatex -interaction=nonstopmode  kniha.tex
	- pdflatex -interaction=nonstopmode  kniha.tex

draft:
	php wiki.php
	- pdflatex -interaction=nonstopmode  kniha.tex



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
	rm rozcvickyshort.tex
	rm longformy.tex
	rm longformy.tex
	rm manual.tex
	rm stavba.tex
xml:
	-rm wiki.xml
	php xml.php
