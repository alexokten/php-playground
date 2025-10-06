# Image Processing Test Images

## Setup

Place test images in the `input/` directory. Recommended test images:

- **Large high-resolution image** (e.g., 4000x3000px JPEG) - tests performance under load
- **Medium image** (e.g., 1920x1080px JPEG) - typical web image
- **PNG with transparency** - tests format handling
- **Small image** (e.g., 500x500px) - tests overhead for small operations

## Directory Structure

```
tests/images/
├── input/          # Place your test images here
├── output/         # Generated thumbnails and processed images
└── README.md       # This file
```

## Example Test Images

You can download test images from:
- https://unsplash.com (free high-resolution photos)
- https://picsum.photos (placeholder images)

Or use ImageMagick to generate test images:

```bash
# Generate a large test image
docker-compose exec server convert -size 4000x3000 xc:blue -fill white -pointsize 72 -annotate +100+100 'Test Image 4000x3000' tests/images/input/test_large.jpg

# Generate a medium test image
docker-compose exec server convert -size 1920x1080 xc:green -fill white -pointsize 48 -annotate +100+100 'Test Image 1920x1080' tests/images/input/test_medium.jpg

# Generate a small test image
docker-compose exec server convert -size 800x600 xc:red -fill white -pointsize 36 -annotate +100+100 'Test Image 800x600' tests/images/input/test_small.jpg
```

## Testing

Once images are placed in `input/`, run the comparison:

```bash
curl http://localhost:8080/api/image-benchmark?image=test_large.jpg
```
