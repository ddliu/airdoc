<?php $length = count($breadcrumb); 
if ($length):?>
<div class="breadcrumb">
<?php for ($i = 0; $i < $length; $i++):$info = $breadcrumb[$i];?>
    <?php if ($i == $length - 1):?>
        <?php echo $info['name'];?>
    <?php else:?>
        <a href="<?php echo $info['path'];?>"><?php echo $info['name'];?></a>
        <span class="seperator">/</span>
    <?php endif;?>
<?php endfor;?>
</div>
<?php endif;?>