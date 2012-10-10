<?=$this->element('admin_header',
	array("selected" => "Users")); 
?>
<?
    if(!isset($loop_fuel)) {
        $loop_fuel = -1;
    }
    $i = $loop_fuel;
?>

<table>
<tr>
<?
foreach($users as $key => $value) {
    $key = $i + 1;
    if(!isset($users[$key])) {
        break;
    }
?>
    
    <td><?=$html->link($users[$key]['User']['username'],'/users/' . $users[$key]['User']['public_key'] . '/' . $users[$key]['User']['username']);?> 
	<?if(empty($users[$key]['User']['permission'])) {?><a class="promote" href="/admin/promote/<?=$users[$key]['User']['public_key'];?>">promote</a><? } else { ?>
		<?=$html->link(__('demote', true), '/admin/demote/' . $users[$key]['User']['public_key'], array('class' => 'demote'));?> 
	<? } ?>
	</td>
<?  $i++;
    if($i < 5 && $i > 0) {
        if($i % 4 == 0) {
?>
</tr>
<tr>
<?      } ?>
<?  }elseif($i > 5) {
        if(($i - 4) % 5 == 0) {
?>
</tr>
<tr>
<?      } ?>
<?  }
    if($i - $loop_fuel == 100) {
        break;
    }  ?>
<?
}
?>
</tr>
</table>

<?
    if((($end_page - $current) > 3) && $current > 3) { ?>
    <span style="float: left;"><?=$html->link('<u>1</u>&nbsp;', '/admin/users/1', array('escape' => false)); ?></span>
    <span style="float: left;"><?=$html->link('<u>' . $current-2 . '</u>&nbsp;', '/admin/users/' . $current-2, array('escape' => false)); ?></span>
    <span style="float: left;"><?=$html->link('<u>' . $current-1 . '</u>&nbsp;', '/admin/users/' . $current-1, array('escape' => false)); ?></span>
    <span style="float: left;"><?=$current;?>&nbsp;</span>
    <span style="float: left;"><?=$html->link('<u>' . $current+1 . '</u>&nbsp;', '/admin/users/' . $current+1, array('escape' => false)); ?></span>
    <span style="float: left;"><?=$html->link('<u>' . $current+2 . '</u>&nbsp;', '/admin/users/' . $current+2, array('escape' => false)); ?></span>
    <span style="float: left;"><?=$html->link('<u>' . $end_page . '</u>&nbsp;', '/admin/users/' . $end_page, array('escape' => false)); ?></span>
<? }elseif($current < $end_page) { ?>
    <span style="float: left;">page <?=$current;?> of <?=$html->link($end_page, '/admin/users/' . $end_page); ?></span>
<? }else { ?>
    <span style="float: left;">page <?=$current;?> of <?=$end_page;?></span>
<? }
if(isset($next)) { ?>
    <span style="float: right;"><?=$html->link('&nbsp;&nbsp;Next >>', '/admin/users/' . $next, array('escape' => false)); ?></span>
<?
}
if(isset($previous)) { ?>
    <span style="float: right;"><?=$html->link('<< Previous&nbsp;&nbsp;', '/admin/users/' . $previous, array('escape' => false)); ?></span>
<? } ?>