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
     * 匹配规则：连续出现的 |...| 行，其中第二行必须是分隔行（|---|）。
     *
     * Trix 编辑器会把带换行的 Markdown 表格文本保存成 <div>行</div><div>行</div>
     * 或 <br>、<p> 分隔的 HTML，因此先把这种被 HTML 包裹的表格行还原成纯 Markdown
     * 行，再按行扫描识别表格块，避免正则贪婪匹配把中间正文也吞进表格。
     */
    protected static function convertMarkdownTables(string $content): string
    {
        $content = self::normalizeTableHtml($content);

        $lines = preg_split('/\r?\n/', $content);
        $result = [];
        $tableBuffer = [];

        foreach ($lines as $line) {
            if (! self::isMarkdownTableLine($line)) {
                if (! empty($tableBuffer)) {
                    $result[] = self::convertTableBuffer($tableBuffer);
                    $tableBuffer = [];
                }
                $result[] = $line;
                continue;
            }

            // 当前行是表格行。若它是分隔行，且缓冲区里已经存在分隔行，
            // 并且分隔行后面还有数据行，说明这是新表格的开始（连续表格）。
            if (self::isMarkdownTableSeparator($line)) {
                $lastSeparatorIndex = self::lastSeparatorIndex($tableBuffer);

                if ($lastSeparatorIndex !== -1 && $lastSeparatorIndex < count($tableBuffer) - 1) {
                    // 上一个表格：去掉最后一行（新表格的表头行）
                    $previousTable = array_slice($tableBuffer, 0, -1);
                    $result[] = self::convertTableBuffer($previousTable);

                    // 新表格从上一行（新表头）和当前分隔行开始
                    $tableBuffer = [end($tableBuffer), $line];
                    continue;
                }
            }

            $tableBuffer[] = $line;
        }

        if (! empty($tableBuffer)) {
            $result[] = self::convertTableBuffer($tableBuffer);
        }

        return implode("\n", $result);
    }

    /**
     * 获取表格缓冲区中最后一行分隔行的索引。
     */
    protected static function lastSeparatorIndex(array $buffer): int
    {
        $index = -1;
        foreach ($buffer as $i => $line) {
            if (self::isMarkdownTableSeparator($line)) {
                $index = $i;
            }
        }

        return $index;
    }

    /**
     * 把 Trix 保存的 <div>/<p>/<br> 包裹的表格行还原为 \n 分隔的普通行。
     */
    protected static function normalizeTableHtml(string $content): string
    {
        // 先把相邻 <div>/<p> 块之间断开成换行，否则去掉标签后不同块的内容会连成一行
        $content = preg_replace('/<\/(?:div|p)>\s*<(?:div|p)[^>]*>/i', "\n", $content);
        $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
        $content = preg_replace('/<\/?div[^>]*>|<\/?p[^>]*>/i', '', $content);

        // 去掉 <p>/<div> 后，<h2>标题</h2>|...| 会连成一行，这里把块级结束标签与表格行断开
        $content = preg_replace('/(<\/(?:h[1-6]|blockquote|ul|ol|li)>)(\s*)(\|)/i', "$1\n$3", $content);

        return $content;
    }

    /**
     * 判断一行是否是 Markdown 表格行（以 | 开头和结尾）。
     */
    protected static function isMarkdownTableLine(string $line): bool
    {
        return preg_match('/^[ \t]*\|.*\|[ \t]*$/', $line) === 1;
    }

    /**
     * 判断一行是否是 Markdown 表格分隔行。
     */
    protected static function isMarkdownTableSeparator(string $line): bool
    {
        return preg_match('/^[ \t]*\|(?:[ \t]*:?-+:?[ \t]*\|)+[ \t]*$/', $line) === 1;
    }

    /**
     * 把收集到的表格行转换成 HTML table；如果不是合法表格则原样返回。
     */
    protected static function convertTableBuffer(array $buffer): string
    {
        if (count($buffer) < 2 || ! self::isMarkdownTableSeparator($buffer[1])) {
            return implode("\n", $buffer);
        }

        $markdown = implode("\n", $buffer);

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        $html = (string) $converter->convert($markdown);

        // 仅保留表格相关标签，去掉 converter 可能包裹的多余标签
        $allowedTags = '<table><thead><tbody><tr><th><td><colgroup><col><caption>';

        return trim(strip_tags($html, $allowedTags));
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
