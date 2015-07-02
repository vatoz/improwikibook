<?php
/*Stáhne z improligy xml dump
  */

define("CZK",
"říšňěžťčýůúěďóéáŘÍŠŇĚŽŤČÝÚŮĚĎÓÉÁ"
);

//<form method="post" action="/index.php?title=Specmakei%C3%A1ln%C3%AD:Exportovat_str%C3%A1nky&amp;action=submit">
//<br /><textarea name="pages" cols="40" rows="10"></textarea><br />
//<input name="curonly" type="checkbox" value="1" checked="checked" id="curonly" />&#160;<label for="curonly">Zahrnout jen současnou verzi, ne plnou historii</label><br />
//<input name="templates" type="checkbox" value="1" id="wpExportTemplates" />&#160;<label for="wpExportTemplates">Zahrnout šablony</label><br />
//<input name="wpDownload" type="checkbox" value="1" checked="checked" id="wpDownload" />&#160;<label for="wpDownload">Nabídnout uložení jako soubor</label><br />
//<input type="submit" value="Exportovat" title="[s]" accesskey="s" /></form></div>	



function load_list($url){
	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	//curl_setopt($ch,CURLOPT_POST, count($fields));
	//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	ob_start();
	$result = curl_exec($ch);

	

	$data= ob_get_contents();
	curl_close($ch);
	ob_end_clean();
	$p_start= strpos($data,'<td class="mw-allpages-nav">');
	$p_end=strpos($data,'<div class="printfooter">',$p_start);
	$data=substr($data,$p_start,$p_end -$p_start);
	preg_match_all("~\>([[:alnum:]\ \_\(\)\-\&\:\,\/".CZK."]*)\<~",$data,$result);

	return implode ("\n",$result[1]);	
}

$seznam="";
$urls=array(
"http://wiki.improliga.cz/index.php?title=Speci%C3%A1ln%C3%AD:Všechny_str%C3%A1nky",
"http://wiki.improliga.cz/index.php?title=Speciální%3AVšechny+stránky&namespace=2",
"http://wiki.improliga.cz/index.php?title=Speciální%3AVšechny+stránky&namespace=14");

foreach($urls as $url){
	$seznam.="\n".load_list ($url);	
}
file_put_contents("seznam.txt",$seznam);

//set POST variables
$url = 'http://wiki.improliga.cz/index.php?title=Speci%C3%A1ln%C3%AD:Exportovat_str%C3%A1nky&amp;action=submit';

$fields = array();
$fields["pages"]=urlencode($seznam);
$fields["curonly"]="checked";
$fields["wpDownload"]="checked";

			
$fields_string="";
//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//execute post
ob_start();
$result = curl_exec($ch);



$data= ob_get_contents();
curl_close($ch);
ob_end_clean();

//var_export(curl_getinfo($ch));

//close connection

if($result){
file_put_contents("wiki.xml",$data);
}
