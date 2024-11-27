<?php
/**
 * 外链转内链，可自定义页面模版
 * 
 * @package LinkRouter
 * @author Nobu121
 * @version 1.0.0
 * @link https://github.com/Nobu121/typecho-link-router
 */
class LinkRouter_Plugin extends Widget_Abstract implements Typecho_Plugin_Interface
{
    /**
     * 当前页面的URL
     *
     * @access private
     * @var string
     */
    private $url;

    /**
     * 初始化函数
     *
     * @param Typecho\Config $parameter
     * @return void
     */
    public function initParameter(Typecho\Config $parameter)
    {
        parent::initParameter($parameter);
        $this->url = $this->request->get('url', '');
    }

    /**
     * 执行函数简化
     */
    public function execute()
    {
        $this->request->isGet() && $this->action();
    }

    /**
     * 优化 action 方法
     */
    public function action()
    {
        if ($url = $this->request->get('url')) {
            $options = Helper::options()->plugin('LinkRouter');
            header('Content-Type: text/html; charset=UTF-8');
            echo str_replace('{url}', htmlspecialchars($url), $options->template);
            exit;
        }
        $this->response->redirect(Helper::options()->siteUrl);
    }

    /**
     * 插件激活接口
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('LinkRouter_Plugin', 'convertLinks');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('LinkRouter_Plugin', 'convertLinks');
        // 修改路由名称为 go
        Helper::addAction('go', 'LinkRouter_Plugin');
        return '插件已激活';
    }

    /**
     * 插件禁用接口
     */
    public static function deactivate()
    {
        Helper::removeAction('go');
        return '插件已禁用';
    }

    /**
     * 插件配置面板
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 跳转页面模板
        $template = new Typecho_Widget_Helper_Form_Element_Textarea(
            'template',
            null,
            '<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="2;url={url}">
    <title>页面加载中...</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .loading {
            text-align: center;
        }
        .loading-text {
            color: #6c757d;
            font-size: 18px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="loading-text">页面加载中，请稍候...</div>
    </div>
</body>
</html>',
            '跳转页面模板',
            '可用变量: {url} - 目标链接地址'
        );
        $form->addInput($template);

        // 新标签页打开
        $newTab = new Typecho_Widget_Helper_Form_Element_Radio(
            'newTab',
            array('0' => '否', '1' => '是'),
            '1',
            '在新标签页打开',
            '是否在新标签页打开外部链接'
        );
        $form->addInput($newTab);
    }

    /**
     * 个人用户的配置面板
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 优化 convertLinks 方法
     */
    public static function convertLinks($content, $widget, $lastResult)
    {
        $content = $lastResult ?: $content;
        $options = Typecho_Widget::widget('Widget_Options');
        $siteUrl = $options->siteUrl;
        $newTab = $options->plugin('LinkRouter')->newTab ? ' target="_blank"' : '';

        return preg_replace_callback(
            '/<a\s+([^>]*?)href="([^"]+)"([^>]*?)>/i',
            function ($matches) use ($siteUrl, $newTab) {
                $url = $matches[2];
                if (
                    !preg_match('/^(#|javascript:|mailto:|tel:)/', $url) &&
                    !preg_match('/^' . preg_quote($siteUrl, '/') . '/', $url) &&
                    !preg_match('/^\/[^\/]/', $url)
                ) {
                    return sprintf(
                        '<a %shref="%saction/go?url=%s"%s%s>',
                        $matches[1],
                        $siteUrl,
                        urlencode($url),
                        $newTab,
                        $matches[3]
                    );
                }
                return $matches[0];
            },
            $content
        );
    }

}
