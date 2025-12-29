# CaptchaEU TYPO3 Extension Testing

This document describes how to test the CaptchaEU extension with real TYPO3 installations.

## Quick Integration Test

Run the automated integration test:

```bash
./test-integration.sh
```

This will:
- Start TYPO3 12 and 13 in Docker containers
- Verify the extension loads correctly
- Test basic functionality
- Clean up afterwards

## Manual Testing

### 1. Start TYPO3 Test Environment

Start TYPO3 12:
```bash
docker-compose -f docker-compose.test.yml up -d typo3-12
```

Start TYPO3 13:
```bash
docker-compose -f docker-compose.test.yml up -d typo3-13
```

### 2. Access TYPO3 Backend

- **TYPO3 12**: http://localhost:8012
- **TYPO3 13**: http://localhost:8013
- **Login**: admin / admin123!

### 3. Activate Extension

1. Go to **Admin Tools** > **Extensions**
2. Search for "captchaeu"
3. Click **Activate** on "CaptchaEU"

### 4. Configure CaptchaEU

1. Go to **Site Management** > **Sites**
2. Edit your site configuration
3. Add CaptchaEU settings:
   - **Host**: `https://www.captcha.eu` (or your custom host)
   - **Public Key**: Your CaptchaEU public key
   - **REST Key**: Your CaptchaEU REST key

### 5. Create Test Form

1. Go to **Web** > **Page**
2. Create a new page
3. Add a **Form** content element
4. Create a new form with:
   - Text field (name, email, etc.)
   - **CaptchaEU** field
   - Submit button

### 6. Test Form Functionality

1. Visit the frontend page with your form
2. Verify the CaptchaEU widget loads
3. Fill out the form and submit
4. Check that validation works correctly

### 7. Test Different Scenarios

- **Without keys**: Form should work but captcha validation disabled
- **With test keys**: Form should validate captcha
- **Invalid submission**: Should show validation errors
- **Valid submission**: Should process form successfully

## Unit Tests

Run unit tests:
```bash
composer test
```

Run with coverage:
```bash
composer test:coverage
```

## Code Quality

Check code style:
```bash
composer cs
```

Fix code style issues:
```bash
composer cs:fix
```

Run static analysis:
```bash
composer analyse
```

## Integration Tests (Advanced)

Run TYPO3 functional tests:
```bash
composer test:integration
```

## Cleanup

Stop and remove all test containers:
```bash
docker-compose -f docker-compose.test.yml down --volumes
```

## Troubleshooting

### Extension Not Loading

1. Check if extension files are in `/var/www/html/typo3conf/ext/captchaeu_typo3/`
2. Verify composer.json is valid
3. Check TYPO3 logs in the backend

### Form Not Showing CaptchaEU Field

1. Ensure the Form extension is active
2. Check if CaptchaEU configuration is loaded
3. Verify TypoScript setup is applied

### Captcha Not Rendering

1. Check browser console for JavaScript errors
2. Verify CaptchaEU host is accessible
3. Check public key configuration

### Validation Errors

1. Verify REST key is correct
2. Check network connectivity to CaptchaEU service
3. Review TYPO3 system logs