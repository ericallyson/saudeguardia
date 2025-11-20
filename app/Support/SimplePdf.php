<?php

namespace App\Support;

class SimplePdf
{
    private const PAGE_WIDTH = 595.28; // A4 width in points
    private const PAGE_HEIGHT = 841.89; // A4 height in points
    private const MARGIN = 56.7; // 2 cm approx
    private const TITLE_FONT_SIZE = 18;
    private const BODY_FONT_SIZE = 12;
    private const LINE_HEIGHT = 18;

    /**
     * @param array<int, string> $lines
     */
    public function generate(string $title, array $lines): string
    {
        $contentLines = [];
        $y = self::PAGE_HEIGHT - self::MARGIN;

        $contentLines[] = $this->makeTextBlock($title, $y, self::TITLE_FONT_SIZE);
        $y -= self::LINE_HEIGHT * 1.6;

        foreach ($lines as $line) {
            foreach ($this->wrapText($line) as $wrappedLine) {
                if ($y <= self::MARGIN) {
                    break 2; // Stop if page would overflow
                }

                $contentLines[] = $this->makeTextBlock($wrappedLine, $y, self::BODY_FONT_SIZE);
                $y -= self::LINE_HEIGHT;
            }
        }

        $contentStream = implode("\n", $contentLines);
        $contentLength = strlen($contentStream);

        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj";
        $objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj";
        $objects[] = sprintf(
            '3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj',
            self::PAGE_WIDTH,
            self::PAGE_HEIGHT,
        );
        $objects[] = sprintf("4 0 obj<< /Length %d >>stream\n%s\nendstream\nendobj", $contentLength, $contentStream);
        $objects[] = "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefStart = strlen($pdf);
        $pdf .= sprintf("xref\n0 %d\n", count($objects) + 1);
        $pdf .= "0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefStart . "\n%%EOF";

        return $pdf;
    }

    private function makeTextBlock(string $text, float $y, int $fontSize): string
    {
        $font = 'F1';
        $encoded = $this->encode($text);
        $yFormatted = number_format($y, 2, '.', '');

        return sprintf(
            "BT\n/%s %d Tf\n72 %s Td\n(%s) Tj\nET",
            $font,
            $fontSize,
            $yFormatted,
            $encoded,
        );
    }

    /**
     * @return array<int, string>
     */
    private function wrapText(string $text, int $maxLength = 90): array
    {
        if ($text === '') {
            return [''];
        }

        $words = preg_split('/\s+/u', $text) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current === '' ? $word : $current . ' ' . $word);

            if (mb_strlen($candidate) > $maxLength && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function encode(string $text): string
    {
        $converted = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $converted);

        return $escaped;
    }
}
