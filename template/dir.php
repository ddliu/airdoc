<?php $this->extend('layout.php');?>
<?php $this->begin('content');?>

<div class="list">

<?php if ($children):?>
<ul>
    <?php foreach ($children as $child):?>
    <li><a href="<?php echo $child->getFilename();?><?php if ($child->isDir()):?>/<?php endif;?>"><?php echo $child->getFilename();?></a></li>
    <?php endforeach;?>
</ul>
<?php endif;?>
</div>

<?php $this->end();?>