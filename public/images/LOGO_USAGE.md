# DailyForever Logo Assets

## Logo Design Philosophy

The DailyForever logo combines minimalistic design principles inspired by PrivateBin, Apple, and Google. It features:

- **Shield Symbol**: Represents security and protection
- **Lock Icon**: Symbolizes encryption and privacy
- **Blue Gradient**: Professional color scheme (#60a5fa to #2563eb)
- **Clean Typography**: Modern, readable sans-serif font

## Available Logo Variants

### 1. Icon Only (`logo-icon.svg`)
- **Size**: 64x64px
- **Use Cases**: App icons, favicons, avatar images
- **Description**: Shield with lock symbol, no text

### 2. Full Logo (`logo-full.svg`)
- **Size**: 280x64px
- **Use Cases**: Website headers, documentation, marketing materials
- **Description**: Icon + "DailyForever" text for light backgrounds

### 3. Full Logo Dark (`logo-full-dark.svg`)
- **Size**: 280x64px
- **Use Cases**: Dark mode interfaces, dark backgrounds
- **Description**: Icon + "DailyForever" text optimized for dark backgrounds

### 4. Navbar Logo (`logo-navbar.svg`)
- **Size**: 200x40px
- **Use Cases**: Website navigation bar
- **Description**: Compact version optimized for header usage
- **Currently in use**: Main navigation bar

### 5. Minimal Logo (`logo-minimal.svg`)
- **Size**: 320x80px
- **Use Cases**: Large displays, presentations
- **Description**: Ultra-minimalist design with simplified icon

### 6. Monochrome Logo (`logo-monochrome.svg`)
- **Size**: 280x64px
- **Use Cases**: Single-color contexts, print materials
- **Description**: Single color version using `currentColor`

### 7. Favicon (`favicon.svg`)
- **Size**: 32x32px
- **Use Cases**: Browser tabs, bookmarks
- **Description**: Simplified shield design for small sizes

## Usage Guidelines

### Colors
- **Primary Blue**: #2563eb
- **Light Blue**: #60a5fa
- **Gradient**: Linear gradient from #60a5fa to #2563eb

### Spacing
- Maintain clear space around the logo equal to at least 25% of the logo height
- Never place the logo on busy backgrounds

### Don'ts
- Don't alter the colors unless using the monochrome version
- Don't stretch or distort the logo
- Don't add effects like shadows or outlines
- Don't separate the icon from text in the full logo versions

### Implementation Examples

#### HTML (Navbar)
```html
<a href="/" class="brand-mark">
  <img src="/images/logo-navbar.svg" alt="DailyForever" class="h-8 w-auto">
</a>
```

#### HTML (Footer)
```html
<img src="/images/logo-icon.svg" alt="DailyForever" class="h-8 w-8">
```

#### CSS (Monochrome with custom color)
```css
.logo-custom {
  color: #your-color;
  /* The monochrome logo will inherit this color */
}
```

## File Locations
All logo files are located in `/public/images/` directory.

## License
These logo designs are proprietary to DailyForever and should only be used in accordance with the DailyForever brand guidelines.
