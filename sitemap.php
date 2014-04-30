<?php
define('APPPATH', dirname(__FILE__));
require_once(APPPATH.'/application/library/Multicache.php');
require_once(APPPATH.'/application/library/Dbmysql.php');
$multicache = new Multicache();
$db = new Dbmysql('10.52.21.1','uf101web','uf101','uf101');

function add_url($loc,$lastmod,$changefreq,$priority){
  $str="<url><loc>".str_replace('&','&amp;',$loc)."</loc><lastmod>$lastmod</lastmod><changefreq>$changefreq</changefreq><priority>$priority</priority></url>";
  return $str;
}

header("Content-type: text/xml");

if(!isset($_GET['type'])){
echo '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$sitemaptypes = array(
                  array('article'),
                  array('news')
                );
foreach($sitemaptypes as $type)
  echo '<sitemap><loc>http://www.uf101.com/sitemap.php?type='.$type[0].'</loc><lastmod>'.date('Y-m-d').'</lastmod></sitemap>';

echo '</sitemapindex>';
exit;
}

echo '<?xml version="1.0" encoding="UTF-8" ?>';
if($_GET['type'] == 'news') {
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
}else{
echo '<urlset  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
}
$cache = $multicache->get($_GET['type'].'sitemapcache');
if($cache){
  echo $cache;
}
else{
   $data = '';
   switch($_GET['type']){
       case "article":
       $sql = sprintf(" SELECT a.* FROM articles AS a  WHERE a.closed=0 ORDER BY a.ctime DESC LIMIT 1000 ");
       $lists = $db->fetch_all($sql);
       foreach($lists as $key=>$row){
         $data.=add_url('http://www.uf101.com/articles/'.$row['id'].'-'.$row['link'].'-zh/',
                date('Y-m-d',$row['ctime']),'always','0.8');
       }
       break;
       case "news":
       $lastposttime = mktime(date("H"),date("i") ,0) - 86400*7;
       $sql = sprintf(" SELECT a.* FROM articles AS a  WHERE a.closed=0 AND a.ctime > %d ORDER BY a.ctime",$lastposttime);
       $lists = $db->fetch_all($sql);
       foreach($lists as $key=>$row){
         $data .='<url><loc>http://www.uf101.com/articles/'.$row['id'].'-'.$row['link'].'-zh/'.'</loc>
                 <news:news><news:publication><news:name>UF101故事</news:name><news:language>zh-cn</news:language>
                 </news:publication><news:publication_date>'.date('Y-m-d',$row['ctime']).'</news:publication_date>
                 <news:title>'.htmlspecialchars($row['zhtitle']).'</news:title><news:keywords>'.htmlspecialchars($row['zhsummary']).'</news:keywords>
                 </news:news></url>';
       }
       break;
   }
   $multicache->set($_GET['type'].'sitemapcache', $data);
   echo $data;

}
echo '</urlset>';

?>
