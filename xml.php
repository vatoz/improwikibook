<?php
/*Stáhne z improligy xml dump
 * Používá k tomu soubor clanky.txt,
 * který při každém běhu obnovuje skript wiki.php
 * jsou v něm všechny články na které zatím aplikace narazila a 
 * všechny, na které existuje nějaký odkaz
 * Pokud je ve wiki nějaký článek či ostrov článků, ne které nic neodkazuje, zkuste je přidat ručně
 * To je asi případ zejména článků o uživatelích.
 * 
 *Jsou tu všechny "Chybějící články" , pokud nějaké přibydou, tak generováním wiki z nově staženého xml se přidají do seznamu 
 * 
 * Bylo by hezké mít tu seznam stránek získaný přímo z wiki, ale to je hudba budoucnosti. 
 * 
 * */


//<form method="post" action="/index.php?title=Speci%C3%A1ln%C3%AD:Exportovat_str%C3%A1nky&amp;action=submit">
//<br /><textarea name="pages" cols="40" rows="10"></textarea><br />
//<input name="curonly" type="checkbox" value="1" checked="checked" id="curonly" />&#160;<label for="curonly">Zahrnout jen současnou verzi, ne plnou historii</label><br />
//<input name="templates" type="checkbox" value="1" id="wpExportTemplates" />&#160;<label for="wpExportTemplates">Zahrnout šablony</label><br />
//<input name="wpDownload" type="checkbox" value="1" checked="checked" id="wpDownload" />&#160;<label for="wpDownload">Nabídnout uložení jako soubor</label><br />
//<input type="submit" value="Exportovat" title="[s]" accesskey="s" /></form></div>	



//set POST variables
$url = 'http://wiki.improliga.cz/index.php?title=Speci%C3%A1ln%C3%AD:Exportovat_str%C3%A1nky&amp;action=submit';

$fields = array();
$fields["pages"]=urlencode(file_get_contents("clanky.txt"));
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
