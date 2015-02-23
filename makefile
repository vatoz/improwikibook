all:
	php wiki.php
	- pdflatex -interaction=nonstopmode  kniha.tex
	- pdflatex -interaction=nonstopmode  kniha.tex
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
	
xml:
	-rm wiki.xml
	php xml.php
