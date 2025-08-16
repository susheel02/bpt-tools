class PrintCalculator {
    constructor() {
        this.initializeEventListeners();
        this.initializeDarkMode();
    }
    
    initializeEventListeners() {
        // Mode toggle
        document.getElementById('mode-max-print').addEventListener('click', () => this.switchMode('max-print'));
        document.getElementById('mode-required-resolution').addEventListener('click', () => this.switchMode('required-resolution'));
        
        // Image source toggle
        document.getElementById('image-source').addEventListener('change', (e) => this.toggleImageSource(e.target.value));
        
        // Camera preset buttons
        document.querySelectorAll('.camera-preset-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.selectCameraPreset(e));
        });
        
        // Print size preset buttons
        document.querySelectorAll('.print-size-preset').forEach(preset => {
            preset.addEventListener('click', (e) => this.selectPrintSizePreset(e));
        });
        
        // Form submissions
        document.getElementById('max-print-form').addEventListener('submit', (e) => this.handleMaxPrintSubmit(e));
        document.getElementById('required-resolution-form').addEventListener('submit', (e) => this.handleRequiredResolutionSubmit(e));
        
        // Dark mode toggle
        document.getElementById('theme-toggle').addEventListener('click', () => this.toggleDarkMode());
        
        // Real-time calculation triggers
        this.setupRealTimeCalculations();
    }
    
    switchMode(mode) {
        // Update toggle buttons
        document.querySelectorAll('.mode-toggle button').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`mode-${mode}`).classList.add('active');
        
        // Show/hide calculator modes
        document.querySelectorAll('.calculator-mode').forEach(section => section.classList.remove('active'));
        document.getElementById(`calculator-${mode}`).classList.add('active');
        
        // Hide previous results
        document.getElementById('max-print-results').style.display = 'none';
        document.getElementById('required-resolution-results').style.display = 'none';
    }
    
    toggleImageSource(source) {
        const dimensionsInputs = document.querySelectorAll('#dimensions-input, #dimensions-input-2');
        const megapixelsInput = document.getElementById('megapixels-input');
        
        if (source === 'megapixels') {
            dimensionsInputs.forEach(input => input.style.display = 'none');
            megapixelsInput.style.display = 'block';
        } else {
            dimensionsInputs.forEach(input => input.style.display = 'block');
            megapixelsInput.style.display = 'none';
        }
    }
    
    selectCameraPreset(e) {
        e.preventDefault();
        const mp = e.target.dataset.mp;
        const width = e.target.dataset.width;
        const height = e.target.dataset.height;
        
        // Update megapixels input
        document.getElementById('camera-megapixels').value = mp;
        
        // Also update dimensions for consistency
        document.getElementById('width-pixels').value = width;
        document.getElementById('height-pixels').value = height;
        
        // Visual feedback
        document.querySelectorAll('.camera-preset-btn').forEach(btn => btn.classList.remove('selected'));
        e.target.classList.add('selected');
    }
    
    selectPrintSizePreset(e) {
        const width = e.currentTarget.dataset.width;
        const height = e.currentTarget.dataset.height;
        
        document.getElementById('target-width').value = width;
        document.getElementById('target-height').value = height;
        
        // Visual feedback
        document.querySelectorAll('.print-size-preset').forEach(preset => preset.classList.remove('selected'));
        e.currentTarget.classList.add('selected');
    }
    
    async handleMaxPrintSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const imageSource = formData.get('image_source');
        
        // Determine pixel dimensions
        let widthPixels, heightPixels;
        
        if (imageSource === 'megapixels') {
            const megapixels = parseFloat(formData.get('camera_megapixels') || document.getElementById('camera-megapixels').value);
            // Calculate dimensions assuming 3:2 aspect ratio
            const totalPixels = megapixels * 1000000;
            widthPixels = Math.round(Math.sqrt(totalPixels * 1.5));
            heightPixels = Math.round(widthPixels / 1.5);
        } else {
            widthPixels = parseInt(formData.get('width_pixels'));
            heightPixels = parseInt(formData.get('height_pixels'));
        }
        
        const data = {
            action: 'calculate_max_print',
            width_pixels: widthPixels,
            height_pixels: heightPixels,
            print_medium: formData.get('print_medium')
        };
        
        try {
            this.showLoading('max-print-results');
            const response = await this.makeRequest(data);
            
            if (response.success) {
                this.displayMaxPrintResults(response.data, widthPixels, heightPixels);
            } else {
                this.showError('max-print-results', response.error);
            }
        } catch (error) {
            this.showError('max-print-results', 'Calculation failed. Please try again.');
            console.error('Calculation error:', error);
        }
    }
    
    async handleRequiredResolutionSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            action: 'calculate_required_resolution',
            print_width: parseFloat(formData.get('print_width')),
            print_height: parseFloat(formData.get('print_height')),
            quality_tier: formData.get('quality_tier'),
            print_medium: formData.get('print_medium')
        };
        
        try {
            this.showLoading('required-resolution-results');
            const response = await this.makeRequest(data);
            
            if (response.success) {
                this.displayRequiredResolutionResults(response.data);
            } else {
                this.showError('required-resolution-results', response.error);
            }
        } catch (error) {
            this.showError('required-resolution-results', 'Calculation failed. Please try again.');
            console.error('Calculation error:', error);
        }
    }
    
    async makeRequest(data) {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        });
        
        const responseText = await response.text();
        
        try {
            return JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response was:', responseText);
            throw new Error('Invalid JSON response from server');
        }
    }
    
    displayMaxPrintResults(recommendations, widthPixels, heightPixels) {
        const resultsContainer = document.getElementById('max-print-results');
        const megapixels = ((widthPixels * heightPixels) / 1000000).toFixed(1);
        
        let html = `
            <div class="result-card">
                <h4>Your Image: ${widthPixels} × ${heightPixels} pixels (${megapixels} MP)</h4>
                <p>Here are the maximum print sizes recommended for different quality levels:</p>
            </div>
        `;
        
        // Create quality tier results
        Object.entries(recommendations).forEach(([tierKey, tierData]) => {
            const tierInfo = tierData.tier_info;
            const maxSize = tierData.max_size;
            const suitableSizes = tierData.suitable_common_sizes.slice(0, 6); // Show top 6 sizes
            
            html += `
                <div class="result-card">
                    <h4 style="color: ${tierInfo.color}">${tierInfo.name}</h4>
                    <p><strong>Maximum Size:</strong> ${maxSize.max_width_inches.toFixed(1)}" × ${maxSize.max_height_inches.toFixed(1)}" at ${maxSize.effective_ppi.toFixed(0)} PPI</p>
                    <p><small>${tierInfo.description}</small></p>
                    
                    ${suitableSizes.length > 0 ? `
                        <div class="print-sizes-grid">
                            ${suitableSizes.map(size => `
                                <div class="print-size-item">
                                    <div class="size-name">${size.size_key}</div>
                                    <div class="size-dimensions">${size.width}" × ${size.height}"</div>
                                    <div class="quality-indicator quality-${size.quality.quality_level}">
                                        ${size.quality.quality_level.charAt(0).toUpperCase() + size.quality.quality_level.slice(1)}
                                        (${size.quality.quality_percentage.toFixed(0)}%)
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p><em>No common print sizes fit within this quality tier for your image resolution.</em></p>'}
                </div>
            `;
        });
        
        // Add recommendations
        html += this.generateRecommendations(recommendations, widthPixels, heightPixels);
        
        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
        resultsContainer.scrollIntoView({ behavior: 'smooth' });
    }
    
    displayRequiredResolutionResults(data) {
        const resultsContainer = document.getElementById('required-resolution-results');
        const required = data.required_resolution;
        const cameras = data.camera_recommendations;
        
        let html = `
            <div class="result-card">
                <h4>Required Resolution for ${required.target_width_inches}" × ${required.target_height_inches}" Print</h4>
                <p><strong>Required Dimensions:</strong> ${required.required_width_pixels.toLocaleString()} × ${required.required_height_pixels.toLocaleString()} pixels</p>
                <p><strong>Required Megapixels:</strong> ${required.required_megapixels} MP</p>
                <p><strong>Effective PPI:</strong> ${required.effective_ppi.toFixed(0)}</p>
            </div>
        `;
        
        if (cameras.length > 0) {
            html += `
                <div class="result-card">
                    <h4>Camera Recommendations</h4>
                    <p>These cameras have sufficient resolution for your target print:</p>
                    <div class="print-sizes-grid">
                        ${cameras.map(camera => `
                            <div class="print-size-item">
                                <div class="size-name">${camera.megapixels} MP</div>
                                <div class="size-dimensions">${camera.width} × ${camera.height}</div>
                                <small>${camera.description}</small>
                                ${camera.excess_resolution > 0 ? `<br><small style="color: #27ae60;">+${camera.excess_resolution} MP extra</small>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }
        
        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
        resultsContainer.scrollIntoView({ behavior: 'smooth' });
    }
    
    generateRecommendations(recommendations, widthPixels, heightPixels) {
        const megapixels = ((widthPixels * heightPixels) / 1000000);
        let tips = [];
        
        // Analyze the results and generate tips
        const gallerySize = recommendations.gallery?.max_size;
        const standardSize = recommendations.standard?.max_size;
        
        if (gallerySize && gallerySize.max_width_inches < 8) {
            tips.push({
                icon: 'warning',
                text: `With ${megapixels.toFixed(1)} MP, gallery-quality prints are limited to ${gallerySize.max_width_inches.toFixed(1)}" wide. Consider higher resolution for large fine art prints.`
            });
        }
        
        if (standardSize && standardSize.max_width_inches >= 16) {
            tips.push({
                icon: 'tip',
                text: `Great resolution! You can make quality 16×20" prints and larger for home display.`
            });
        }
        
        if (megapixels < 12) {
            tips.push({
                icon: 'info',
                text: 'For larger prints, consider AI upscaling software or upgrading to a higher resolution camera.'
            });
        }
        
        if (tips.length === 0) {
            tips.push({
                icon: 'tip',
                text: 'Your image has good resolution for most printing needs. Choose your print size based on intended viewing distance and display location.'
            });
        }
        
        return `
            <div class="recommendations">
                <h4>Recommendations</h4>
                ${tips.map(tip => `
                    <div class="recommendation-item">
                        <div class="recommendation-icon ${tip.icon}">${tip.icon === 'tip' ? '💡' : tip.icon === 'warning' ? '⚠️' : 'ℹ️'}</div>
                        <div>${tip.text}</div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    showLoading(containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '<div class="result-card"><p>Calculating...</p></div>';
        container.style.display = 'block';
    }
    
    showError(containerId, message) {
        const container = document.getElementById(containerId);
        container.innerHTML = `<div class="result-card"><p style="color: #e74c3c;">Error: ${message}</p></div>`;
        container.style.display = 'block';
    }
    
    setupRealTimeCalculations() {
        // Add debounced real-time calculations for better UX
        let timeout;
        
        const inputs = document.querySelectorAll('input[type="number"], select');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    // Could trigger automatic calculations here
                }, 1000);
            });
        });
    }
    
    initializeDarkMode() {
        // Check for saved dark mode preference
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
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.textContent = isDarkMode ? '☀️' : '🌙';
        themeToggle.setAttribute('title', isDarkMode ? 'Switch to light mode' : 'Switch to dark mode');
    }
}

// Initialize the calculator when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.printCalc = new PrintCalculator();
});