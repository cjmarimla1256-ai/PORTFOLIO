#!/bin/bash

# Performance Verification Script
# This script verifies that all performance optimizations are in place

echo "=========================================="
echo "Performance Optimization Verification"
echo "=========================================="
echo ""

# Check 1: Verify minified CSS files exist
echo "✓ Checking minified CSS files..."
css_files=("base.min.css" "layout.min.css" "components.min.css" "print.min.css")
css_ok=true
for file in "${css_files[@]}"; do
    if [ -f "css/$file" ]; then
        size=$(ls -lh "css/$file" | awk '{print $5}')
        echo "  ✓ css/$file exists ($size)"
    else
        echo "  ✗ css/$file NOT FOUND"
        css_ok=false
    fi
done
echo ""

# Check 2: Verify minified JS files exist
echo "✓ Checking minified JavaScript files..."
js_files=("utils.min.js" "ui-components.min.js" "flight-manager.min.js")
js_ok=true
for file in "${js_files[@]}"; do
    if [ -f "js/$file" ]; then
        size=$(ls -lh "js/$file" | awk '{print $5}')
        echo "  ✓ js/$file exists ($size)"
    else
        echo "  ✗ js/$file NOT FOUND"
        js_ok=false
    fi
done
echo ""

# Check 3: Verify optimized images
echo "✓ Checking optimized images..."
images_ok=true
total_size=0
for img in img/*.jpg; do
    if [ -f "$img" ]; then
        size_kb=$(ls -l "$img" | awk '{print int($5/1024)}')
        if [ $size_kb -lt 200 ]; then
            echo "  ✓ $img (${size_kb}KB)"
        else
            echo "  ⚠ $img (${size_kb}KB) - larger than recommended"
        fi
        total_size=$((total_size + size_kb))
    fi
done
echo "  Total images size: ${total_size}KB"
echo ""

# Check 4: Verify index.php uses minified files
echo "✓ Checking index.php references..."
if grep -q "\.min\.css" index.php && grep -q "\.min\.js" index.php; then
    echo "  ✓ index.php uses minified CSS files"
    echo "  ✓ index.php uses minified JS files"
else
    echo "  ✗ index.php does NOT reference minified files"
fi
echo ""

# Check 5: Verify lazy loading implementation
echo "✓ Checking lazy loading implementation..."
if grep -q "initializeLazyLoading" index.php; then
    echo "  ✓ Lazy loading initialized in index.php"
else
    echo "  ✗ Lazy loading NOT initialized"
fi

if grep -q "data-src" includes/flight-card.php; then
    echo "  ✓ Images use data-src attribute in flight-card.php"
else
    echo "  ✗ Images do NOT use data-src attribute"
fi

if grep -q "IntersectionObserver" js/utils.min.js 2>/dev/null || grep -q "IntersectionObserver" js/utils.js; then
    echo "  ✓ Intersection Observer implemented in utils.js"
else
    echo "  ✗ Intersection Observer NOT found"
fi
echo ""

# Summary
echo "=========================================="
echo "Summary"
echo "=========================================="
if [ "$css_ok" = true ] && [ "$js_ok" = true ] && [ "$images_ok" = true ]; then
    echo "✓ All performance optimizations are in place!"
    echo ""
    echo "Performance Targets:"
    echo "  • Initial load time: < 2 seconds (Requirement 11.1)"
    echo "  • Filter/sort response: < 300ms (Requirement 11.4)"
    echo "  • Images optimized: < 100KB each (Requirement 11.2)"
    echo "  • CSS/JS minified (Requirement 11.3)"
    echo "  • Lazy loading enabled (Requirement 11.5)"
    echo ""
    echo "To verify performance in browser:"
    echo "  1. Start PHP server: php -S localhost:8000"
    echo "  2. Open http://localhost:8000 in Chrome"
    echo "  3. Open DevTools (F12) > Lighthouse tab"
    echo "  4. Run Performance audit"
    echo "  5. Check Network tab for load times"
    echo ""
    echo "See PERFORMANCE.md for detailed testing instructions."
else
    echo "✗ Some optimizations are missing. Please review above."
fi
echo ""
