# Photography Tools Suite - BeyondPhotoTips.com

A comprehensive collection of professional photography calculators and tools for photographers.

## Project Overview
- **Purpose**: Help photographers with precise calculations for various photographic scenarios
- **Target Users**: Photographers of all skill levels - from beginners to professionals
- **Website**: Part of BeyondPhotoTips.com toolkit
- **Vision**: Become the go-to resource for photography calculations and technical planning

## Current Tools Suite

### 1. Depth of Field Calculator ✅ COMPLETE
**Location**: `/dof-calculator/`
- Calculate DOF based on focal length, sensor size, distance, and aperture
- Multiple sensor format support (Full Frame, APS-C, Micro 4/3, Medium Format)
- Visual SVG diagram showing focus range
- Percentage distribution display (front/behind subject)
- Hyperfocal distance calculations
- Multiple units: meters, feet, centimeters, inches
- Save/bookmark functionality
- Dark/light mode toggle

### 2. Print Size Calculator 🚧 IN DEVELOPMENT
**Location**: `/print-size-calculator/`
- Calculate optimal print sizes from image resolution
- Determine required megapixels for target print sizes
- Print quality recommendations based on viewing distance
- Support for different print media (photo paper, canvas, metal, fine art)
- PPI/DPI education and guidance
- Quality tiers: Gallery/Fine Art, Standard Photo, Casual, Large Format
- Real-world scenario recommendations

### 3. Field of View Calculator 📋 PLANNED
**Location**: `/field-of-view-calculator/`
- Calculate horizontal and vertical field of view
- Lens and sensor combination planning
- Crop factor comparisons
- Subject framing calculations

### 4. Macro Photography Calculator 📋 PLANNED  
**Location**: `/macro-calculator/`
- Magnification ratio calculations
- Working distance optimization
- Focus stacking parameters
- Extension tube calculations

## Technical Architecture

### Technology Stack
- **Backend**: PHP 8.3
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Server**: PHP VPS (production), MAMP (development)
- **Architecture**: Multi-page application with shared components

### Project Structure
```
/tools-root/
├── index.php                    # Tools landing page
├── shared/
│   ├── header.php              # Consistent header template
│   └── footer.php              # Consistent footer template
├── assets/
│   └── css/
│       └── tools.css           # Shared styling (v0.0.2)
├── dof-calculator/             # Depth of Field Calculator
│   ├── index.php              # Calculator application
│   ├── assets/                # Calculator-specific assets
│   ├── includes/              # PHP calculation logic
│   └── .htaccess              # Security configuration
├── print-size-calculator/      # Print Size Calculator (in development)
├── .htaccess                   # Root security configuration
└── .git/                       # Version control
```

### Shared Components
- **Header/Footer**: Consistent BeyondPhotoTips.com branding
- **Styling**: Shared CSS with tool-specific overrides
- **Security**: .htaccess protection for all directories
- **Analytics**: Google Analytics integration
- **SEO**: Optimized meta descriptions and structured navigation

### Development Standards
- **CSS Versioning**: Cache-busting with version numbers (current: v0.0.2)
- **Responsive Design**: Mobile-first approach
- **Accessibility**: WCAG guidelines compliance
- **Performance**: Optimized loading and caching
- **Security**: Input validation, XSS protection, secure headers

## Development Workflow

### Local Development
- **Environment**: MAMP with PHP 8.3
- **Access**: http://localhost/dof-calc/ (or your local path)
- **Testing**: Cross-browser testing on mobile and desktop

### Deployment
- **Production**: PHP VPS server
- **Security**: .htaccess configurations for protection
- **Analytics**: Google Analytics tracking (G-ZZXPCE599N)

### Version Control
- **Repository**: Git with descriptive commit messages
- **Branching**: Main branch for stable releases
- **Documentation**: This CLAUDE.md file and individual tool documentation

## Calculator Development Guidelines

### UI/UX Principles
1. **Intuitive Interface**: Clear labels, logical flow
2. **Educational Value**: Explain concepts, not just calculate
3. **Mobile Responsive**: Touch-friendly controls
4. **Visual Feedback**: Progress indicators, real-time updates
5. **Save/Bookmark**: Allow users to save calculations

### Code Organization
1. **Separation of Concerns**: Logic in PHP, presentation in HTML/CSS
2. **Reusable Components**: Shared functions and styling
3. **Input Validation**: Both client and server-side
4. **Error Handling**: Graceful failure with helpful messages
5. **Performance**: Efficient calculations and minimal resources

### Feature Requirements
- **Multiple Input Methods**: Sliders, dropdowns, text inputs
- **Real-time Calculations**: Update as user types/selects
- **Educational Content**: Tooltips, help text, explanations
- **Quality Indicators**: Visual feedback on calculation quality
- **Export Options**: Save, print, or share results

## Future Enhancements
- Additional specialized calculators based on user feedback
- Advanced features for professional photographers
- Integration with camera databases (when appropriate)
- Educational content and tutorials
- Community features (user submissions, favorites)

## Maintenance Notes
- **CSS Versioning**: Increment version numbers when updating styles
- **Security Updates**: Regular .htaccess and PHP security reviews  
- **Performance Monitoring**: Track loading times and user engagement
- **User Feedback**: Collect and implement user suggestions
- **Browser Compatibility**: Test with major browsers and devices

---

**Last Updated**: August 2025  
**Current Version**: v0.0.2  
**Active Development**: Print Size Calculator