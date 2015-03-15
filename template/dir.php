<?php require(dirname(__FILE__).'/breadcrumb.php');?>

<div class="list">

<?php if ($dirs):?>
<ul>
<?php foreach ($dirs as $dir):?>
    <li><a href="<?php echo $dir['path'];?>"><?php echo $dir['name'];?></a></li>
<?php endforeach;?>
</ul>
<?php endif;?>

<ul>
<?php foreach ($files as $file) :?>
    <li><a href="<?php echo $file['path'];?>"><?php echo $file['name'];?></a></li>
<?php endforeach;?>
</ul>
</div>