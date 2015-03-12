<!DOCTYPE html>
<html >
<head>
<meta charset="UTF-8">     
    <title>Wikiconvert</title>
    </head><body>
<pre>
<?php
setlocale(LC_ALL,"cs_CZ.UTF8");
$links=explode("\n",file_get_contents("clanky.txt"));//viz xml.php

include "preg.php";
$TRANSPOSE=false;
function safe($t){

$t=rep_link($t);	
$t=rep_something($t);	
	
	//todo
//$t= str_replace("#","Q",$t);
//$t= str_replace("|","i",$t);
//$t= str_replace("_","u",$t);
//$t= str_replace("&","a",$t);
return $t;
}

function MediaWiki2Latex($title,$text){
	global $TRANSPOSE;
	ob_start();
	echo "\\needspace{5cm} ";
if (!$TRANSPOSE) echo "\section{".safe($title)."} ";
echo "\label{".safe(mb_strtolower($title,"UTF-8"))."} ";
$lastrow="";
foreach (explode("\n",$text) as $row){
$trimrow=trim($row);
//echo "DEBUG".$trimrow;
if(preg_match("~^[\*]{0,1}[[:space:]]{0,2}[\[]{0,1}http[s]{0,1}\:~",$trimrow)){
	//ignore youtube videos and internet hrefs
}
elseif(preg_match("~\=\=[[:space:]]{0,2}(Ukázková videa|Videa|Externí odkazy|Reference|Ukázky)[[:space:]]{0,2}\=\=~",$trimrow)){
	//ignore chapters
}


elseif(substr($trimrow,0,4)=="===="){
  echo $lastrow;$lastrow="";
  echo "\subsubsection{".safe(substr($trimrow,4,-4)  )."} "; //todo

}elseif(substr($trimrow,0,3)=="==="){
  echo $lastrow;$lastrow="";
  if($TRANSPOSE){
	  echo "\subsection{".safe(substr($trimrow,3,-3)  )."} ";

	  
	  }else{
  echo "\subsubsection{".safe(substr($trimrow,3,-3)  )."} ";
}
}

elseif(substr($trimrow,0,2)=="=="){
  echo $lastrow;$lastrow="";
  if($TRANSPOSE){
  echo "\section{".safe(substr($trimrow,2,-2)  )."} ";
	}else{
	echo "\subsection{".safe(substr($trimrow,2,-2)  )."} ";
		
		
		}}
elseif(strpos($trimrow,"REDIRECT")!==false){
ob_end_clean();

	return "";
}

elseif(strpos($trimrow,"PŘESMĚRUJ")!==false){
ob_end_clean();

	return "";
}
elseif(substr($trimrow,0,1)=="#"){
if($lastrow!= "\\end{enumerate}\n"){
  echo $lastrow;$lastrow="";
  echo "\\begin{enumerate}\n";
}

echo "\\item ".safe( substr($trimrow,1))  ."\n";  
$lastrow="\\end{enumerate}\n";
}

elseif(substr($trimrow,0,1)=="*"){
if($lastrow!= "\\end{itemize}\n"){
  echo $lastrow;$lastrow="";
  echo "\\begin{itemize}\n";
}
echo "\\item ".safe( substr($trimrow,1))  ."\n";  
$lastrow="\\end{itemize}\n";

}else{ 
  echo $lastrow;$lastrow="";
  echo safe($row)." \n";
}

}
echo $lastrow;


$result=ob_get_contents();
ob_end_clean();
if($title=="Emoce"){
	$result=preg_replace("~\<div [[:alnum:]\=".'\"'."\-\:\;]{1,120}\>~","\begin{multicols}{3}",$result);
	$result=preg_replace("~\<\/div\>~","\\end{multicols}",$result);
}

echo sablony($result,$title);
//echo $result;
}


if (!file_exists("wiki.xml")) include "xml.php";


$a= simplexml_load_file("wiki.xml"); 

$articles=array();
echo "simple _done ";
function atoa($a,$b){
global $articles;
$articles[trim($a)]=trim($b);
global $links;
$links[]=trim($a);
}



$defz=array(
  'kategoriez'=>"[[Kategorie:Zápasové kategorie]]",
  'kategoriei'=>"[[Kategorie:Kategorie na improshow]]",
  "pribeh"=>"{{Fáze příběhu}}",
  "fauly"=>"[[Kategorie:Fauly]]",
  "postavy"=>"[[Kategorie:Postavy]]",
  
  "terminologie"=>"[[Kategorie:Terminologie]]",
  "rozcvicky"=>"[[Kategorie:Rozcvičky]]",
  "cviceni"=>"[[Kategorie:Cvičení]]",
  'zpivane'=>"[[Kategorie:Zpívané kategorie]]",
  'porovnavaci'=>"[[Kategorie:Porovnávací kategorie]]",
  
);


$data=array(
);
foreach($defz as $key=>$Row){
	$data[$key]=array();//nucené pořadí skupin, jinak se generují do jiných, než je žádoucí
}


