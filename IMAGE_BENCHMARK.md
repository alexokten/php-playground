# ImageMagick vs VIPS Performance Comparison

This benchmark system compares ImageMagick and VIPS image processing libraries for common thumbnail generation operations.

## Overview

The benchmark tests both libraries across multiple operations:
- **Resize** - Scale image to 800x600
- **Thumbnail** - Create 200x200 thumbnail with cropping
- **Crop** - Extract 500x500 region
- **Quality Reduction** - Compress JPEG to 70% quality
- **Format Conversion** - Convert to WebP format

## Quick Start

### 1. Rebuild Docker with Image Libraries

```bash
docker-compose down
docker-compose up -d --build
```

This installs both ImageMagick and VIPS extensions in the container.

### 2. Add Test Images

Place test images in `tests/images/input/`. You can generate test images using:

```bash
# Large image (4000x3000)
docker-compose exec server convert -size 4000x3000 xc:blue -fill white -pointsize 72 \
  -annotate +100+100 'Test Image 4000x3000' tests/images/input/test_large.jpg

# Medium image (1920x1080)
docker-compose exec server convert -size 1920x1080 xc:green -fill white -pointsize 48 \
  -annotate +100+100 'Test Image 1920x1080' tests/images/input/test_medium.jpg

# Small image (800x600)
docker-compose exec server convert -size 800x600 xc:red -fill white -pointsize 36 \
  -annotate +100+100 'Test Image 800x600' tests/images/input/test_small.jpg
```

### 3. Run Benchmarks

List available test images:
```bash
curl http://localhost:8080/api/images/list | jq
```

Run comparison on a specific image:
```bash
curl "http://localhost:8080/api/image-benchmark?image=test_large.jpg" | jq
```

## API Endpoints

### GET `/api/images/list`

Lists all available test images in `tests/images/input/`.

**Response:**
```json
{
  "success": true,
  "data": {
    "images": [
      {
        "filename": "test_large.jpg",
        "size": 145632,
        "dimensions": {
          "width": 4000,
          "height": 3000
        },
        "type": "image/jpeg"
      }
    ],
    "count": 1
  }
}
```

### GET `/api/image-benchmark?image={filename}`

Runs benchmark comparison between ImageMagick and VIPS.

**Parameters:**
- `image` (required) - Filename from `tests/images/input/`

**Response:**
```json
{
  "success": true,
  "data": {
    "results": {
      "input_file": "test_large.jpg",
      "input_size": 145632,
      "input_dimensions": {
        "width": 4000,
        "height": 3000,
        "type": "image/jpeg"
      },
      "tests": {
        "resize": {
          "imagick": {
            "success": true,
            "execution_time_ms": 234.56,
            "memory_used_mb": 12.5,
            "output_size": 45632,
            "output_path": "imagick_resize_test_large.jpg"
          },
          "vips": {
            "success": true,
            "execution_time_ms": 123.45,
            "memory_used_mb": 8.2,
            "output_size": 44120,
            "output_path": "vips_resize_test_large.jpg"
          }
        }
      }
    },
    "summary": {
      "total_tests": 5,
      "imagick_wins": 1,
      "vips_wins": 4,
      "imagick_avg_time": 189.34,
      "vips_avg_time": 95.67,
      "imagick_avg_memory": 10.5,
      "vips_avg_memory": 6.8
    },
    "recommendation": {
      "winner": "VIPS",
      "reasoning": [
        "VIPS is 49.5% faster on average",
        "VIPS uses 3.7MB less memory on average",
        "VIPS won 4 out of 5 tests",
        "ImageMagick won 1 out of 5 tests"
      ]
    }
  }
}
```

## Benchmark Results Interpretation

### Execution Time
- Measured in milliseconds
- Lower is better
- VIPS typically 2-4x faster for large images

### Memory Usage
- Measured in MB
- Lower is better
- VIPS uses streaming processing, reducing memory footprint

### Output Size
- File size in bytes after processing
- Similar between libraries for same quality settings
- Format-dependent (WebP produces smaller files)

## Architecture

### Components

**`src/Services/ImageProcessing/ImageBenchmark.php`**
- Core benchmark logic
- Runs operations on both libraries
- Collects performance metrics
- Generates summary statistics

**`src/Controllers/ImageBenchmarkController.php`**
- HTTP endpoint handlers
- Request validation
- Response formatting with recommendations

**`tests/images/`**
- `input/` - Test images
- `output/` - Generated thumbnails (prefixed with `imagick_` or `vips_`)

## Testing with Bruno

Use the included Bruno API collection:

1. Open Bruno and load the `bruno/` directory
2. Run "ListImages" to see available test images
3. Run "ImageBenchmark" to execute comparison

## Expected Performance Characteristics

### VIPS Advantages
- **Speed**: 2-4x faster for large images
- **Memory**: Lower memory usage (streaming)
- **Concurrency**: Better for high-throughput scenarios

### ImageMagick Advantages
- **Compatibility**: Wider format support
- **Maturity**: More stable API
- **Features**: More advanced filters and effects

## Typical Results

For a 4000x3000 JPEG (≈12MB):

| Operation | ImageMagick | VIPS | Winner |
|-----------|-------------|------|--------|
| Resize    | ~250ms      | ~120ms | VIPS |
| Thumbnail | ~180ms      | ~90ms  | VIPS |
| Crop      | ~150ms      | ~80ms  | VIPS |
| Quality   | ~200ms      | ~100ms | VIPS |
| WebP      | ~280ms      | ~140ms | VIPS |

## Recommendations

**Use VIPS if:**
- Processing large images (>2000px)
- High throughput requirements
- Memory constraints
- Building thumbnail generation service

**Use ImageMagick if:**
- Need exotic image formats
- Complex image manipulation (distortions, compositing)
- Legacy system compatibility required
- Lower volume processing

## Troubleshooting

### Extensions Not Loaded

Check if extensions are enabled:
```bash
docker-compose exec server php -m | grep -E 'imagick|vips'
```

Should output:
```
imagick
vips
```

### Test Images Not Found

Ensure images exist:
```bash
docker-compose exec server ls -lh tests/images/input/
```

### Permission Issues

Fix permissions:
```bash
docker-compose exec server chmod -R 777 tests/images/
```

## Further Reading

- [VIPS Documentation](https://www.libvips.org/)
- [ImageMagick Documentation](https://imagemagick.org/)
- [PHP Imagick](https://www.php.net/manual/en/book.imagick.php)
- [PHP VIPS](https://github.com/libvips/php-vips)
