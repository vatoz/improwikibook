<?php
define("CZK",
"říšňěžťčýůúěďóéŘÍŠŇĚŽŤČÝÚŮĚĎÓÉ"
);
function preg_mediawiki($text){
//$text=" [[pokus]]ný [[králík]] [[voda|vodník]]";
preg_match_all("~\[\[([[:alnum:][:space:]\:\,\-\(\)\_".CZK."\#]+)([\|]{1,1}[[:alnum:][:space:]\(\)".CZK."]*)*\]\]([[:alnum:]".CZK."]*)~",$text,$results);
//
return ($results);
}
function rep_something($t){
$t=preg_replace("~'''([[:alnum:][:space:]\/".CZK."]{1,30})'''~",'\\textbf{$1}',$t);
$t=preg_replace("~\[\[Kategorie:[[:alnum:][:space:]".CZK."]+\]\]~",'',$t);

//todo vylepšit odkaz na titulní stránky kategorií , asi samostatnou fcí.
$t=preg_replace("~\[\[:Kategorie:([[:alnum:][:space:]".CZK."]+)\|([[:alnum:][:space:]".CZK."]+)\]\]~",'\\textbf{$2}',$t);


$t=preg_replace("~\[\[Image\:([[:alnum:]\_\-]*)\.png[\|[:alnum:][:space:]".CZK."]*\|([[:alnum:][:space:]".CZK."\,]*)\]\]~",'\\obrazek{$1}{$2}',$t);


//$t=preg_replace("~ ([szvkai]{1,1}) ~",' $1~',$t);
$t=preg_replace("~\{\{todo\|([[:alnum:][:space:]\/".CZK."]{1,30})\}\}~",'\\todo{$1}',$t);
$t=preg_replace("~\<br\>~","\n\n",$t);
//$t=preg_replace("~\{\{todo\|([[:alnum:][:space:]\/".$czk."]{1,30})\}\}~",'\\todo{$1}',$t);


$t=preg_replace("~\<(cite|blockquote)[[:space:]]{0,10}\>~","\begin{quote}",$t);

$t=preg_replace("~\<\/(cite|blockquote)[[:space:]]{0,10}\>~","\\end{quote}",$t);

//$t=preg_replace("~\[\[(Uživatel:[[:alnum:][:space:]\-".CZK."]+)\|([[:alnum:][:space:]".CZK."]+)\]\]~",'\\odkaz{$1}{$2}',$t);

return $t;	
	}
function rep_link($text){
$a=preg_mediawiki($text);
if(isset($a[0])){
	foreach($a[0] as $index=>$link){
		$start=strpos($text,$link);
		$result=substr($text,0,$start);
		$href=$a[1][$index];
		if(strpos($href,"#")){
			$href=substr($href,0,strpos($href,"#"));
			}
		$title=$a[1][$index];
		if($a[2][$index]!==""){
				$title=substr($a[2][$index],1);//oříznu | kterou mi tam preg nechalqti
		}
		if($a[3][$index]!==""){
				$title.=$a[3][$index];
		}
		
		$title=str_replace("_"," ",$title);
		$href=str_replace("_"," ",$href);
		$result.="\\odkaz{".$title."}{".strtolower($href)."}";
		$result.=substr($text,$start+strlen($link));
		$text=$result;
    }
	
	}
return $text;

	
}


function infoboxkategorie($text){
	$dta="[\|[\s]{0,10}(cas|hraci|tema)[\s]{0,10}\=[\s]{0,10}([[:alnum:]\,\+\-\s\:\/\.\*\(\)".CZK."]*)[\s]{0,10}]*";
preg_match_all("~\{\{[\s]*Kategorie[\s]*".$dta.$dta.$dta."\}\}~",$text,$results);


if(isset($results[0])){
	foreach($results[6] as $index=>$rand){
		$start=strpos($text,$results[0][$index]);
		$result=substr($text,0,$start);
		$d=array();
		$d[$results[1][$index]]=trim($results[2][$index]);
		$d[$results[3][$index]]=trim($results[4][$index]);
		$d[$results[5][$index]]=trim($results[6][$index]);
		$result.="\\katabox{".$d["tema"]."}{".$d["hraci"]."}{".$d["cas"]."}";
		$result.=substr($text,$start+strlen($results[0][$index]));
		$text=$result;
    }
	
	}



	return $text;
	}


echo rep_link(" [[pokus]]ný [[králík]] [[voda|vodník]]");
echo rep_link(" otlivé [[hráč]]e v [[tým]]ech a představ");
var_export (infoboxkategorie("{{Kategorie
|hraci=neomezeně
|tema=Problém pro každého hráče
|cas=neomezeně}}

{{Kategorie
|hraci=neomsssezeně
|tema=Problém ssspro každého hráče
|cas=neomezensssě}}

"));
