<?php
$page_title = "Photography Tools & Calculators";
$page_description = "Professional photography calculators and tools for photographers. Calculate depth of field, exposure settings, and more.";
$base_url = "./";

include 'shared/header.php';
?>

<!-- Dark Mode Toggle -->
<button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode" aria-label="Toggle dark mode" aria-pressed="false">
    🌙
</button>

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

            <!-- Print Size Calculator - Next -->
            <div class="tool-card">
                <h3>Print Size Calculator</h3>
                <p>Calculate optimal print sizes and resolution requirements for different viewing distances and print qualities. Essential for photographers planning prints.</p>
                <div class="features">
                    <ul>
                        <li>Print resolution calculations (DPI/PPI)</li>
                        <li>Viewing distance optimization</li>
                        <li>Image upscaling recommendations</li>
                        <li>Maximum print size from image dimensions</li>
                        <li>Paper size recommendations</li>
                        <li>Print quality guidelines</li>
                    </ul>
                </div>
                <a href="print-size-calculator/" class="tool-link">Use Print Calculator</a>
            </div>

            <!-- Field of View Calculator - Coming Soon -->
            <div class="tool-card coming-soon">
                <h3>Field of View Calculator</h3>
                <p>Calculate field of view for different lens and sensor combinations. Plan your shots and understand lens coverage for composition.</p>
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

    <script>
        // Dark mode functionality for main page
        class ThemeManager {
            constructor() {
                this.themeToggle = document.getElementById('theme-toggle');
                this.initializeDarkMode();
                this.initializeEventListeners();
            }
            
            initializeEventListeners() {
                this.themeToggle.addEventListener('click', () => {
                    this.toggleDarkMode();
                });
                
                this.themeToggle.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.toggleDarkMode();
                    }
                });
            }
            
            initializeDarkMode() {
                const isDarkMode = localStorage.getItem('darkMode') === 'true';
                if (isDarkMode) {
                    document.body.classList.add('dark-mode');
                    this.updateThemeToggleIcon(true);
                }
            }
            
            toggleDarkMode() {
                const isDarkMode = document.body.classList.toggle('dark-mode');
                localStorage.setItem('darkMode', isDarkMode);
                this.updateThemeToggleIcon(isDarkMode);
            }
            
            updateThemeToggleIcon(isDarkMode) {
                this.themeToggle.textContent = isDarkMode ? '☀️' : '🌙';
                this.themeToggle.setAttribute('title', isDarkMode ? 'Switch to light mode' : 'Switch to dark mode');
                this.themeToggle.setAttribute('aria-label', isDarkMode ? 'Switch to light mode' : 'Switch to dark mode');
                this.themeToggle.setAttribute('aria-pressed', isDarkMode.toString());
            }
        }
        
        // Initialize theme manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
        });
    </script>

<?php include 'shared/footer.php'; ?>