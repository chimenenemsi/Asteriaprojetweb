<?php
declare(strict_types=1);

class BaseController
{
    public function render(string $view, array $data = [], string $area = 'frontoffice'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require ROOT_PATH . '/views/' . $view . '.php';
        $content = (string) ob_get_clean();

        require ROOT_PATH . '/views/layouts/' . $area . '.php';
    }

    protected function redirect(string $route, array $params = []): void
    {
        header('Location: ' . route_url($route, $params));
        exit;
    }

    public function renderNotFound(): void
    {
        http_response_code(404);
        echo '<h1>Not Found</h1>';
    }

    protected function connection(): PDO
    {
        return Database::connection();
    }

    protected function executeNamed(PDOStatement $statement, array $params = []): bool
    {
        return $statement->execute($this->namedParams($params));
    }

    protected function downloadSimplePdf(string $fileName, array $lines): void
    {
        $pdf = $this->buildSimplePdfDocument($lines);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-Length: ' . (string) strlen($pdf));
        echo $pdf;
        exit;
    }

    /**
     * @param string[] $lines
     */
    protected function buildSimplePdfDocument(array $lines): string
    {
        [$title, $metaLines, $bodyLines] = $this->pdfSections($lines);
        $pageStreams = $this->buildPdfPageStreams($title, $metaLines, $bodyLines);

        return $this->compilePdf($pageStreams);
    }

    /**
     * @return string[]
     */
    private function wrapPdfText(string $text, int $length): array
    {
        $ascii = function_exists('iconv')
            ? (string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text)
            : $text;
        $normalized = preg_replace('/\s+/', ' ', trim($ascii)) ?: '';

        return $normalized === '' ? [' '] : explode("\n", wordwrap($normalized, $length, "\n", true));
    }

    private function pdfEscape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function namedParams(array $params): array
    {
        $bound = [];
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $bound[$key] = $value;
                continue;
            }

