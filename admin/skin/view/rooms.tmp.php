<script type="text/javascript">
    $(function () {
        OrderTable("#crudtable",'<?=url(array('act'=>'order'))?>');
    });
</script>

<div class="content">
    <? if($title != '') echo "<h1>$title</h1>"; ?>
    <?=$content?>
</div>
