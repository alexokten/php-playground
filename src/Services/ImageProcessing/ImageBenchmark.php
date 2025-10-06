<?php

declare(strict_types=1);

namespace App\Services\ImageProcessing;

use Exception;
use Imagick;

class ImageBenchmark
{
    private array $results = [];

    public function runComparison(string $inputImage): array
    {
        $this->results = [
            'input_file' => basename($inputImage),
            'input_size' => filesize($inputImage),
            'input_dimensions' => $this->getImageDimensions($inputImage),
            'tests' => [],
        ];

        // Test operations
        $operations = [
            'resize' => ['width' => 800, 'height' => 600],
            'thumbnail' => ['width' => 200, 'height' => 200],
            'crop' => ['width' => 500, 'height' => 500, 'x' => 0, 'y' => 0],
            'quality_reduction' => ['quality' => 70],
            'format_conversion' => ['format' => 'webp'],
        ];

        foreach ($operations as $operation => $params) {
            $this->results['tests'][$operation] = [
                'imagick' => $this->benchmarkImageMagick($inputImage, $operation, $params),
                'vips' => $this->benchmarkVips($inputImage, $operation, $params),
            ];
        }

        return $this->results;
    }

    private function benchmarkImageMagick(string $inputImage, string $operation, array $params): array
    {
        if (!extension_loaded('imagick')) {
            return ['error' => 'ImageMagick extension not loaded'];
        }

        $outputPath = __DIR__ . '/../../../tests/images/output/imagick_' . $operation . '_' . basename($inputImage);

        try {
            $startTime = microtime(true);
            $startMemory = memory_get_usage(true);

            $imagick = new \Imagick($inputImage);

            switch ($operation) {
                case 'resize':
                    $imagick->resizeImage($params['width'], $params['height'], \Imagick::FILTER_LANCZOS, 1);
                    break;
                case 'thumbnail':
                    $imagick->thumbnailImage($params['width'], $params['height'], true);
                    break;
                case 'crop':
                    $imagick->cropImage($params['width'], $params['height'], $params['x'], $params['y']);
                    break;
                case 'quality_reduction':
                    $imagick->setImageCompressionQuality($params['quality']);
                    break;
                case 'format_conversion':
                    $imagick->setImageFormat($params['format']);
                    $outputPath = str_replace(pathinfo($outputPath, PATHINFO_EXTENSION), $params['format'], $outputPath);
                    break;
            }

            $imagick->writeImage($outputPath);
            $imagick->clear();

            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);

            return [
                'success' => true,
                'execution_time_ms' => round(($endTime - $startTime) * 1000.0, 2),
                'memory_used_mb' => round((float) ($endMemory - $startMemory) / 1024.0 / 1024.0, 2),
                'output_size' => filesize($outputPath),
                'output_path' => basename($outputPath),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function benchmarkVips(string $inputImage, string $operation, array $params): array
    {
        if (!extension_loaded('vips')) {
            return [
                'success' => false,
                'error' => 'VIPS extension not loaded',
            ];
        }

        if (!class_exists('\Jcupitt\Vips\Image')) {
            return [
                'success' => false,
                'error' => 'jcupitt/vips package not installed',
                'note' => 'Run: composer require jcupitt/vips',
            ];
        }

        $outputPath = __DIR__ . '/../../../tests/images/output/vips_' . $operation . '_' . basename($inputImage);

        try {
            $startTime = microtime(true);
            $startMemory = memory_get_usage(true);

            $image = \Jcupitt\Vips\Image::newFromFile($inputImage, ['access' => 'sequential']);

            switch ($operation) {
                case 'resize':
                    $scaleW = $params['width'] / $image->width;
                    $scaleH = $params['height'] / $image->height;
                    $scale = min($scaleW, $scaleH);
                    $image = $image->resize($scale);
                    break;
                case 'thumbnail':
                    // Use thumbnail_image for better performance
                    $image = \Jcupitt\Vips\Image::thumbnail($inputImage, $params['width'], [
                        'height' => $params['height'],
                        'crop' => 'centre',
                    ]);
                    break;
                case 'crop':
                    $image = $image->crop($params['x'], $params['y'], $params['width'], $params['height']);
                    break;
                case 'quality_reduction':
                    $image->writeToFile($outputPath, ['Q' => $params['quality']]);
                    $image = null; // Already written
                    break;
                case 'format_conversion':
                    $outputPath = str_replace(pathinfo($outputPath, PATHINFO_EXTENSION), $params['format'], $outputPath);
                    $image->writeToFile($outputPath, ['Q' => 80]);
                    $image = null; // Already written
                    break;
            }

            if ($image !== null) {
                $image->writeToFile($outputPath);
            }

            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);

            return [
                'success' => true,
                'execution_time_ms' => round(($endTime - $startTime) * 1000.0, 2),
                'memory_used_mb' => round((float) ($endMemory - $startMemory) / 1024.0 / 1024.0, 2),
                'output_size' => file_exists($outputPath) ? filesize($outputPath) : 0,
                'output_path' => basename($outputPath),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getImageDimensions(string $imagePath): array
    {
        $info = getimagesize($imagePath);
        return [
            'width' => $info[0] ?? 0,
            'height' => $info[1] ?? 0,
            'type' => $info['mime'] ?? 'unknown',
        ];
    }

    public function generateSummary(): array
    {
        if (empty($this->results)) {
            return ['error' => 'No results available. Run comparison first.'];
        }

        $summary = [
            'total_tests' => 0,
            'imagick_wins' => 0,
            'vips_wins' => 0,
            'imagick_avg_time' => 0,
            'vips_avg_time' => 0,
            'imagick_avg_memory' => 0,
            'vips_avg_memory' => 0,
        ];

        $imagickTimes = [];
        $vipsTimes = [];
        $imagickMemory = [];
        $vipsMemory = [];

        foreach ($this->results['tests'] as $test) {
            $summary['total_tests']++;

            if (isset($test['imagick']['execution_time_ms'])) {
                $imagickTimes[] = $test['imagick']['execution_time_ms'];
                $imagickMemory[] = $test['imagick']['memory_used_mb'];
            }

            if (isset($test['vips']['execution_time_ms'])) {
                $vipsTimes[] = $test['vips']['execution_time_ms'];
                $vipsMemory[] = $test['vips']['memory_used_mb'];
            }

            // Determine winner for this test
            if (isset($test['imagick']['execution_time_ms'], $test['vips']['execution_time_ms'])) {
                if ($test['imagick']['execution_time_ms'] < $test['vips']['execution_time_ms']) {
                    $summary['imagick_wins']++;
                } else {
                    $summary['vips_wins']++;
                }
            }
        }

        $summary['imagick_avg_time'] = !empty($imagickTimes) ? round((float) array_sum($imagickTimes) / (float) count($imagickTimes), 2) : 0;
        $summary['vips_avg_time'] = !empty($vipsTimes) ? round((float) array_sum($vipsTimes) / (float) count($vipsTimes), 2) : 0;
        $summary['imagick_avg_memory'] = !empty($imagickMemory) ? round((float) array_sum($imagickMemory) / (float) count($imagickMemory), 2) : 0;
        $summary['vips_avg_memory'] = !empty($vipsMemory) ? round((float) array_sum($vipsMemory) / (float) count($vipsMemory), 2) : 0;

        return $summary;
    }
}
