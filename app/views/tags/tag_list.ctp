<div id="body" class="wrapper">
    <table>
        <tr>

<?  if(!isset($loop_fuel)) {
    $loop_fuel = -1;
    }
    $i = $loop_fuel;
     foreach($tag as $key => $value) {
        $key = $i + 1;
        if(!isset($tag[$key])) {
            break;
        }
?>
            <td style="width: 200px; padding: 5px;">
                <div class="tag"><a href="/tags/<?=$tag[$key]['tag'];?>"><?=$tag[$key]['tag'];?></a> x <?=$tag[$key]['count'];?></div>
            </td>
<?      $i++;
        if($i < 5 && $i > 0) {
            if($i % 4 == 0) {
?>
        </tr>
        <tr>
<?
            }
        }elseif($i > 5) {
            if(($i - 4) % 5 == 0) {
?>
        </tr>
        <tr>
<?          }
        }
        if($i - $loop_fuel == 100) {
            break;
        }
    }

?>
        </tr>
    </table>
</div>
<?
    if((($end_page - $current) > 3) && $current > 3) { ?>
    <span style="float: left;"><a href="/tags/1"><u>1</u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tags/<?=$current-2;?>"><u><?=$current-2;?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tags/<?=$current-1;?>"><u><?=$current-1;?></u>&nbsp;</a></span>
    <span style="float: left;"><?=$current;?>&nbsp;</span>
    <span style="float: left;"><a href="/tags/<?=$current+1;?>"><u><?=$current+1;?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tags/<?=$current+2;?>"><u><?=$current+2;?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tags/<?=$end_page;?>"><u><?=$end_page;?></u></a></span>
<? }elseif($current < $end_page) { ?>
    <span style="float: left;">page <?=$current;?> of <a href="/tags/page:<?=$end_page;?>"><?=$end_page;?></a></span>
<? }else { ?>
    <span style="float: left;">page <?=$current;?> of <?=$end_page;?></span>
<? }
if(isset($next)) { ?>
    <span style="float: right;"><a href="/tags/<?=$next;?>">&nbsp;&nbsp;Next >></a></span>
<?
}
if(isset($previous)) { ?>
    <span style="float: right;"><a href="/tags/<?=$previous;?>"><< Previous&nbsp;&nbsp;</a></span>
<? } ?>
