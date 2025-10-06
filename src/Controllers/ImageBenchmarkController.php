<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\ExceptionHandler;
use App\Helpers\Response;
use App\Services\ImageProcessing\ImageBenchmark;
use Router\RequestItem;

class ImageBenchmarkController
{
    public function runBenchmark(RequestItem $request): void
    {
        try {
            $imageName = $request->params['image'] ?? $_GET['image'] ?? null;

            if (!$imageName) {
                Response::sendError('Image parameter is required', 400);
                return;
            }

            $inputPath = __DIR__ . '/../../tests/images/input/' . basename($imageName);

            if (!file_exists($inputPath)) {
                Response::sendError("Image not found: {$imageName}. Please place test images in tests/images/input/", 404);
                return;
            }

            $benchmark = new ImageBenchmark();
            $results = $benchmark->runComparison($inputPath);
            $summary = $benchmark->generateSummary();

            Response::sendSuccess([
                'results' => $results,
                'summary' => $summary,
                'recommendation' => $this->generateRecommendation($summary),
            ]);
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function listAvailableImages(RequestItem $request): void
    {
        try {
            $inputDir = __DIR__ . '/../../tests/images/input/';
            $images = [];

            if (is_dir($inputDir)) {
                $files = scandir($inputDir);
                foreach ($files as $file) {
                    if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $filePath = $inputDir . $file;
                        $info = getimagesize($filePath);
                        $images[] = [
                            'filename' => $file,
                            'size' => filesize($filePath),
                            'dimensions' => [
                                'width' => $info[0] ?? 0,
                                'height' => $info[1] ?? 0,
                            ],
                            'type' => $info['mime'] ?? 'unknown',
                        ];
                    }
                }
            }

            Response::sendSuccess([
                'images' => $images,
                'count' => count($images),
            ]);
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    private function generateRecommendation(array $summary): array
    {
        $recommendation = [
            'winner' => '',
            'reasoning' => [],
        ];

        // Check if we have valid times to compare
        if ($summary['vips_avg_time'] > 0 && $summary['imagick_avg_time'] > 0) {
            if ($summary['vips_avg_time'] < $summary['imagick_avg_time']) {
                $speedup = round(($summary['imagick_avg_time'] / $summary['vips_avg_time']) * 100 - 100, 1);
                $recommendation['winner'] = 'VIPS';
                $recommendation['reasoning'][] = "VIPS is {$speedup}% faster on average";
            } else {
                $slowdown = round(($summary['vips_avg_time'] / $summary['imagick_avg_time']) * 100 - 100, 1);
                $recommendation['winner'] = 'ImageMagick';
                $recommendation['reasoning'][] = "ImageMagick is {$slowdown}% faster on average";
            }
        } elseif ($summary['imagick_avg_time'] > 0) {
            $recommendation['winner'] = 'ImageMagick';
            $recommendation['reasoning'][] = "Only ImageMagick results available";
            $recommendation['reasoning'][] = "ImageMagick completed all {$summary['imagick_wins']} tests successfully";
            $recommendation['reasoning'][] = "Average processing time: {$summary['imagick_avg_time']}ms";
            $recommendation['reasoning'][] = "Average memory usage: {$summary['imagick_avg_memory']}MB";
            $recommendation['reasoning'][] = "Note: Install jcupitt/vips for VIPS comparison";
        } else {
            $recommendation['winner'] = 'N/A';
            $recommendation['reasoning'][] = "No successful benchmark results available";
        }

        // Only compare memory if both have results
        if ($summary['vips_avg_memory'] > 0 && $summary['imagick_avg_memory'] > 0) {
            if ($summary['vips_avg_memory'] < $summary['imagick_avg_memory']) {
                $memSaving = round($summary['imagick_avg_memory'] - $summary['vips_avg_memory'], 2);
                $recommendation['reasoning'][] = "VIPS uses {$memSaving}MB less memory on average";
            } else {
                $memExtra = round($summary['vips_avg_memory'] - $summary['imagick_avg_memory'], 2);
                $recommendation['reasoning'][] = "ImageMagick uses {$memExtra}MB less memory on average";
            }
        }

        return $recommendation;
    }
}
