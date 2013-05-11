<?php

class DefaultViewSetting
{
	public static function getTemplate()
	{
		$template = new Smarty();
		return $template;
	}
    public static function getRootDir()
    {
        return ROOT_PATH."/app/view/";
    }
	
	public static function setTemplateSetting($template)
	{
		$template->caching = false;
//		$template->caching = true;
		$template->cache_dir = ROOT_PATH."/ctemplates/";
		$template->php_handling = false;
		
		$template->template_dir = self::getRootDir();
		$template->compile_dir = ROOT_PATH."/ctemplates/";
//		echo $template->template_dir;
		$template->config_dir   = ROOT_PATH."/app/view/";
		$template->compile_check = true;
		// this helps if php is running in 'safe_mode'
		$template->use_sub_dirs = false;
		$template->left_delimiter='<{';
		$template->right_delimiter='}>';
		// register dynamic block for every template instance
		$template->register_block('dynamic', 'smarty_block_dynamic', false);      
	}
}
?>
