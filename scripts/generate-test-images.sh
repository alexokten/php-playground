#!/bin/bash

# Generate test images for benchmark comparison
# Run this inside the Docker container after rebuild

echo "Generating test images..."

# Large image (4000x3000)
convert -size 4000x3000 xc:blue -fill white -pointsize 72 \
  -annotate +100+100 'Test Image 4000x3000' \
  tests/images/input/test_large.jpg

echo "✓ Created test_large.jpg (4000x3000)"

# Medium image (1920x1080)
convert -size 1920x1080 xc:green -fill white -pointsize 48 \
  -annotate +100+100 'Test Image 1920x1080' \
  tests/images/input/test_medium.jpg

echo "✓ Created test_medium.jpg (1920x1080)"

# Small image (800x600)
convert -size 800x600 xc:red -fill white -pointsize 36 \
  -annotate +100+100 'Test Image 800x600' \
  tests/images/input/test_small.jpg

echo "✓ Created test_small.jpg (800x600)"

# PNG with transparency (1000x1000)
convert -size 1000x1000 xc:none -fill 'rgba(255,0,0,0.5)' \
  -draw "circle 500,500 500,200" \
  tests/images/input/test_transparent.png

echo "✓ Created test_transparent.png (1000x1000)"

echo ""
echo "Test images generated successfully!"
echo "Run: curl http://localhost:8080/api/images/list | jq"
