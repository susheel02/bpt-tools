<?php
$page_title = "Photography Tools & Calculators";
$page_description = "Professional photography calculators and tools for photographers. Calculate depth of field, exposure settings, and more.";
$base_url = "/";

include 'shared/header.php';
?>

<main>
    <div class="tools-container">
        <div class="page-header">
            <h1>Photography Tools & Calculators</h1>
            <p>Professional calculators and tools designed specifically for photographers. Get precise calculations for your photographic needs.</p>
        </div>

        <div class="tools-grid">
            <!-- Depth of Field Calculator -->
            <div class="tool-card">
                <h3>Depth of Field Calculator</h3>
                <p>Calculate precise depth of field for any lens, sensor, and distance combination. Perfect for macro, portrait, and landscape photography.</p>
                <div class="features">
                    <ul>
                        <li>Multiple sensor format support (Full Frame, APS-C, Micro 4/3, Medium Format)</li>
                        <li>Visual diagram showing focus range</li>
                        <li>Percentage distribution of DOF front/behind subject</li>
                        <li>Hyperfocal distance calculations</li>
                        <li>Multiple units: meters, feet, centimeters, inches</li>
                        <li>Save and bookmark calculations</li>
                        <li>Dark/light mode toggle</li>
                        <li>Mobile responsive design</li>
                    </ul>
                </div>
                <a href="dof-calculator/" class="tool-link">Use DOF Calculator</a>
            </div>

            <!-- Exposure Calculator - Coming Soon -->
            <div class="tool-card coming-soon">
                <h3>Exposure Calculator</h3>
                <p>Calculate equivalent exposures and reciprocity relationships. Perfect for understanding exposure triangles and creative control.</p>
                <div class="features">
                    <ul>
                        <li>Equivalent exposure calculations</li>
                        <li>Shutter speed, aperture, and ISO relationships</li>
                        <li>ND filter compensation</li>
                        <li>Reciprocity failure calculations</li>
                        <li>Creative exposure suggestions</li>
                    </ul>
                </div>
                <a href="#" class="tool-link">Coming Soon</a>
            </div>

            <!-- Hyperfocal Distance Calculator - Coming Soon -->
            <div class="tool-card coming-soon">
                <h3>Field of View Calculator</h3>
                <p>Calculate field of view for different lens and sensor combinations. Plan your shots and understand lens coverage.</p>
                <div class="features">
                    <ul>
                        <li>Horizontal and vertical field of view</li>
                        <li>Subject distance calculations</li>
                        <li>Crop factor comparisons</li>
                        <li>Multiple sensor formats</li>
                        <li>Lens comparison tool</li>
                    </ul>
                </div>
                <a href="#" class="tool-link">Coming Soon</a>
            </div>

            <!-- Sun/Moon Calculator - Coming Soon -->
            <div class="tool-card coming-soon">
                <h3>Sun & Moon Calculator</h3>
                <p>Calculate sunrise, sunset, moonrise, and moonset times. Plan your golden hour and blue hour photography sessions.</p>
                <div class="features">
                    <ul>
                        <li>Sunrise and sunset times</li>
                        <li>Golden hour and blue hour calculations</li>
                        <li>Moon phase and position</li>
                        <li>Location-based calculations</li>
                        <li>Photography timeline planner</li>
                    </ul>
                </div>
                <a href="#" class="tool-link">Coming Soon</a>
            </div>

            <!-- Print Size Calculator - Coming Soon -->
            <div class="tool-card coming-soon">
                <h3>Print Size Calculator</h3>
                <p>Calculate optimal print sizes and resolution requirements for different viewing distances and print qualities.</p>
                <div class="features">
                    <ul>
                        <li>Print resolution calculations</li>
                        <li>Viewing distance optimization</li>
                        <li>Image upscaling recommendations</li>
                        <li>Print cost estimator</li>
                        <li>Paper size recommendations</li>
                    </ul>
                </div>
                <a href="#" class="tool-link">Coming Soon</a>
            </div>

            <!-- Macro Calculator - Coming Soon -->
            <div class="tool-card coming-soon">
                <h3>Macro Photography Calculator</h3>
                <p>Specialized calculations for macro photography including magnification ratios, working distances, and lighting setups.</p>
                <div class="features">
                    <ul>
                        <li>Magnification ratio calculations</li>
                        <li>Working distance optimization</li>
                        <li>Focus stacking parameters</li>
                        <li>Flash power calculations</li>
                        <li>Extension tube calculations</li>
                    </ul>
                </div>
                <a href="#" class="tool-link">Coming Soon</a>
            </div>
        </div>
    </div>
</main>

<?php include 'shared/footer.php'; ?>