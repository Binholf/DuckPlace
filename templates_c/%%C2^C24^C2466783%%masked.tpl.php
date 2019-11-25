<input
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "editors/editor_options.tpl", 'smarty_include_vars' => array('Editor' => $this->_tpl_vars['Editor'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    class="form-control"
    masked="true"
    mask="<?php echo $this->_tpl_vars['Editor']->GetMask(); ?>
"
    type="text"
    value="<?php echo $this->_tpl_vars['Editor']->GetValue(); ?>
"
>
<div class="masked-edit-hint"><?php echo $this->_tpl_vars['Editor']->GetHint(); ?>
</div>