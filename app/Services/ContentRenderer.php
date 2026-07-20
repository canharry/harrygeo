<?php

namespace App\Services;

use League\CommonMark\GithubFlavoredMarkdownConverter;

class ContentRenderer
{
    /**
     * 渲染文章内容：将 Markdown 表格转换为 HTML，并保留已有的 HTML 内容。
     */
    public static function render(string $content): string
    {
        if (blank($content)) {
            return $content;
        }

        $content = self::convertMarkdownTables($content);
        $content = self::convertImagePaths($content);

        return $content;
    }

    /**
     * 提取 Markdown 表格块并转换为 HTML table。
     *
     * 匹配规则：至少包含一个表头行和一个分隔行（|---|），以及可选的数据行。
     */
    protected static function convertMarkdownTables(string $content): string
    {
        // Trix 编辑器会把带换行的 Markdown 表格文本保存成 <div>行</div><div>行</div>
        // 或 <br> 分隔的 HTML。这里先匹配这种被 HTML 包裹的表格块，再还原成纯
        // Markdown 交给 CommonMark 转换成 <table>。
        $pattern = '/
            (?:<div[^>]*>\s*)?          # 可选的 Trix 块级包裹开头
            [ \t]*\\|[^\n]*\\|            # 表头行
            [ \t]*                      # 行尾空格
            (?:
                <br\s*\/?>              # HTML 换行
                | <\/div>               # 结束上一个块
                | <\/p>                 # 结束上一个段落
                | \r?\n                 # 原始 Markdown 换行
            )
            \s*
            (?:
                <div[^>]*>\s*           # 开始下一个块
                | <p[^>]*>\s*           # 开始下一个段落
            )?
            \s*
            [ \t]*\\|(?:[ \t]*:?-+:?[ \t]*\\|)+[ \t]*  # 分隔行
            \s*
            (?:
                <br\s*\/?>
                | <\/div>
                | <\/p>
                | \r?\n
            )
            \s*
            (?:
                <div[^>]*>\s*
                | <p[^>]*>\s*
            )?
            \s*
            (?:
                [ \t]*\\|[^\n]*\\|        # 数据行
                \s*
                (?:
                    <br\s*\/?>
                    | <\/div>
                    | <\/p>
                    | \r?\n
                )?                       # 最后一行数据可能没有结尾换行
                \s*
                (?:
                    <div[^>]*>\s*
                    | <p[^>]*>\s*
                )?
                \s*
            )*
        /xi';

        return preg_replace_callback($pattern, function (array $matches): string {
            $htmlBlock = $matches[0];

            // 把 HTML 块还原成带 \n 的 Markdown
            $markdown = $htmlBlock;
            $markdown = preg_replace('/<br\s*\/?>/i', "\n", $markdown);
            $markdown = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n", $markdown);
            $markdown = preg_replace('/<\/div>\s*<div[^>]*>/i', "\n", $markdown);
            $markdown = preg_replace('/<\/?div[^>]*>|<\/?p[^>]*>/i', '', $markdown);
            $markdown = trim($markdown, "\r\n");

            if (blank($markdown)) {
                return $htmlBlock;
            }

            $converter = new GithubFlavoredMarkdownConverter([
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
            ]);

            $html = (string) $converter->convert($markdown);

            // 仅保留表格相关标签，去掉 converter 可能包裹的多余标签
            $allowedTags = '<table><thead><tbody><tr><th><td><colgroup><col><caption>';

            return trim(strip_tags($html, $allowedTags));
        }, $content);
    }

    /**
     * 将正文中的相对图片路径转换为 storage 绝对链接。
     */
    protected static function convertImagePaths(string $content): string
    {
        return preg_replace_callback(
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
            function (array $matches): string {
                $src = $matches[1];

                if (filter_var($src, FILTER_VALIDATE_URL) || str_starts_with($src, '/')) {
                    return $matches[0];
                }

                $newSrc = asset('storage/' . ltrim($src, '/'));

                return str_replace($src, $newSrc, $matches[0]);
            },
            $content
        );
    }
}
