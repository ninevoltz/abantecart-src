<?php if ($this->config->get('jivo_chat_code'))
{
  $rm = array('&lt;script src=&quot;//code.jivosite.com/widget/', '&quot; async&gt;&lt;/script&gt;');
  $jcode = trim(
    str_replace($rm, "", strtolower($this->config->get('jivo_chat_code')))
  );
  echo '<script src="//code.jivosite.com/widget/' .$jcode. '" async></script>'.PHP_EOL;
}
?>
