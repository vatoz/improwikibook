<?php
define("CZK",
"říšňěžťčýůúěďáóéŘÍŠŇĚŽŤČÝÚŮĚĎÁÓÉ"
);
define("TEXTUAL","[[:alnum:][:space:]\/".CZK."\.\:\&\(\)-,!-]");
function preg_mediawiki($text){
//$text=" [[pokus]]ný [[králík]] [[voda|vodník]]";
preg_match_all("~\[[\[]{0,1}([[:alnum:][:space:]\,\/\.\-\(\)\:\_\=\?\+".CZK."\#]+)([\|]{1,1}[[:alnum:][:space:]\,\(\)".CZK."]*){0,1}\][\]]{0,1}([[:alnum:]".CZK."]*)~",$text,$results);
//
return ($results);
}
function str_starts($haystack,$needle){
	return substr($haystack,0,strlen($needle))==$needle;


	}
function rep_something($t){
//věci co mají být tučně, poslední uvozovky jsou tu proto, aby se nepolykaly mezery
$t=preg_replace("~'''(".TEXTUAL."{1,45})'''~",'\\textbf{$1}',$t);
$t=preg_replace("~''(".TEXTUAL."{1,40})''~",'\\textit{$1}',$t);

$t=preg_replace("~\[\[Kategorie:[[:alnum:][:space:]".CZK."]+\]\]~",'',$t);
//$t=preg_replace("~'''(".TEXTUAL."{1,40})\\odkaz\{(".TEXTUAL."{1,40})\}\{(".TEXTUAL."{1,40})\}(".TEXTUAL."{1,40})'''~",'Ňuf',$t);
$t=preg_replace("~'''(".TEXTUAL."{1,40})\\\\odkaz\{(".TEXTUAL."{1,40})\}\{(".TEXTUAL."{1,40})\}(".TEXTUAL."{1,40})'''(".TEXTUAL."{1,40})~",'\\textbf{$1\\odkaz{$3}{$2}$4}$5',$t);
//'''Hrát v [[Hudební nálady|náladě]], která se hodí''' nebo aspoň neodporuje

//todo vylepšit odkaz na titulní stránky kategorií , asi samostatnou fcí.
$t=preg_replace("~\[\[:Kategorie:([[:alnum:][:space:]".CZK."]+)\|([[:alnum:][:space:]".CZK."]+)\]\]~",'\\textbf{$2}',$t);


$t=preg_replace("~\[\[Image\:([[:alnum:]\_\-]*)\.png[\|[:alnum:][:space:]".CZK."]*\|([[:alnum:][:space:]".CZK."\,]*)\]\]~",'\\obrazek{$1}{$2}',$t);
$t=preg_replace("~\[\[image\:([[:alnum:]\_\-]*)\.png[\|[:alnum:][:space:]".CZK."]*\|([[:alnum:][:space:]".CZK."\,]*)\]\]~",'\\obrazek{$1}{$2}',$t);


//$t=preg_replace("~ ([szvkai]{1,1}) ~",' $1~',$t);
$t=preg_replace("~\{\{todo\|([[:alnum:][:space:]\/".CZK."]{1,30})\}\}~",'\\todo{$1}',$t);
$t=preg_replace("~\<br[[:space:]\/]{0,10}\>~","\n\n",$t);
//$t=preg_replace("~\{\{todo\|([[:alnum:][:space:]\/".$czk."]{1,30})\}\}~",'\\todo{$1}',$t);


$t=preg_replace("~\<(cite|blockquote)[[:space:]]{0,10}\>~","\begin{quote}",$t);

$t=preg_replace("~\<\/(cite|blockquote)[[:space:]]{0,10}\>~","\\end{quote}",$t);
$t=preg_replace("~\<(references)[[:space:]]{0,10}\/\>~"," ",$t);
//$t=preg_rplace("~\[\[(Uživatel:[[:alnum:][:space:]\-".CZK."]+)\|([[:alnum:][:space:]".CZK."]+)\]\]~",'\\odkaz{$1}{$2}',$t);


$t=preg_replace("~\<ref[[:space:]]{0,10}\>~","\\footnote{",$t);
$t=preg_replace("~\^~","\^{}",$t);
$t=preg_replace("~\<\/ref[[:space:]]{0,10}\>~",'}',$t);

$t=preg_replace("~\"(".TEXTUAL."{1,40})\"~",'\\uv{$1} ',$t);
$t=preg_replace("~\" ~","\"{} ",$t);//mezera za uvozovkami
$t=str_replace("[[Image:Hlasovani.jpg|right|thumb|250px|Diváci hlasují kartičkami]]","",$t);
$t=str_replace("Seznam forem naleznete v \\odkaz{kategorii Formy}{:kategorie:formy}.","",$t);
$t=str_replace("[[Soubor:Beyond-belief-frakes.png|thumb|\"Vizuál seriálu Věřte, Nevěřte\"]]","",$t);
$t=str_replace("[https://cs.wikipedia.org/wiki/V%C4%9B%C5%99te_nev%C4%9B%C5%99te  Věřte, Nevěřte]","Věřte, nevěřte",$t);
$t=str_replace("[[:Kategorie:Warm-up|warm-up]]","\\odkaz{warm-up}{warm-upy}",$t);

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
		if(strpos($href,":")!==false){
			if(str_starts($href,"Uživatel:")){
					$result.="\\odkaz{".$title."}{".mb_strtolower($href,"UTF-8")."}";
				}elseif(str_starts($href,"Kategorie:")){

					//ignore
				}elseif(str_starts($href,":")){
					$result.="\\odkaz{".$title."}{".mb_strtolower($href,"UTF-8")."}";
				}elseif(str_starts($href,"http")){//todo protože : nemůžu zpracovávat v hlavním bloku
					$s=strpos($href," ");
					$result.="\\textbf{".substr($href,$s+1)."}";
				}elseif(str_starts($href,"https")){//todo protože : nemůžu zpracovávat v hlavním bloku
					$s=strpos($href," ");
					$result.="\\textbf{".substr($href,$s+1)."}";

				}elseif(str_starts($href,"Image:")){
						$result.="\obrazekmaly{".substr($href,6)."}";

			    }else{
					$result.="\\odkaz{".$title."}{".mb_strtolower($href,"UTF-8")."}";
					}

		}else{
			$result.="\\odkaz{".$title."}{".mb_strtolower($href,"UTF-8")."}";

		}
		$result.=substr($text,$start+strlen($link));
		$text=$result;
    }

	}
