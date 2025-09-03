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

### 2. Print Size Calculator ✅ COMPLETE
**Location**: `/print-size-calculator/`
- Calculate optimal print sizes from image resolution
- Determine required megapixels for target print sizes
- Print quality recommendations based on viewing distance
- Support for different print media (photo paper, canvas, metal, fine art)
- PPI/DPI education and guidance
- Quality tiers: Gallery/Fine Art, Standard Photo, Casual, Large Format
- Real-world scenario recommendations

### 3. Equipment Investment Calculator ✅ COMPLETE
**Location**: `/equipment-investment-calculator/`
- Rent vs buy financial analysis for photography equipment
- Multi-currency support (USD, EUR, GBP, CAD, AUD, JPY, INR)
- Depreciation calculations (20% annually over 5-year standard)
- Maintenance cost analysis (20% of purchase price over lifetime)
- Break-even analysis with usage frequency patterns
- 5-year total cost projections and comparisons
- Professional financial guidance with debt warnings
- Annual and per-usage cost breakdowns
- Dark/light mode toggle and responsive design

### 4. Field of View Calculator 📋 PLANNED
**Location**: `/field-of-view-calculator/`
- Calculate horizontal and vertical field of view
- Lens and sensor combination planning
- Crop factor comparisons
- Subject framing calculations

### 5. Macro Photography Calculator 📋 PLANNED  
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

The project follows a **distributed modular structure** where each calculator tool is self-contained with its own assets and logic. This approach was chosen after careful analysis for optimal maintainability, scalability, and development workflow.

```
/tools-root/
├── index.php                    # Tools landing page
├── shared/                      # SHARED components only
│   ├── header.php              # Consistent header template
│   └── footer.php              # Consistent footer template
├── assets/                      # SHARED assets only
│   ├── css/
│   │   └── tools.css           # Global styling framework (v0.0.3)
│   ├── js/
│   │   └── common.js           # Shared utilities (if needed)
│   └── images/
│       └── shared/             # Shared icons, logos, etc.
├── includes/                    # SHARED includes only
│   ├── common-functions.php    # Shared utility functions
│   └── validation.php          # Shared validation logic
├── dof-calculator/             # DOF Calculator (self-contained)
│   ├── index.php              # Calculator application
│   ├── assets/                # Tool-specific assets
│   │   ├── css/style.css      # DOF-specific styling
│   │   ├── js/app.js          # DOF-specific JavaScript
│   │   └── images/            # DOF-specific images
│   ├── includes/              # DOF-specific logic
│   │   ├── calculations.php   # DOF calculation functions
│   │   └── config.php         # DOF configuration
│   └── .htaccess              # Security configuration
├── print-size-calculator/      # Print Calculator (self-contained)
│   ├── index.php              # Calculator application
│   ├── assets/                # Tool-specific assets
│   │   ├── css/print-calculator.css
│   │   ├── js/print-calculator.js
│   │   └── images/
│   ├── includes/              # Print-specific logic
│   │   ├── calculations.php   # Print calculation functions
│   │   └── config.php         # Print configuration
│   └── .htaccess              # Security configuration
├── equipment-investment-calculator/  # Investment Calculator (self-contained)
│   ├── index.php              # Calculator application
│   ├── assets/                # Tool-specific assets
│   │   ├── css/style.css      # Investment-specific styling
│   │   ├── js/app.js          # Investment-specific JavaScript
│   │   └── images/            # Investment-specific images
│   ├── includes/              # Investment-specific logic
│   │   ├── calculations.php   # Investment calculation functions
│   │   └── config.php         # Investment configuration
│   └── .htaccess              # Security configuration
├── .htaccess                   # Root security configuration
└── .git/                       # Version control
```

#### Structure Design Rationale

**Distributed Modular Approach Benefits:**
- **Clear Separation of Concerns**: Each tool is self-contained with its own assets and logic
- **Modular Development**: Developers can work on individual tools without affecting others
- **Easy Tool Management**: Adding, removing, or archiving tools is straightforward
- **Independent Deployment**: Individual tools can be deployed or updated separately
- **Reduced Naming Conflicts**: Tool-specific files can use similar names without collision
- **Scalable Architecture**: New tools can be added without restructuring existing ones

**File Organization Principles:**
- **Root-level folders**: Contain only truly shared resources used by 3+ tools
- **Tool-specific folders**: Maintain independence with their own assets/ and includes/
- **Clear Boundaries**: Shared vs. tool-specific resources are easily identifiable
- **Intuitive Navigation**: Developers can quickly locate tool-specific vs. shared files

### Shared Components
- **Header/Footer**: Consistent BeyondPhotoTips.com branding
- **Styling**: Shared CSS with tool-specific overrides
- **Security**: .htaccess protection for all directories
- **Analytics**: Google Analytics integration
- **SEO**: Optimized meta descriptions and structured navigation

### Development Standards
- **CSS Versioning**: Cache-busting with version numbers (current: v0.0.3)
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
2. **Reusable Components**: Shared functions and styling for input elements and overall page structure
3. **Input Validation**: Both client and server-side
4. **Error Handling**: Graceful failure with helpful messages
5. **Performance**: Efficient calculations and minimal resources

### File Organization Guidelines

#### Shared vs. Tool-Specific Decision Matrix

**Move to shared/ when:**
- Used by 3+ tools
- Core functionality (authentication, validation, utilities)
- Brand assets (logos, common UI components)
- Performance critical (can be cached globally)

**Keep in tool-specific folders when:**
- Unique to one tool
- Tool-specific configurations
- Domain-specific calculations
- Tool-specific UI styling

#### File Naming Conventions
- **Shared assets**: Use generic names (`tools.css`, `common.js`)
- **Tool-specific assets**: Use descriptive names that indicate the tool
  - `/dof-calculator/assets/css/style.css` (clear context from path)
  - `/dof-calculator/assets/js/app.js` (main application file)
  - `/print-size-calculator/assets/css/print-calculator.css` (descriptive name)

#### Development Workflow Benefits
1. **New Tool Creation**: Simply copy an existing tool folder structure
2. **Asset Management**: Clear path resolution (`../assets/css/tools.css` for shared, `assets/css/style.css` for local)
3. **Code Maintenance**: Changes are scoped to specific tools unless intentionally shared
4. **Team Collaboration**: Multiple developers can work on different tools without conflicts

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

**Last Updated**: September 2025  
**Current Version**: v0.0.4  
**Active Development**: Field of View Calculator