            $bound[':' . ltrim((string) $key, ':')] = $value;
        }

        return $bound;
    }

    /**
     * @param string[] $lines
     * @return array{0:string,1:array<int,string>,2:array<int,string>}
     */
    private function pdfSections(array $lines): array
    {
        $normalized = array_map(static fn ($line): string => trim((string) $line), $lines);
        $title = $normalized[0] !== '' ? $normalized[0] : 'Export';
        $metaLines = [];
        $bodyLines = [];
        $bodyStarted = false;

        foreach (array_slice($normalized, 1) as $line) {
            if ($line === '') {
                if ($metaLines !== []) {
                    $bodyStarted = true;
                }
                continue;
            }

            if (!$bodyStarted && preg_match('/^[A-Za-z][A-Za-z ]+:/', $line) === 1) {
                $metaLines[] = $line;
                continue;
            }

            $bodyStarted = true;
            $bodyLines[] = $line;
        }

        if ($bodyLines === []) {
            $bodyLines[] = 'No rows available for the selected filters.';
        }

        return [$title, $metaLines, $bodyLines];
    }

    /**
     * @param string[] $metaLines
     * @param string[] $bodyLines
     * @return string[]
     */
    private function buildPdfPageStreams(string $title, array $metaLines, array $bodyLines): array
    {
        $pageWidth = 595.0;
        $pageHeight = 842.0;
        $margin = 40.0;
        $contentWidth = $pageWidth - ($margin * 2);
        $top = $pageHeight - $margin;
        $bottom = 46.0;
        $pageStreams = [];
        $pageIndex = 0;
        $currentY = $top;
        $stream = '';

        $startPage = function (bool $includeMeta) use (
            &$stream,
            &$currentY,
            &$pageIndex,
            $title,
            $metaLines,
            $pageHeight,
            $margin,
            $contentWidth,
            $top
        ): void {
            $pageIndex++;
            $currentY = $top;
            $stream = '';
            $subtitle = $pageIndex === 1 ? 'Catalog export overview' : 'Catalog export overview (continued)';

            $stream .= $this->pdfRect($margin, $pageHeight - 122.0, $contentWidth, 82.0, [0.09, 0.20, 0.35], null, 'f');
            $stream .= $this->pdfTextLine($margin + 24.0, $pageHeight - 76.0, 'F2', 23.0, $title, [1.0, 1.0, 1.0]);
            $stream .= $this->pdfTextLine($margin + 24.0, $pageHeight - 101.0, 'F1', 11.0, $subtitle, [0.87, 0.92, 0.98]);
            $stream .= $this->pdfTextLine($margin + $contentWidth - 128.0, $pageHeight - 76.0, 'F1', 10.0, date('Y-m-d H:i'), [0.87, 0.92, 0.98]);
            $currentY = $pageHeight - 146.0;

            if ($includeMeta && $metaLines !== []) {
                $wrappedMeta = [];
                foreach ($metaLines as $metaLine) {
                    foreach ($this->wrapPdfText($metaLine, 76) as $wrappedLine) {
                        $wrappedMeta[] = $wrappedLine;
                    }
                }

                $metaHeight = 18.0 + (count($wrappedMeta) * 16.0);
                $stream .= $this->pdfRect($margin, $currentY - $metaHeight, $contentWidth, $metaHeight, [0.95, 0.97, 0.99], [0.82, 0.88, 0.94], 'B');
                $stream .= $this->pdfTextLine($margin + 18.0, $currentY - 20.0, 'F2', 11.0, 'Filters and context', [0.18, 0.28, 0.40]);
                $metaTextY = $currentY - 40.0;
                foreach ($wrappedMeta as $wrappedLine) {
                    $stream .= $this->pdfTextLine($margin + 18.0, $metaTextY, 'F1', 10.5, $wrappedLine, [0.30, 0.36, 0.45]);
                    $metaTextY -= 15.0;
                }

                $currentY -= $metaHeight + 18.0;
            }
        };

        $finalizePage = function () use (&$stream, &$pageStreams): void {
            $pageStreams[] = $stream;
        };

        $startPage(true);

        foreach ($bodyLines as $index => $line) {
            $wrapped = $this->wrapPdfText($line, 72);
            $rowHeight = 24.0 + (count($wrapped) * 15.0);

            if ($currentY - $rowHeight < $bottom) {
                $finalizePage();
                $startPage(false);
            }

            $rowY = $currentY - $rowHeight;
            $fillColor = $index % 2 === 0 ? [1.0, 1.0, 1.0] : [0.98, 0.99, 1.0];
            $stream .= $this->pdfRect($margin, $rowY, $contentWidth, $rowHeight, $fillColor, [0.88, 0.91, 0.95], 'B');
            $stream .= $this->pdfTextLine($margin + 16.0, $rowY + $rowHeight - 18.0, 'F2', 10.0, 'Record ' . (string) ($index + 1), [0.20, 0.32, 0.45]);

            $textY = $rowY + $rowHeight - 36.0;
            foreach ($wrapped as $wrappedLine) {
                $stream .= $this->pdfTextLine($margin + 16.0, $textY, 'F1', 10.5, $wrappedLine, [0.16, 0.20, 0.27]);
                $textY -= 14.0;
            }

            $currentY = $rowY - 10.0;
        }

        $finalizePage();

        $totalPages = count($pageStreams);
        foreach ($pageStreams as $pageNumber => $pageStream) {
            $pageStreams[$pageNumber] = $pageStream
                . $this->pdfLine($margin, 34.0, $pageWidth - $margin, 34.0, [0.82, 0.88, 0.94])
                . $this->pdfTextLine($margin, 22.0, 'F1', 9.0, 'Generated by Produits export', [0.48, 0.54, 0.63])
                . $this->pdfTextLine($pageWidth - 94.0, 22.0, 'F1', 9.0, 'Page ' . (string) ($pageNumber + 1) . ' / ' . (string) $totalPages, [0.48, 0.54, 0.63]);
        }

        return $pageStreams;
    }

    /**
     * @param string[] $pageStreams
     */
    private function compilePdf(array $pageStreams): string
    {
        $catalogId = 1;
        $pagesId = 2;
        $fontRegularId = 3;
        $fontBoldId = 4;
        $nextId = 5;
        $pageIds = [];
        $objects = [];

        $objects[$catalogId] = '<< /Type /Catalog /Pages ' . $pagesId . ' 0 R >>';
        $objects[$fontRegularId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBoldId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        foreach ($pageStreams as $pageStream) {
            $contentId = $nextId++;
            $pageId = $nextId++;

            $objects[$contentId] = '<< /Length ' . strlen($pageStream) . " >>\nstream\n" . $pageStream . "\nendstream";
            $objects[$pageId] = '<< /Type /Page /Parent ' . $pagesId . ' 0 R /MediaBox [0 0 595 842] '
                . '/Resources << /Font << /F1 ' . $fontRegularId . ' 0 R /F2 ' . $fontBoldId . ' 0 R >> >> '
                . '/Contents ' . $contentId . ' 0 R >>';
            $pageIds[] = $pageId;
        }

        $objects[$pagesId] = '<< /Type /Pages /Kids [' . implode(' ', array_map(
            static fn (int $pageId): string => $pageId . ' 0 R',
            $pageIds
        )) . '] /Count ' . count($pageIds) . ' >>';

        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        $maxObjectId = max(array_keys($objects));
        for ($id = 1; $id <= $maxObjectId; $id++) {
            $offset = $offsets[$id] ?? 0;
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . ($maxObjectId + 1) . ' /Root ' . $catalogId . " 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";

        return $pdf;
    }

    /**
     * @param array<int,float>|null $strokeColor
     * @param array<int,float> $fillColor
     */
    private function pdfRect(float $x, float $y, float $width, float $height, array $fillColor, ?array $strokeColor, string $paintOperator): string
    {
        $commands = "q\n";
        $commands .= $this->pdfColorCommand('rg', $fillColor) . "\n";
        if ($strokeColor !== null) {
            $commands .= $this->pdfColorCommand('RG', $strokeColor) . "\n";
            $commands .= "0.8 w\n";
        }
        $commands .= sprintf("%.2F %.2F %.2F %.2F re\n%s\nQ\n", $x, $y, $width, $height, $paintOperator);

        return $commands;
    }

    /**
     * @param array<int,float> $color
     */
    private function pdfLine(float $x1, float $y1, float $x2, float $y2, array $color): string
    {
        return "q\n"
            . $this->pdfColorCommand('RG', $color) . "\n"
            . "0.8 w\n"
            . sprintf("%.2F %.2F m\n%.2F %.2F l\nS\nQ\n", $x1, $y1, $x2, $y2);
    }

    /**
     * @param array<int,float> $color
     */
    private function pdfTextLine(float $x, float $y, string $font, float $size, string $text, array $color): string
    {
        return "BT\n"
            . $this->pdfColorCommand('rg', $color) . "\n"
            . '/' . $font . ' ' . number_format($size, 2, '.', '') . " Tf\n"
            . sprintf("1 0 0 1 %.2F %.2F Tm\n", $x, $y)
            . '(' . $this->pdfEscape($text) . ") Tj\nET\n";
    }

    /**
     * @param array<int,float> $color
     */
    private function pdfColorCommand(string $operator, array $color): string
    {
        return implode(' ', array_map(
            static fn (float $value): string => number_format($value, 3, '.', ''),
            $color
        )) . ' ' . $operator;
    }
}
