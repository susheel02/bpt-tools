# Depth of Field Calculator

A web application for photographers to calculate depth of field for their lenses.

## Project Overview
- **Purpose**: Help photographers understand depth of field calculations
- **Target Users**: Photographers of all skill levels
- **Website**: Part of BeyondPhotoTips.com toolkit

## Technical Stack
- **Backend**: PHP 8.3
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: PHP VPS (production), MAMP (development)
- **Architecture**: Single-page application

## Features
### Core Functionality
- Depth of field calculation based on:
  - Lens focal length
  - Sensor/film size (presets + custom)
  - Distance to subject
  - Aperture (f-stop)
- Results show:
  - Distance in front of subject
  - Distance behind subject  
  - Total depth of field

### Sensor Support
- Common formats: Full Frame, APS-C, Micro 4/3
- Large format: Fujifilm GFX, Hasselblad medium format
- Custom sensor dimensions input

### Additional Features
- Unit conversion (metric/imperial)
- Visual side-view DOF illustration
- Hyperfocal distance calculation
- Save/bookmark calculations
- Mobile-responsive design

## Development Commands
- **Start MAMP**: Use MAMP control panel
- **Access locally**: http://localhost/dof-calc/
- **PHP version**: 8.3.9 (local), 8.3 (production)

## File Structure
```
/dof-calc/
├── index.php           # Main application
├── assets/
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── images/        # Visual assets
├── includes/
│   ├── calculations.php # DOF calculation logic
│   └── config.php     # Configuration
└── CLAUDE.md          # This documentation
```

## Calculation Notes
- Circle of confusion values vary by sensor size
- Hyperfocal distance applies when focused at infinity
- DOF calculations use standard photographic formulas