return $text;


}
$kategorie_boxtable=array();

$faultable=array();
function render_katabox($title,$data){
	global $kategorie_boxtable;
		$kategorie_boxtable[$title]=$data;
		return "\\katabox{".$data["tema"]."}{".$data["hraci"]."}{".$data["cas"]."}";

	}


function render_faulbox($title,$data){
	global $faultable;
		$faultable[$title]=$data;
		return "\\faulbox{".$data["obrazek"]."}{".$data["gesto"]."}{".$data["body"]."}";
	}




function sablona_params($text,$sablona,$params,$fce,$title){


	$dta="[\|[\s]{0,10}(".implode("|",$params) .")[\s]{0,10}\=[\s]{0,10}([[:alnum:]\,\\\\\{\}\+\-\s\:\/\.\*\(\)".CZK."]*)[\s]{0,10}]*";
preg_match_all("~\{\{[\s]*".$sablona."[\s]*".
str_repeat($dta,count($params))."\}\}~",$text,$results);



if(isset($results[0])){
	foreach($results[count($params)*2] as $index=>$rand){
		$start=strpos($text,$results[0][$index]);
		$result=substr($text,0,$start);
		$d=array();
		for ($i=1;$i<count($params)*2;$i+=1){
			$d[$results[$i][$index]]=$results[$i+1][$index];
			}
		$result.=$fce($title,$d);
		$result.=substr($text,$start+strlen($results[0][$index]));
		$text=$result;

    }

	}
	return $text;


	}
function sablony($text,$title){
		$text=sablona_params($text,"Kategorie",array("cas","hraci","tema"),"render_katabox",$title);
		$text=sablona_params($text,"Faul",array("obrazek","body","gesto"),"render_faulbox",$title);
	return $text;
}
