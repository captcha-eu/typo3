#!/bin/bash

set -e

echo "🚀 Starting TYPO3 CaptchaEU Integration Tests"

# Function to test a specific TYPO3 version
test_typo3_version() {
    local version=$1
    local port=$2
    
    echo "📦 Testing TYPO3 ${version}..."
    
    # Start the specific TYPO3 version
    docker compose -f docker-compose.test.yml up -d typo3-${version}
    
    # Wait for TYPO3 to be ready
    echo "⏳ Waiting for TYPO3 ${version} to be ready..."
    timeout=120
    while [ $timeout -gt 0 ]; do
        if curl -s http://localhost:${port} > /dev/null 2>&1; then
            echo "✅ TYPO3 ${version} is ready!"
            break
        fi
        sleep 2
        timeout=$((timeout-2))
    done
    
    if [ $timeout -le 0 ]; then
        echo "❌ TYPO3 ${version} failed to start within 2 minutes"
        return 1
    fi
    
    # Test if extension is available
    echo "🔍 Checking if CaptchaEU extension is loaded..."
    
    # Check if extension files are in place
    docker exec captcha_test_typo3_${version} ls -la /var/www/html/typo3conf/ext/captchaeu_typo3/ || {
        echo "❌ Extension directory not found"
        return 1
    }
    
    # Test if we can access the site
    response=$(curl -s -w "%{http_code}" http://localhost:${port})
    if [[ ${response: -3} == "200" ]]; then
        echo "✅ TYPO3 ${version} is responding correctly"
    else
        echo "⚠️  TYPO3 ${version} responded with status ${response: -3}"
    fi
    
    # Stop the container
    docker compose -f docker-compose.test.yml stop typo3-${version}
    
    echo "✅ TYPO3 ${version} test completed"
}

# Clean up any existing containers
echo "🧹 Cleaning up existing containers..."
docker compose -f docker-compose.test.yml down --volumes --remove-orphans || true

# Test TYPO3 12
test_typo3_version "12" "8012"

# Test TYPO3 13
test_typo3_version "13" "8013"

# Clean up
echo "🧹 Cleaning up..."
docker compose -f docker-compose.test.yml down --volumes --remove-orphans

echo "🎉 Integration tests completed!"

# Instructions for manual testing
cat << 'EOF'

📋 Manual Testing Instructions:
================================

To manually test the extension:

1. Start TYPO3 12:
   docker compose -f docker-compose.test.yml up -d typo3-12
   
   Access: http://localhost:8012
   Login: admin / admin123!

2. Start TYPO3 13:
   docker compose -f docker-compose.test.yml up -d typo3-13
   
   Access: http://localhost:8013
   Login: admin / admin123!

3. In TYPO3 Backend:
   - Go to Admin Tools > Extensions
   - Activate "captchaeu_typo3" extension
   - Go to Web > Page module
   - Create a new page with Form Framework
   - Add CaptchaEU field to your form
   - Test form submission

4. Clean up when done:
   docker compose -f docker-compose.test.yml down --volumes

EOF