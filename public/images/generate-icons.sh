#!/bin/bash

# Script to generate PNG versions of the DailyForever logo
# Requires: ImageMagick (convert command) or rsvg-convert

echo "Generating PNG icons from SVG sources..."

# Function to convert SVG to PNG using ImageMagick
generate_with_imagemagick() {
    # Generate favicon sizes
    convert -background none -resize 16x16 favicon.svg ../favicon-16x16.png
    convert -background none -resize 32x32 favicon.svg ../favicon-32x32.png
    convert -background none -resize 48x48 favicon.svg ../favicon-48x48.png
    
    # Generate Apple touch icon
    convert -background none -resize 180x180 logo-icon.svg ../apple-touch-icon.png
    
    # Generate Android Chrome icons
    convert -background none -resize 192x192 logo-icon.svg ../android-chrome-192x192.png
    convert -background none -resize 512x512 logo-icon.svg ../android-chrome-512x512.png
    
    # Generate Microsoft Tile icon
    convert -background none -resize 150x150 logo-icon.svg ../mstile-150x150.png
    
    # Generate multiple sizes for ICO file
    convert -background none favicon.svg -resize 16x16 favicon-16.png
    convert -background none favicon.svg -resize 32x32 favicon-32.png
    convert -background none favicon.svg -resize 48x48 favicon-48.png
    convert favicon-16.png favicon-32.png favicon-48.png ../favicon.ico
    rm favicon-16.png favicon-32.png favicon-48.png
}

# Function to convert SVG to PNG using rsvg-convert
generate_with_rsvg() {
    # Generate favicon sizes
    rsvg-convert -w 16 -h 16 favicon.svg -o ../favicon-16x16.png
    rsvg-convert -w 32 -h 32 favicon.svg -o ../favicon-32x32.png
    rsvg-convert -w 48 -h 48 favicon.svg -o ../favicon-48x48.png
    
    # Generate Apple touch icon
    rsvg-convert -w 180 -h 180 logo-icon.svg -o ../apple-touch-icon.png
    
    # Generate Android Chrome icons
    rsvg-convert -w 192 -h 192 logo-icon.svg -o ../android-chrome-192x192.png
    rsvg-convert -w 512 -h 512 logo-icon.svg -o ../android-chrome-512x512.png
    
    # Generate Microsoft Tile icon
    rsvg-convert -w 150 -h 150 logo-icon.svg -o ../mstile-150x150.png
}

# Check which tool is available
if command -v convert &> /dev/null; then
    echo "Using ImageMagick..."
    generate_with_imagemagick
elif command -v rsvg-convert &> /dev/null; then
    echo "Using rsvg-convert..."
    generate_with_rsvg
else
    echo "Error: Neither ImageMagick nor rsvg-convert is installed."
    echo "Please install one of these tools:"
    echo "  Ubuntu/Debian: sudo apt-get install imagemagick"
    echo "  Ubuntu/Debian: sudo apt-get install librsvg2-bin"
    echo "  macOS: brew install imagemagick"
    echo "  macOS: brew install librsvg"
    exit 1
fi

echo "Icon generation complete!"
echo ""
echo "Generated files:"
echo "  - favicon-16x16.png"
echo "  - favicon-32x32.png"
echo "  - favicon-48x48.png"
echo "  - apple-touch-icon.png (180x180)"
echo "  - android-chrome-192x192.png"
echo "  - android-chrome-512x512.png"
echo "  - mstile-150x150.png"
echo "  - favicon.ico (multi-resolution)"
