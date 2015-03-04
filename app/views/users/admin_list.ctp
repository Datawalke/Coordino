<?php echo $this->element('admin_header',
    array("selected" => "Users"));
?>
<?php
if(!isset($loop_fuel)) {
    $loop_fuel = -1;
}
$i = $loop_fuel;
?>

    <table>
        <tr>
            <?php
            foreach($users as $key => $value) {
            $key = $i + 1;
            if(!isset($users[$key])) {
                break;
            }
            ?>
            <td><a href="/users/<?php echo $users[$key]['User']['public_key'];?>/<?php echo $users[$key]['User']['username'];?>"><?php echo $users[$key]['User']['username'];?></a>
                <?php if(empty($users[$key]['User']['permission'])) {?><a class="promote" href="/admin/promote/<?php echo $users[$key]['User']['public_key'];?>">promote</a><?php } else { ?>
                    <a class="demote" href="/admin/demote/<?php echo $users[$key]['User']['public_key'];?>">demote</a>
                <?php } ?>
            </td>
            <?php  $i++;
            if($i < 5 && $i > 0) {
            if($i % 4 == 0) {
            ?>
        </tr>
        <tr>
            <?php      } ?>
            <?php  }elseif($i > 5) {
            if(($i - 4) % 5 == 0) {
            ?>
        </tr>
        <tr>
            <?php      } ?>
            <?php  }
            if($i - $loop_fuel == 100) {
                break;
            }  ?>
            <?php
            }
            ?>
        </tr>
    </table>

<?php
if((($end_page - $current) > 3) && $current > 3) { ?>
    <span style="float: left;"><a href="/admin/users/1"><u>1</u>&nbsp;</a></span>
    <span style="float: left;"><a href="/admin/users/<?php echo $current-2;?>"><u><?php echo $current-2;?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/admin/users/<?php echo $current-1;?>"><u><?php echo $current-1;?></u>&nbsp;</a></span>
    <span style="float: left;"><?php echo $current;?>&nbsp;</span>
    <span style="float: left;"><a href="/admin/users/<?php echo $current+1;?>"><u><?php echo $current+1;?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/admin/users/<?php echo $current+2;?>"><u><?php echo $current+2;?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/admin/users/<?php echo $end_page;?>"><u><?php echo $end_page;?></u></a></span>
<?php }elseif($current < $end_page) { ?>
    <span style="float: left;">page <?php echo $current;?> of <a href="/admin/users/<?php echo $end_page;?>"><?php echo $end_page;?></a></span>
<?php }else { ?>
    <span style="float: left;">page <?php echo $current;?> of <?php echo $end_page;?></span>
<?php }
if(isset($next)) { ?>
    <span style="float: right;"><a href="/admin/users/<?php echo $next;?>">&nbsp;&nbsp;Next >></a></span>
<?php
}
if(isset($previous)) { ?>
    <span style="float: right;"><a href="/admin/users/<?php echo $previous;?>"><< Previous&nbsp;&nbsp;</a></span>
<?php } ?>