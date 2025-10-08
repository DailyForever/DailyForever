#!/usr/bin/env python3

import os
import sys
from PIL import Image, ImageDraw, ImageFont
import xml.etree.ElementTree as ET
import re

def parse_svg_path(path_data):
    """Parse SVG path data into drawable commands"""
    # This is a simplified parser for our specific paths
    commands = []
    path_data = re.findall(r'([MLQCZ])\s*([^MLQCZ]*)', path_data)

    for cmd, coords in path_data:
        if cmd in ['M', 'L']:
            coords = [float(x) for x in coords.split()]
            commands.append(('line', coords))
        elif cmd == 'Q':
            coords = [float(x) for x in coords.split()]
            commands.append(('quad', coords))
        elif cmd == 'Z':
            commands.append(('close',))

    return commands

def draw_svg_to_pil(svg_path, size):
    """Convert our simple SVG to PIL Image"""
    # Read SVG content
    with open(svg_path, 'r') as f:
        content = f.read()

    # Parse viewBox
    viewbox_match = re.search(r'viewBox="([^"]+)"', content)
    if viewbox_match:
        viewbox = [float(x) for x in viewbox_match.group(1).split()]
    else:
        viewbox = [0, 0, size, size]

    # Create image
    img = Image.new('RGBA', (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)

    # Define colors
    colors = {
        '#60a5fa': (96, 165, 250, 255),
        '#2563eb': (37, 99, 235, 255),
        '#3b82f6': (59, 130, 246, 255),
        'white': (255, 255, 255, 255),
        'currentColor': (37, 99, 235, 255)  # Default blue for monochrome
    }

    # Parse gradients (simplified)
    gradients = {}

    # Find all path elements
    paths = re.findall(r'<path[^>]*d="([^"]+)"[^>]*(?:fill="([^"]+)"[^>]*)?>', content)

    for path_data, fill_color in paths:
        if fill_color and fill_color.startswith('url(#'):
            # Use gradient colors
            grad_id = fill_color[5:-1]  # Remove 'url(' and ')'
            if grad_id in gradients:
                color = gradients[grad_id]
            else:
                color = colors.get('white', (255, 255, 255, 255))
        else:
            color = colors.get(fill_color or 'white', (255, 255, 255, 255))

        # For now, just draw as a simple shape since our SVGs are simple
        # This is a basic implementation - for production you'd want a full SVG parser
        try:
            # Draw a simple shield-like shape
            points = []
            if 'Z' in path_data:
                # Try to extract some key points for a simple polygon
                coords = re.findall(r'[-0-9.]+,[-0-9.]+', path_data)
                if coords:
                    for coord in coords[:6]:  # Take first 6 coordinate pairs
                        x, y = map(float, coord.split(','))
                        # Scale and translate to fit our image size
                        scale_x = size / (viewbox[2] - viewbox[0]) if viewbox[2] > viewbox[0] else 1
                        scale_y = size / (viewbox[3] - viewbox[1]) if viewbox[3] > viewbox[1] else 1
                        points.append((x * scale_x, y * scale_y))

            if len(points) >= 3:
                draw.polygon(points, fill=color)
        except Exception as e:
            print(f"Warning: Could not parse path: {e}")

    return img

def generate_png_from_svg(svg_path, png_path, size):
    """Generate PNG from SVG using PIL"""
    try:
        img = draw_svg_to_pil(svg_path, size)
        img.save(png_path, 'PNG')
        print(f"Generated {png_path} ({size}x{size})")
        return True
    except Exception as e:
        print(f"Error generating {png_path}: {e}")
        return False

def create_favicon_ico(sizes, output_path):
    """Create a multi-resolution ICO file"""
    try:
        images = []
        for size in sizes:
            img = draw_svg_to_pil('favicon.svg', size)
            images.append(img)

        # Save as ICO (Pillow can handle this)
        images[0].save(output_path, 'ICO', sizes=sizes, append_images=images[1:])
        print(f"Generated {output_path} with sizes {sizes}")
        return True
    except Exception as e:
        print(f"Error creating ICO: {e}")
        return False

def main():
    """Generate all required PNG files"""
    script_dir = os.path.dirname(os.path.abspath(__file__))

    # Change to script directory
    os.chdir(script_dir)

    print("Generating PNG icons from SVG sources using Python/Pillow...")

    # Generate favicon sizes
    generate_png_from_svg('../favicon.svg', 'favicon-16x16.png', 16)
    generate_png_from_svg('../favicon.svg', 'favicon-32x32.png', 32)
    generate_png_from_svg('../favicon.svg', 'favicon-48x48.png', 48)

    # Generate Apple touch icon
    generate_png_from_svg('logo-icon.svg', 'apple-touch-icon.png', 180)

    # Generate Android Chrome icons
    generate_png_from_svg('logo-icon.svg', 'android-chrome-192x192.png', 192)
    generate_png_from_svg('logo-icon.svg', 'android-chrome-512x512.png', 512)

    # Generate Microsoft Tile icon
    generate_png_from_svg('logo-icon.svg', 'mstile-150x150.png', 150)

    # Create multi-resolution ICO file
    create_favicon_ico([16, 32, 48], '../favicon.ico')

    print("\nIcon generation complete!")
    print("\nGenerated files:")
    print("  - favicon-16x16.png")
    print("  - favicon-32x32.png")
    print("  - favicon-48x48.png")
    print("  - apple-touch-icon.png (180x180)")
    print("  - android-chrome-192x192.png")
    print("  - android-chrome-512x512.png")
    print("  - mstile-150x150.png")
    print("  - favicon.ico (multi-resolution)")

if __name__ == "__main__":
    main()
