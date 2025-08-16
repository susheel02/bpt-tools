# Depth of Field Calculator

A professional depth of field calculator for photographers, designed to run on PHP servers and optimized for mobile use.

## Features

- **Comprehensive Sensor Support**: Includes presets for common sensors (Full Frame, APS-C, Micro 4/3) and large format sensors (Fujifilm GFX, Hasselblad)
- **Custom Sensor Input**: Enter custom sensor dimensions for any camera
- **Dual Unit Support**: Switch between metric (meters) and imperial (feet) measurements
- **Visual Representation**: Side-view SVG illustration showing depth of field zones
- **Hyperfocal Distance**: Calculate the optimal focus distance for maximum depth of field
- **Save Calculations**: Bookmark calculations for later reference (localStorage)
- **Mobile Optimized**: Responsive design with touch-friendly controls
- **PWA Ready**: Service worker and manifest for mobile app-like experience

## Technical Requirements

- **Server**: PHP 8.3+ (tested on PHP 8.3.9)
- **Browser**: Modern browsers with ES6+ support
- **Storage**: Uses localStorage for saved calculations

## Installation

1. Copy files to your web server directory
2. Ensure PHP 8.3+ is available
3. Access via web browser at `/dof-calc/`

## Usage

1. Select your camera sensor from the dropdown or enter custom dimensions
2. Enter lens focal length (mm)
3. Set aperture (f-stop)
4. Input distance to subject
5. Choose measurement units (meters/feet)
6. Click "Calculate Depth of Field"

## Results Display

- **Near/Far Distances**: The closest and farthest points in acceptable focus
- **DOF Front/Behind**: How much depth of field extends in front of and behind the subject
- **Total DOF**: Complete depth of field measurement
- **Hyperfocal Distance**: Focus distance for maximum DOF from half that distance to infinity
- **Visual Diagram**: SVG illustration showing camera, subject, and focus zones

## File Structure

```
/dof-calc/
├── index.php              # Main application
├── includes/
│   ├── calculations.php   # DOF calculation logic
│   └── config.php        # Sensor presets and configuration
├── assets/
│   ├── css/style.css     # Responsive styling
│   └── js/app.js         # Interactive functionality
├── manifest.json         # PWA manifest
├── sw.js                 # Service worker
├── CLAUDE.md             # Development documentation
└── README.md             # This file
```

## Calculation Formula

The app uses standard photographic depth of field formulas:

- **Hyperfocal Distance**: H = (f² / (N × c)) + f
- **Near Distance**: Dn = (H × D) / (H + D - f)
- **Far Distance**: Df = (H × D) / (H - D + f)

Where:
- f = focal length
- N = f-number (aperture)
- c = circle of confusion
- D = distance to subject
- H = hyperfocal distance

## Mobile Features

- Touch-friendly preset buttons
- Responsive grid layout
- Offline capability via service worker
- PWA installation support
- Optimized for portrait orientation

## Browser Support

- Chrome/Safari (iOS): Full support
- Firefox: Full support
- Edge: Full support
- Older browsers: Basic functionality (calculation works, some visual features may be limited)

## License

Part of the BeyondPhotoTips.com toolkit for photographers.