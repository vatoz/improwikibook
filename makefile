all:
	php wiki.php
	pdflatex -interaction=nonstopmode  kniha.tex
	rm kniha.toc
	xpdf kniha.pdf
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
