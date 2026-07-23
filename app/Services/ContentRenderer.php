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
        $pattern = '/^[ \t]*\|[^\n]*\|[ \t]*\r?\n[ \t]*\|(?:\s*:?-+:?\s*\|)+[ \t]*\r?\n(?:[ \t]*\|[^\n]*\|[ \t]*\r?\n?)*/m';

        return preg_replace_callback($pattern, function (array $matches): string {
            $markdown = trim($matches[0], "\r\n");

            if (blank($markdown)) {
                return $matches[0];
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