//var_export($a);
foreach ($a->page as $Page){
 atoa( $Page->title, $Page->revision->text[0]);
 foreach($defz as $r=>$k){
  if(strpos("  ".$Page->revision->text[0],$k    )){
  $data[$r][]=trim($Page->title);
  
  //break;
  }

} 

//$articles[trim($Page->title)]= $Page->revision->text[0];

}

function arrkeystotexlist($arr){
$t="\begin{itemize}\n";
foreach($arr as $Key){
$t.= "\item  \odkaz{".$Key."}{".mb_strtolower($Key,"UTF-8") ."} \n";
}
$t.="\\end{itemize}\n";
return $t;
	
	}
$articles["Seznam zpívaných kategorií"]="\label{:kategorie:zpívané kategorie}\n"
.arrkeystotexlist($data["zpivane"]);
unset($data["zpivane"]);

$articles["Porovnávací kategorie"]= $articles["Kategorie:Porovnávací kategorie"]. "\label{:kategorie:porovnávací kategorie}\n"
.arrkeystotexlist($data["porovnavaci"]);
$data["terminologie"][]="Porovnávací kategorie";
unset($data["porovnavaci"]);

function PutArrData($Seznam,$file){
  global $articles;

ob_start();  
foreach($Seznam as $Row){
	if(isset($articles[$Row])){
  MediaWiki2Latex($Row,$articles[$Row]);
  unset ($articles[$Row])	;}
}
file_put_contents($file,ob_get_contents());
ob_end_clean();
echo "File: ".$file." saved\n"; 
}

$d=strpos($articles["Kategorie"],"== Kam");
if($d>20){
	$articles["Kategorie"]=substr($articles["Kategorie"],0,$d);
}

$TRANSPOSE=true;	
PutArrData(array('Zápas'),"zapas.tex");
PutArrData(array('ImproWiki'),"uvod.tex");
PutArrData(array('Kategorie' ,'Kategorie:Zápasové kategorie'),"kategorie_start.tex");
PutArrData(array('Příběh'),"pribeh_start.tex");
PutArrData(array('Faul'),"fauly_start.tex");
PutArrData(array('Kategorie:Rozcvičky'),"rozcvicky_start.tex");
PutArrData(array('Předzápasový trénink'),"predzapasovy.tex");

$TRANSPOSE=false;


unset($articles["Kategorie:Krátké formy"]);
unset($articles["Kategorie:Žánry"]);
unset($articles["Kategorie:Cvičení"]);
unset($articles["Kategorie:Cvičení na rytmus"]);
unset($articles["Kategorie:Cvičení na fyzický kontakt"]);
unset($articles["Kategorie:Rozehřívačky"]);
unset($articles["Kategorie:Kratší formy"]);
unset($articles["Kategorie:Seznam kategorií"]);
unset($articles["Kategorie:Delší formy"]);
unset($articles["Kategorie:Formy"]);
unset($articles["Kategorie:Zpívané kategorie"]);
unset($articles["Kategorie:Terminologie"]);
unset($articles["Kategorie:Kategorie na improshow"]);
unset($articles["Kategorie:Porovnávací kategorie"]);
unset($articles["Kategorie:Fauly"]);

unset($articles["Julyen Hamilton"]);
unset($articles["Nátlak"]);
unset($articles["Hlavní strana"]);


$data["books"][]="Literatura";	
$data["books"][]="Improknihovnička";	
$data["authors"][]="Uživatel:VandaGabi";
$data["authors"][]="Uživatel:Just-paja";	
$data["authors"][]="Uživatel:Vatoz";	
$data["authors"][]="Uživatel:VojtechKopta";

$zapas=$data["kategoriez"];	


PutArrData (array("Expozice","Kolize","Krize","Peripetie","Katastrofa","Katarze"),"pribeh.tex"); //Custom řazení
unset($data["pribeh"]);

foreach ($data as $r=>$nonsense){
	"try ".$r." <br>";
	$t=$data[$r];
	asort($t,SORT_LOCALE_STRING);//todo
  PutArrData($t, $r.'.tex'  );  
}

//var_export(array_keys($articles));
PutArrData(array_keys($articles),"zbytek.tex");


function save_table($filename,$data,$element,$keys){
ob_start();
foreach($data as $Key=>$Row){
	echo "\\".$element."{".$Key."}{".
	mb_strtolower($Key,"UTF-8") ."}";
	foreach($keys as $subkey){
	echo "{";
	echo $Row[$subkey];
	echo "}";	
	}
	echo "\n";
	
	}
	file_put_contents($filename,ob_get_contents());
	ob_end_clean();
	echo "Saved ".$filename."\n";
}

foreach ($kategorie_boxtable as $Key=>$Row){
	$kategorie_boxtable[$Key]["zapasova"]=	(in_array($Key,$zapas)?"Z":"I");
}

ksort($kategorie_boxtable,SORT_LOCALE_STRING);
save_table("boxtable.tex",$kategorie_boxtable,"btbinfo",array("cas","hraci","tema","zapasova"));
save_table("faultable.tex",$faultable,"faulinfo",array("body","obrazek","gesto"));


$links=array_unique($links);
asort($links,SORT_LOCALE_STRING);

file_put_contents("clanky.txt",implode("\n",$links));
echo "Saved clanky.txt\n";


