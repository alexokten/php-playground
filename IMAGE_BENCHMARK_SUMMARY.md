# ImageMagick vs VIPS Benchmark - Summary

## Setup Complete ✅

Your benchmark system is now configured and ready to test ImageMagick performance. The foundation is in place to add VIPS comparison when needed.

## What's Working

### ImageMagick Benchmarking
- ✅ PHP Imagick extension installed and configured
- ✅ 5 benchmark operations implemented:
  - **Resize** - Scale to 800x600px
  - **Thumbnail** - Generate 200x200px thumbnail
  - **Crop** - Extract 500x500px region
  - **Quality Reduction** - Compress to 70% JPEG quality
  - **Format Conversion** - Convert to WebP

### Test Images Generated
- `test_large.jpg` - 4000x3000px (174KB) - For performance testing
- `test_medium.jpg` - 1920x1080px (41KB) - Typical web image
- `test_small.jpg` - 800x600px (18KB) - Small image overhead
- `test_transparent.png` - 1000x1000px (7KB) - PNG with transparency

### API Endpoints
- `GET /api/images/list` - View available test images
- `GET /api/image-benchmark?image={filename}` - Run full benchmark

## Sample Results (4000x3000 JPEG)

| Operation | Execution Time | Output Size |
|-----------|---------------|-------------|
| Resize | 234ms | 9.7KB |
| Thumbnail | 26ms | 1.1KB |
| Crop | 22ms | 20KB |
| Quality (70%) | 81ms | 156KB |
| WebP Conversion | 278ms | 36KB |

**Average**: 132ms per operation

## Quick Commands

```bash
# List available images
curl http://localhost:8080/api/images/list | jq

# Run benchmark on specific image
curl "http://localhost:8080/api/image-benchmark?image=test_large.jpg" | jq '.data.summary'

# View detailed results
curl "http://localhost:8080/api/image-benchmark?image=test_large.jpg" | jq '.data.results.tests'

# Check generated thumbnails
docker-compose exec server ls -lh tests/images/output/
```

## VIPS Integration (Next Steps)

The VIPS PHP extension is installed, but requires the `jcupitt/vips` composer package for an ergonomic OOP interface.

### To Add VIPS Benchmarking:

1. **Install the composer package:**
   ```bash
   docker-compose exec server composer require jcupitt/vips
   ```

2. **Update the `benchmarkVips()` method** in `src/Services/ImageProcessing/ImageBenchmark.php`:
   ```php
   use Jcupitt\Vips\Image;

   $image = Image::newFromFile($inputImage, ['access' => 'sequential']);
   $image = $image->resize($scale);
   $image->writeToFile($outputPath);
   ```

3. **Expected VIPS Performance:**
   - 2-4x faster than ImageMagick for large images
   - 50-70% less memory usage (streaming processing)
   - Smaller output files (better compression)

### Why VIPS is Typically Faster

1. **Streaming Architecture** - Processes image regions independently
2. **Memory Efficient** - Doesn't load entire image into memory
3. **Optimized Pipelines** - Operations are fused together
4. **Modern Algorithms** - Uses libwebp, libjpeg-turbo natively

## Architecture

```
Request → ImageBenchmarkController
         ↓
    ImageBenchmark Service
         ↓
    ┌────────────┬─────────────┐
    │ ImageMagick│    VIPS     │
    │  (Imagick) │ (procedural)│
    └────────────┴─────────────┘
         ↓
    Performance Metrics
    (time, memory, file size)
         ↓
    Recommendation Engine
```

## Files Created

- `src/Services/ImageProcessing/ImageBenchmark.php` - Core benchmark logic
- `src/Controllers/ImageBenchmarkController.php` - HTTP endpoints
- `bruno/ImageBenchmark.bru` - Bruno API test
- `bruno/ListImages.bru` - List images test
- `tests/images/input/` - Test images
- `tests/images/output/` - Generated thumbnails
- `scripts/generate-test-images.sh` - Image generation script

## Use Case: Thumbnail Service Decision

Based on your results, here's guidance for your production thumbnail service:

### Choose ImageMagick If:
- Processing < 1000 images/day
- Image sizes < 2000px
- Need exotic formats (TIFF, PSD, etc.)
- Legacy system compatibility required
- Team familiar with ImageMagick

### Choose VIPS If:
- Processing > 1000 images/day
- Large images (> 2000px)
- Memory constraints
- Need maximum performance
- Building new service

### Hybrid Approach:
Use ImageMagick for complex operations (filters, compositing) and VIPS for batch thumbnail generation.

## Performance Insights

`★ Insight ─────────────────────────────────────`
ImageMagick loads entire images into memory for processing, which is simple but memory-intensive. For a 4000x3000 image, this means ~48MB of RAM per operation. VIPS uses demand-driven processing - it only loads the image regions it needs, reducing memory to ~5-10MB for the same operation. When processing thousands of images concurrently, this difference becomes critical for server stability.
`─────────────────────────────────────────────────`

## Next Steps

1. **Test with your actual images** - Replace test images with real production samples
2. **Add VIPS comparison** - Install jcupitt/vips to see side-by-side performance
3. **Tune parameters** - Adjust quality settings, resize dimensions for your use case
4. **Load testing** - Use Apache Bench or k6 to simulate concurrent requests
5. **Monitor production** - Add Ray debugging to track performance in real scenarios

## Documentation

- Full guide: `IMAGE_BENCHMARK.md`
- Project docs: `CLAUDE.md`
- Test images: `tests/images/README.md`

---

**Generated**: October 2025
**Status**: ImageMagick benchmarking operational, VIPS foundation ready
