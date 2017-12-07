
<?php echo validation_errors(); ?>

<?php echo form_open('http://115.159.119.52/ci/index.php/wechat/create'); ?>

    <label for="title">关键字</label>
    <input type="input" name="keyword" /><br />

    <label for="text">回复字</label>
    <textarea name="replyTpl"></textarea><br />

    <input type="submit" name="submit" value="提交" />
</form>