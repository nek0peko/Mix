<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$mixSearchForm = array_merge([
    'class' => 'mix-search-form', 'method' => 'get', 'id' => '', 'value' => '',
    'inputClass' => '', 'inputType' => 'search', 'placeholder' => '输入关键词',
    'label' => '搜索文章', 'button' => true, 'required' => true
], $mixSearchForm ?? []);
$mixSearchEscape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
?>
<form class="<?php echo $mixSearchEscape($mixSearchForm['class']); ?>" method="<?php echo $mixSearchEscape($mixSearchForm['method']); ?>"
      <?php if ($mixSearchForm['id'] !== ''): ?>id="<?php echo $mixSearchEscape($mixSearchForm['id']); ?>"<?php endif; ?>
      action="<?php echo $mixSearchEscape($this->options->index); ?>" role="search">
    <input type="<?php echo $mixSearchEscape($mixSearchForm['inputType']); ?>" name="s" class="<?php echo $mixSearchEscape($mixSearchForm['inputClass']); ?>"
           value="<?php echo $mixSearchEscape($mixSearchForm['value']); ?>" placeholder="<?php echo $mixSearchEscape($mixSearchForm['placeholder']); ?>"
           aria-label="<?php echo $mixSearchEscape($mixSearchForm['label']); ?>"<?php if ($mixSearchForm['required']): ?> required<?php endif; ?>>
    <?php if ($mixSearchForm['button']): ?><button type="submit">搜索</button><?php endif; ?>
</form>
