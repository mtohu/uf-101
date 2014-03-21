<?php
/*
$page=new Page();
$page->getPaginationString( 14, 338, 30);
*/
class Page {
    
 function getPaginationString($page = 1, $totalitems, $limit = 10, $adjacents = 1, $targetpage = "/", $pagestring = "?page=",$ajax=null)
{
  //defaults
  $margin="";
  $padding="";
  if(!$adjacents) $adjacents = 1;
  if(!$limit) $limit = 15;
  if(!$page) $page = 1;
  if(!$targetpage) $targetpage = "/";
  
  $firstPage = 0;
  $latestPage = 0;

  //other vars
  $lastpage = ceil($totalitems / $limit);       //lastpage is = total items / items per page, rounded up.
  $lpm1 = $lastpage - 1;                //last page minus 1
  $pageAge = ceil($page / 10)-1; 
  $start = $pageAge*10+1;
  if($page != 1)
      $firstPage = 1;

  if($lastpage > $page )
    $latestPage = 1;


  if(!$start)
      $start = 1;
   
  $end = $start +9; 

  $prevTenPage = $page > 10 ? $start - 10 : 0;
  $nextTenPage = ($lastpage - $page > 10 )? $start + 10 :0;

  
  if($lastpage<$end)
      $end = $lastpage;

  $prev = $page > 1 ? $page - 1 : 0;                  //previous page is page - 1
  $next = $page < $lastpage ? $page +1 : 0;                  //next page is page + 1

      
  $from = (($page - 5) > 2)?$page - 5 : 2;
  
  if($page > 5)
    $eof  = (($page + 4) < $lastpage)? $page + 4 : $lastpage -1;
  else
    $eof = (($page + 9) < ($lastpage-1)) ? $page + 9 : $lastpage-1;
  
/*limit page >14 */
  if($lastpage - $from > 10 && $page > 10)
    $from = ($lastpage - 9) ;

 

  $pagination = "";
  if($lastpage > 1)
  {
    $pagination .= "<div class=\"pagination\"";
    if($margin || $padding)
    {
      $pagination .= " style=\"";
      if($margin)
        $pagination .= "margin: $margin;";
      if($padding)
        $pagination .= "padding: $padding;";
      $pagination .= "\"";
    }
    $pagination .= ">";
    //previous 10 page

    if ($prevTenPage)
      $pagination .= "<a href=\"$targetpage$pagestring$prevTenPage\" title='上十頁'>&#171;&#171; </a>";
//    else
//      $pagination .= "<span class=\"disabled\">&#171;&#171; </span>";
    
    //previous button
    if ($prev)
      $pagination .= "<a href=\"$targetpage$pagestring$prev\" title='上一頁'>&#171; </a>";
    else
      $pagination .= "<span class=\"disabled\">&#171; </span>";
     
    //first button
    if($firstPage)
      $pagination .= "<a href=\"$targetpage$pagestring$firstPage\">1</a>";
    else
      $pagination .= "<span class=\"disabled\">1</span>";

    if($from != 2)
      $pagination .= "<span class=\"disabled\">...</span>";
        


    for ($counter = $from; $counter <= $eof; $counter++)
      {
        if ($counter == $page &&!$ajax)
          $pagination .= "<span class=\"current\">$counter</span>";
        else
          $pagination .= "<a href=\"$targetpage$pagestring$counter\">$counter</a>";
      }


    if($eof +1 != $lastpage)
      $pagination .= "<span class=\"disabled\">...</span>";

    if($latestPage)
      $pagination .= "<a href=\"$targetpage$pagestring$lastpage\"> $lastpage</a>";
    else
      $pagination .= "<span class=\"disabled\">$lastpage</span>";

    if ($next)  
      $pagination .= "<a href=\"$targetpage$pagestring$next\" title='下一頁'> &#187;</a>";
    else    
      $pagination .= "<span class=\"disabled\"> &#187;</span>";

    if ($nextTenPage)  
      $pagination .= "<a href=\"$targetpage$pagestring$nextTenPage\" title='下十頁'> &#187;&#187;</a>";
//    else    
//      $pagination .= "<span class=\"disabled\"> &#187;&#187;</span>";


    $pagination .= "</div>\n";
  }
  
  return $pagination;
}
}
