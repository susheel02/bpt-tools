class DOFCalculator {
    constructor() {
        this.form = document.getElementById('dof-form');
        this.resultsSection = document.getElementById('results');
        this.savedSection = document.getElementById('saved-calculations');
        this.currentCalculation = null;
        
        this.initializeEventListeners();
        this.loadSavedCalculations();
        this.initializeDarkMode();
    }
    
    initializeEventListeners() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Sensor preset selection
        document.getElementById('sensor-preset').addEventListener('change', (e) => {
            this.handleSensorPresetChange(e);
        });
        
        // Preset buttons for focal length and aperture
        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handlePresetClick(e));
        });
        
        // Unit system change
        document.getElementById('unit-system').addEventListener('change', (e) => {
            this.handleUnitChange(e);
        });
        
        // Save calculation
        document.getElementById('save-calculation').addEventListener('click', () => {
            this.saveCurrentCalculation();
        });
        
        // Show saved calculations
        document.getElementById('show-saved').addEventListener('click', () => {
            this.toggleSavedCalculations();
        });
        
        // Dark mode toggle
        document.getElementById('theme-toggle').addEventListener('click', () => {
            this.toggleDarkMode();
        });
    }
    
    handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData.entries());
        
        // Get COC value from sensor selection or calculate from custom
        const coc = this.getCircleOfConfusion(data);
        
        if (!coc) {
            this.showError('Please select a camera sensor from the dropdown above to calculate depth of field');
            // Highlight the sensor selection field
            const sensorField = document.getElementById('sensor-preset').closest('.form-group');
            sensorField.classList.add('has-error');
            setTimeout(() => sensorField.classList.remove('has-error'), 3000);
            return;
        }
        
        // Convert distance to meters if needed
        let distance = parseFloat(data.distance);
        switch(data.unit_system) {
            case 'imperial':
                distance = distance / 3.28084; // feet to meters
                break;
            case 'centimeters':
                distance = distance / 100; // cm to meters
                break;
            case 'inches':
                distance = distance / 39.3701; // inches to meters
                break;
            default: // metric
                // distance already in meters
                break;
        }
        
        const calculationData = {
            action: 'calculate',
            focal_length: data.focal_length,
            aperture: data.aperture,
            distance: distance,
            coc: coc,
            unit_system: data.unit_system
        };
        
        this.submitCalculation(calculationData);
    }
    
    getCircleOfConfusion(data) {
        const sensorPreset = document.getElementById('sensor-preset').value;
        
        if (sensorPreset && sensorPreset !== 'custom') {
            const selectedOption = document.querySelector(`#sensor-preset option[value="${sensorPreset}"]`);
            return parseFloat(selectedOption.dataset.coc);
        } else if (sensorPreset === 'custom') {
            const width = parseFloat(data.sensor_width);
            const height = parseFloat(data.sensor_height);
            
            if (!width || !height) return null;
            
            // Calculate COC as diagonal / 1500 (common formula)
            const diagonal = Math.sqrt(width * width + height * height);
            return diagonal / 1500;
        }
        
        return null;
    }
    
    async submitCalculation(data) {
        this.setLoadingState(true);
        
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            });
            
            console.log('Response status:', response.status);
            
            // Get the raw response text first
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            // Try to parse as JSON
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('Parsed JSON:', result);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                console.error('Response was:', responseText);
                throw new Error('Invalid JSON response from server');
            }
            
            if (result.error) {
                this.showError(result.error);
            } else {
                this.currentCalculation = {
                    input: data,
                    output: result.data,
                    timestamp: new Date().toISOString()
                };
                this.displayResults(result.data);
                this.createVisualization(result.data);
            }
        } catch (error) {
            this.showError('Calculation failed. Please try again.');
            console.error('Calculation error:', error);
        } finally {
            this.setLoadingState(false);
        }
    }
    
    displayResults(data) {
        const unit = data.unit || 'm';
        
        // Calculate percentages for DOF front and behind
        const dofFrontValue = data.dof_front === 'infinity' || !isFinite(data.dof_front) ? 0 : data.dof_front;
        const dofBehindValue = data.dof_behind === 'infinity' || !isFinite(data.dof_behind) ? 0 : data.dof_behind;
        const totalDofValue = data.total_dof === 'infinity' || !isFinite(data.total_dof) ? dofFrontValue : data.total_dof;
        
        let frontPercentage = 0;
        let behindPercentage = 0;
        
        if (totalDofValue > 0 && isFinite(totalDofValue)) {
            frontPercentage = Math.round((dofFrontValue / totalDofValue) * 100);
            behindPercentage = data.dof_behind === 'infinity' || !isFinite(data.dof_behind) ? 100 - frontPercentage : Math.round((dofBehindValue / totalDofValue) * 100);
        }
        
        // Update result displays
        document.getElementById('near-distance').textContent = this.formatDistance(data.near_distance, unit);
        document.getElementById('far-distance').textContent = this.formatDistance(data.far_distance, unit);
        document.getElementById('dof-front').textContent = `${this.formatDistance(data.dof_front, unit)} (${frontPercentage}%)`;
        document.getElementById('dof-behind').textContent = `${this.formatDistance(data.dof_behind, unit)} (${behindPercentage}%)`;
        document.getElementById('total-dof').textContent = this.formatDistance(data.total_dof, unit);
        document.getElementById('hyperfocal-distance').textContent = this.formatDistance(data.hyperfocal_distance, unit);
        document.getElementById('circle-of-confusion').textContent = `${data.coc.toFixed(3)} mm`;
        
        // Show results section
        this.resultsSection.style.display = 'block';
        this.resultsSection.scrollIntoView({ behavior: 'smooth' });
    }
    
    createVisualization(data) {
        const container = document.getElementById('dof-visualization');
        container.innerHTML = '';
        
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '200');
        svg.setAttribute('viewBox', '0 0 800 200');
        
        // Calculate visualization scale
        const dofBehind = (data.dof_behind === 'infinity' || !isFinite(data.dof_behind)) ? data.dof_front * 2 : data.dof_behind;
        const maxDistance = Math.max(
            data.subject_distance + dofBehind,
            data.subject_distance + data.dof_front * 2
        );
        
        const scale = 600 / maxDistance; // 600px for the visualization area
        const baseY = 100;
        const startX = 50;
        
        // Camera position
        this.createSVGRect(svg, 10, baseY - 15, 30, 30, '#2c3e50', 'Camera');
        
        // Ground line
        this.createSVGLine(svg, 0, baseY + 20, 800, baseY + 20, '#ccc', 1);
        
        // Subject position
        const subjectX = startX + (data.subject_distance * scale);
        this.createSVGRect(svg, subjectX - 5, baseY - 25, 10, 50, '#e74c3c', 'Subject');
        
        // DOF zone (in focus area)
        const nearX = startX + (data.near_distance * scale);
        const farX = (data.far_distance === 'infinity' || !isFinite(data.far_distance))
            ? 750 // Edge of visualization if infinite
            : startX + (data.far_distance * scale);
        
        this.createSVGRect(svg, nearX, baseY - 10, farX - nearX, 20, 'rgba(46, 204, 113, 0.3)', 'Depth of Field');
        
        // Focus markers
        this.createSVGLine(svg, nearX, baseY - 30, nearX, baseY + 30, '#27ae60', 2);
        this.createSVGLine(svg, farX, baseY - 30, farX, baseY + 30, '#27ae60', 2);
        
        // Labels
        this.createSVGText(svg, 25, baseY - 25, 'Camera', '12px', '#2c3e50');
        this.createSVGText(svg, subjectX - 15, baseY - 35, 'Subject', '12px', '#e74c3c');
        this.createSVGText(svg, nearX - 15, baseY - 40, 'Near', '10px', '#27ae60');
        
        if (data.far_distance === 'infinity' || !isFinite(data.far_distance)) {
            this.createSVGText(svg, farX - 15, baseY - 40, '∞', '12px', '#27ae60');
        } else {
            this.createSVGText(svg, farX - 10, baseY - 40, 'Far', '10px', '#27ae60');
        }
        
        container.appendChild(svg);
    }
    
    createSVGRect(parent, x, y, width, height, fill, title = '') {
        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        rect.setAttribute('x', x);
        rect.setAttribute('y', y);
        rect.setAttribute('width', width);
        rect.setAttribute('height', height);
        rect.setAttribute('fill', fill);
        if (title) rect.appendChild(this.createSVGTitle(title));
        parent.appendChild(rect);
        return rect;
    }
    
    createSVGLine(parent, x1, y1, x2, y2, stroke, strokeWidth = 1) {
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', x1);
        line.setAttribute('y1', y1);
        line.setAttribute('x2', x2);
        line.setAttribute('y2', y2);
        line.setAttribute('stroke', stroke);
        line.setAttribute('stroke-width', strokeWidth);
        parent.appendChild(line);
        return line;
    }
    
    createSVGText(parent, x, y, text, fontSize = '12px', fill = '#000') {
        const textElement = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        textElement.setAttribute('x', x);
        textElement.setAttribute('y', y);
        textElement.setAttribute('font-size', fontSize);
        textElement.setAttribute('fill', fill);
        textElement.setAttribute('font-family', 'sans-serif');
        textElement.textContent = text;
        parent.appendChild(textElement);
        return textElement;
    }
    
    createSVGTitle(text) {
        const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
        title.textContent = text;
        return title;
    }
    
    handleSensorPresetChange(e) {
        const customSensor = document.getElementById('custom-sensor');
        if (e.target.value === 'custom') {
            customSensor.classList.add('show');
        } else {
            customSensor.classList.remove('show');
        }
    }
    
    handlePresetClick(e) {
        e.preventDefault();
        const value = e.target.dataset.value;
        const parentGroup = e.target.closest('.form-group');
        const input = parentGroup.querySelector('input');
        
        if (input) {
            input.value = value;
            input.focus();
        }
    }
    
    handleUnitChange(e) {
        // If we have current results, recalculate with new units
        if (this.currentCalculation) {
            const newUnitSystem = e.target.value;
            const calculationData = {
                ...this.currentCalculation.input,
                unit_system: newUnitSystem
            };
            this.submitCalculation(calculationData);
        }
    }
    
    saveCurrentCalculation() {
        if (!this.currentCalculation) {
            this.showError('No calculation to save');
            return;
        }
        
        const saved = this.getSavedCalculations();
        const calculation = {
            ...this.currentCalculation,
            id: Date.now().toString(),
            name: this.generateCalculationName(),
            sensorInfo: this.getSensorInfo()
        };
        
        saved.push(calculation);
        localStorage.setItem('dof_calculations', JSON.stringify(saved));
        
        this.showSuccess('Calculation saved successfully!');
        this.loadSavedCalculations();
    }
    
    generateCalculationName() {
        const input = this.currentCalculation.input;
        const sensorInfo = this.getSensorInfo();
        return `${sensorInfo.name}: ${input.focal_length}mm f/${input.aperture} @ ${input.distance}${input.unit_system === 'metric' ? 'm' : 'ft'}`;
    }
    
    getSensorInfo() {
        const sensorPreset = document.getElementById('sensor-preset').value;
        
        if (sensorPreset && sensorPreset !== 'custom') {
            const selectedOption = document.querySelector(`#sensor-preset option[value="${sensorPreset}"]`);
            return {
                name: selectedOption.textContent.trim(),
                type: 'preset'
            };
        } else if (sensorPreset === 'custom') {
            const width = document.getElementById('sensor-width').value;
            const height = document.getElementById('sensor-height').value;
            return {
                name: `Custom (${width}×${height}mm)`,
                type: 'custom'
            };
        }
        
        return {
            name: 'Unknown Sensor',
            type: 'unknown'
        };
    }
    
    getSavedCalculations() {
        const saved = localStorage.getItem('dof_calculations');
        return saved ? JSON.parse(saved) : [];
    }
    
    loadSavedCalculations() {
        const saved = this.getSavedCalculations();
        const container = document.getElementById('saved-list');
        
        if (saved.length === 0) {
            container.innerHTML = '<p class="text-center">No saved calculations yet.</p>';
            return;
        }
        
        container.innerHTML = saved.map(calc => {
            const output = calc.output;
            const unit = output.unit || 'm';
            
            return `
            <div class="saved-item">
                <div class="saved-item-info">
                    <div class="saved-item-title">
                        <strong>${calc.name}</strong>
                    </div>
                    <div class="saved-item-details">
                        <div class="saved-item-results">
                            <span class="result-label">Near:</span> ${this.formatDistance(output.near_distance, unit)}
                            <span class="result-separator">•</span>
                            <span class="result-label">Far:</span> ${this.formatDistance(output.far_distance, unit)}
                            <span class="result-separator">•</span>
                            <span class="result-label">Total DOF:</span> ${this.formatDistance(output.total_dof, unit)}
                        </div>
                        <div class="saved-item-date">
                            <small>Saved: ${new Date(calc.timestamp).toLocaleDateString()}</small>
                        </div>
                    </div>
                </div>
                <div class="saved-item-actions">
                    <button onclick="dofCalc.loadCalculation('${calc.id}')">Load</button>
                    <button onclick="dofCalc.deleteCalculation('${calc.id}')">Delete</button>
                </div>
            </div>
            `;
        }).join('');
    }
    
    loadCalculation(id) {
        const saved = this.getSavedCalculations();
        const calculation = saved.find(calc => calc.id === id);
        
        if (!calculation) return;
        
        // Populate form with saved values
        const input = calculation.input;
        document.getElementById('focal-length').value = input.focal_length;
        document.getElementById('aperture').value = input.aperture;
        document.getElementById('distance').value = input.distance;
        document.getElementById('unit-system').value = input.unit_system;
        
        // Display results
        this.currentCalculation = calculation;
        this.displayResults(calculation.output);
        this.createVisualization(calculation.output);
        
        // Scroll to results
        this.resultsSection.scrollIntoView({ behavior: 'smooth' });
    }
    
    deleteCalculation(id) {
        if (!confirm('Delete this saved calculation?')) return;
        
        const saved = this.getSavedCalculations();
        const filtered = saved.filter(calc => calc.id !== id);
        localStorage.setItem('dof_calculations', JSON.stringify(filtered));
        
        this.loadSavedCalculations();
        this.showSuccess('Calculation deleted');
    }
    
    toggleSavedCalculations() {
        const isVisible = this.savedSection.style.display !== 'none';
        this.savedSection.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible) {
            this.savedSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    formatDistance(value, unit) {
        if (!isFinite(value) || value === null || value === undefined || value === 'infinity') {
            return '∞';
        }
        
        return `${value.toFixed(2)}${unit}`;
    }
    
    setLoadingState(loading) {
        const form = this.form;
        const button = form.querySelector('.calculate-btn');
        
        if (loading) {
            form.classList.add('loading');
            button.textContent = 'Calculating...';
        } else {
            form.classList.remove('loading');
            button.textContent = 'Calculate Depth of Field';
        }
    }
    
    showError(message) {
        this.showMessage(message, 'error');
    }
    
    showSuccess(message) {
        this.showMessage(message, 'success');
    }
    
    showMessage(message, type) {
        // Remove existing messages
        document.querySelectorAll('.error, .success-message').forEach(el => el.remove());
        
        const messageEl = document.createElement('div');
        messageEl.className = type === 'error' ? 'error' : 'success-message';
        messageEl.textContent = message;
        
        this.form.appendChild(messageEl);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 5000);
    }
    
    initializeDarkMode() {
        // Check for saved dark mode preference or default to light mode
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
    window.dofCalc = new DOFCalculator();
});

// Service worker registration for PWA capabilities (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('./sw.js')
            .then((registration) => {
                console.log('SW registered: ', registration);
            })
            .catch((registrationError) => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}