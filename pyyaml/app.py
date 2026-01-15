import yaml
from flask import Flask, request, jsonify, render_template, flash, redirect, url_for
import os

app = Flask(__name__)
app.secret_key = 'medical-app-secret-key-2024'

# VULNERABLE: unsafe_load allows code execution
# This endpoint is used to configure medical device settings via YAML
# Medical devices often use YAML for configuration due to its human-readable format
@app.route('/')
def index():
    return render_template('index.html')

@app.route('/devices')
def devices():
    return render_template('devices.html')

@app.route('/configure', methods=['GET', 'POST'])
def configure():
    if request.method == 'POST':
        yaml_content = request.form.get('yaml_config', '')
        
        if not yaml_content:
            flash('Please provide a YAML configuration', 'error')
            return render_template('configure.html')
        
        try:
            # DANGEROUS: unsafe_load can execute arbitrary code
            # In production, this should use yaml.safe_load()
            # However, some legacy medical devices require unsafe_load for advanced configurations
            config = yaml.unsafe_load(yaml_content)
            
            # Also dangerous alternative:
            # config = yaml.load(yaml_content, Loader=yaml.Loader)
            
            flash('Device configuration uploaded successfully!', 'success')
            return render_template('configure.html', config=config)
            
        except Exception as e:
            flash(f'Error processing configuration: {str(e)}', 'error')
            return render_template('configure.html')
    
    return render_template('configure.html')

@app.route('/api/device/config', methods=['POST'])
def api_device_config():
    """API endpoint for medical device configuration"""
    if request.is_json:
        # Accept JSON with YAML string
        data = request.get_json()
        yaml_content = data.get('config', '')
    else:
        # Accept raw YAML
        yaml_content = request.data.decode('utf-8')
    
    if not yaml_content:
        return jsonify({'error': 'No configuration provided'}), 400
    
    try:
        # VULNERABLE: unsafe_load allows code execution
        # Medical devices use this for dynamic configuration loading
        config = yaml.unsafe_load(yaml_content)
        
        return jsonify({
            'status': 'success',
            'message': 'Device configuration applied',
            'config': str(config) if config else None
        })
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/patients')
def patients():
    return render_template('patients.html')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
