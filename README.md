# css-cleaner
💥 Laravel package to automatically purge and minify CSS files in the public directory. Clean, optimized, and beginner-friendly with powerful Artisan commands. Made by Ruman 💪



<h1 align="center">🎨 Laravel CSS Cleaner</h1>
<p align="center">
  <b>Remove Unused CSS • Minify • Optimize — All in One for Laravel</b><br>
  <i>Created with 💙 by <strong>Ruman</strong></i>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11+-red?style=flat-square" />
  <img src="https://img.shields.io/badge/PurgeCSS-Automated-green?style=flat-square" />
  <img src="https://img.shields.io/badge/Minified-CSS-blue?style=flat-square" />
  <img src="https://img.shields.io/badge/License-MIT-yellow?style=flat-square" />
</p>

---

## 📦 What is This?

**Laravel CSS Cleaner** is a developer-friendly Laravel package that:

✅ Removes unused CSS  
✅ Minifies your styles  
✅ Optimizes Laravel’s public CSS automatically  
✅ Works with **Tailwind**, **Bootstrap**, or custom styles  
✅ Requires zero manual searching for dead styles!

---

## 🌟 Features

- 🔍 Auto-scans the entire `/public` directory for `.css` files
- ✂️ Removes unused classes using **PurgeCSS**
- 💨 Minifies and compresses CSS after purging
- 📁 Keeps your structure — creates a `/public/css/optimized/` folder
- 🎛️ Artisan-powered: Clean, Minify, or Optimize with one command
- 🧠 Configurable safelist to preserve dynamic classes like `modal`, `fade`, `active`, etc.

---


| Feature | Icon | Description |
|---------|------|-------------|
| **Auto Purge** | 🧹 | Removes unused CSS classes |
| **Smart Minification** | ✨ | Compresses CSS files |
| **Safelist Protection** | 🛡️ | Preserves dynamic classes |
| **Zero Config** | 🎯 | Works out-of-the-box |
| **Progress Tracking** | 📊 | Real-time optimization stats |

---

## ⚙️ Installation

1. Install the package:
```bash
composer require DeshiThemes/css-cleaner

```


Publish the config file:

```bash
php artisan vendor:publish --tag=css-cleaner-config
```
or, if not working or not genarating file under `config/csscleaner.php` then run command (optional)
```bash
php artisan vendor:publish --tag=css-cleaner-config --force
```

Install the required Node.js dependency:

```bash
npm install @fullhuman/postcss-purgecss --save-dev
```
⚙️ Configuration
The configuration file will be published to: `config/csscleaner.php`

```bash
return [
    /**
     * 🔍 CSS Source Directory
     * 
     * Tip: For better performance, specify exact subdirectory when possible:
     * 'css_path' => public_path('css'),
     */
    'css_path' => public_path(),

    /**
     * 📂 Output Directory
     * 
     * Important: Directory will be created automatically
     * but parent directory must exist!
     */
    'output_path' => public_path('css/optimized'),

    /**
     * 🔎 Content Files to Scan
     * 
     * Pro Tip: Add all file types that might contain CSS class names:
     * - Blade templates
     * - JavaScript components
     * - Markdown files (if using)
     */
    'content_paths' => [
        'resources/views/**/*.blade.php',
        'resources/js/**/*.vue',
        // 'storage/framework/views/*.php' // Uncomment for cached views
    ],

    /**
     * 🛡️ Safelist Configuration
     * 
     * Formats:
     * - Exact: 'active' 
     * - Wildcard: 'modal-*' 
     * - Regex: '/^tooltip/'
     * 
     * Always include:
     * - JS-toggled classes
     * - Animation classes
     * - Dynamically generated classes
     */
    'safelist' => [
        // Core classes
        'active', 'show', 'collapse',
        
        // Bootstrap specific
        'modal', 'fade', 'collapsing',
        '/^carousel/', '/^tooltip/', '/^bs-tooltip/',
        
        // Add your project-specific classes here
    ],

    /**
     * ⚙️ PurgeCSS Options
     * 
     * Recommendation: Keep keyframes and fontFace 
     * unless you're manually handling them
     */
    'purge_options' => [
        'keyframes' => true,    // Keep @keyframes
        'fontFace' => true,     // Keep @font-face
        'variables' => false,    // Remove unused CSS variables
        'rejected' => false      // Don't log removed selectors
    ],

    /**
     * ✂️ Minification Options
     * 
     * Warning: preserve_license=false will remove all comments
     * including legal/license blocks!
     */
    'minify_options' => [
        'remove_comments' => true,    // Remove all comments
        'preserve_license' => false,  // Remove even license comments
        'advanced' => true            // Enable advanced optimizations
    ]
];

```
You can customize which classes to always keep and where output should go.

## 🧩 Artisan Commands
🧩 Command	💡 Description	✅ Usage Example

🧼 css:purge	Purges unused CSS from all files in /public	php artisan css:purge
✂️ css:minify	Minifies the cleaned CSS files	php artisan css:minify
🚀 css:optimize	Runs both purge + minify together	php artisan css:optimize

🧪 Beginner-Friendly Usage
🧼 Purge Only
```bash
php artisan css:purge
```
🧹 This removes unused CSS classes from `.css` files and puts them in:


`/public/css/cleaned/`
✂️ Minify Only
```bash
php artisan css:minify
```
🎯 After purging, use this to compress styles — whitespace and comments are removed.

🚀 Optimize (Purge + Minify)
```bash
php artisan css:optimize
```
🔥 Best option for production — a full clean-up + compression in one step.

📁 Example: Input vs Output
Input Files:
```bash
public/css/style.css
public/assets/vendor/bootstrap.css
```
After css:optimize, Output:
```bash
public/css/cleaned/css/style.css
public/css/cleaned/assets/vendor/bootstrap.css
```
✅ Folder structure is preserved
✅ Only used classes remain
✅ Files are compressed


## 🔥 Command Reference

### 🛠️ Project Commands

| Command | Icon | Description | Example |
|---------|------|-------------|---------|
| `php artisan css:purge` | 🧹 | Remove unused CSS | `php artisan css:purge --safelist="active,modal-*"` |
| `php artisan css:minify` | ✨ | Minify CSS files | `php artisan css:minify --path=public/css` |
| `php artisan css:optimize` | 🚀 | Purge + Minify together | `php artisan css:optimize --stats` |

### 🌍 Global Commands

| Command | Icon | Description | Example |
|---------|------|-------------|---------|
| `css-cleaner purge` | 🌐🧹 | Global purge | `css-cleaner purge --path=/var/www/project` |
| `css-cleaner minify` | 🌐✨ | Global minify | `css-cleaner minify --output=dist` |
| `css-cleaner optimize` | 🌐🚀 | Global optimization | `css-cleaner optimize --verbose` |

## 🎨 Command Colors & Styles

### Regular command (white)
```bash
php artisan css:purge
```
### Optional flags (cyan)
```bash
php artisan css:optimize --stats
```
### Path arguments (yellow)
```bash
php artisan css:minify --path=public/css
```
### Safelist values (green)
```bash
php artisan css:purge --safelist="active,show"
```






### 🛠️ Advanced Usage

## 1. Custom Paths
```bash
php artisan css:optimize --css-path=public/assets --output=public/dist
```
## 2. Safelist Patterns


```bash
php artisan css:purge --safelist="active,modal-*,/^tooltip/"
```

## 3. CI/CD Integration (GitHub Actions)

```bash
- name: Optimize CSS
  run: |
    php artisan css:optimize
    git add public/css/cleaned
    git commit -m "Optimized CSS"
```

## 4. Dry Run (Test Only)

```bash
php artisan css:purge --dry-run
```


## 5. Get detailed output with `--verbose:`

```bash
css-cleaner optimize --verbose
```

## 6. Preserve folder structure with:

```bash
php artisan css:optimize --keep-structure
```



## 🚀 When to Use

| Situation | Icon | Benefit |
|-----------|------|---------|
| **Before Production** | 🚀 | Boost performance by 50-90% |
| **Bootstrap/Tailwind Sites** | 🧹 | Remove 80-95% unused CSS |
| **Portfolio/Business Sites** | ⚡ | Faster loading pages |
| **After View Updates** | 🔄 | Keep CSS lean |
| **CI/CD Pipelines** | 🤖 | Automate optimization |

## 💡 Pro Tips

### 1. Always safelist JS classes
```bash
--safelist="show,collapse,fade"
```
### 2. Run after every deploy
```bash
php artisan css:optimize
```
### 3. Add to deployment scripts
```bash
# forge.sh or github-actions.yml
```


## 🔥 Compatibility

<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #ff6b6b; margin: 20px 0;">
💡 <b>Core Requirements:</b> PHP 8.1+ • Laravel 10+ • Node.js 16+
</div>

| Technology | Icon | Version | Highlight |
|------------|------|---------|-----------|
| **PHP** | 🐘 | `8.1 - 8.4` | <span style="color:#2ecc71">✓ Fully Supported</span> |
| **Laravel** | 🎯 | `10.x - 12.x` | <span style="color:#2ecc71">✓ Optimized</span> |
| **Bootstrap** | 🅱️ | `4.x - 5.x` | <span style="color:#e74c3c">⚠️ v3 needs config</span> |
| **Tailwind** | 🌪️ | `All versions` | <span style="color:#2ecc71">✓ Perfect match</span> |
| **Laravel Mix** | 🎨 | `v6+` | <span style="color:#2ecc71">✓ Works great</span> |
| **Vite** | ⚡ | `v3+` | <span style="color:#3498db">➜ Post-build only</span> |
| **Custom CSS** | ✨ | Any `.css` | <span style="color:#2ecc71">✓ Universal</span> |


<summary><b>🔧 Version Details</b></summary>


### Verify your environment matches:
```bash
php -v        # Requires 8.1+
```
composer show laravel/framework  # Requires 10+
```bash
node -v       # Requires 16+
```

| Project Type             | Benefit of Using This Package      |
| ------------------------ | ---------------------------------- |
| 🛍 eCommerce (Bootstrap) | Smaller styles, faster checkout UX |
| 📚 LMS Site (Tailwind)   | Less bloat, faster page loads      |
| 🧑‍🎨 Portfolio          | Light footprint for static sites   |
| 🏢 Business Website      | Quick, optimized performance       |
| 📊 Admin Panel           | Clean dashboard assets             |


## 📊 Benchmark Results

| **Scenario**     | **Original Size** | **Optimized Size** | **Reduction** |
|------------------|------------------:|-------------------:|--------------:|
| Bootstrap 5      | 187 KB            | 24 KB              | **87%**       |
| Tailwind         | 1.2 MB            | 28 KB              | **98%**       |



---

## 🌍 Global Command Setup

### 1. Install Globally
```bash
composer global require deshithemes/css-cleaner
```

## 2. Add to PATH
```bash
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc
source ~/.bashrc
```


## 3. Verify Installation
```bash
css-cleaner --version
```



### Global Usage Examples

 Optimize any project

```bash

css-cleaner optimize --path=/var/www/project/public

# Minify specific directory
css-cleaner minify --path=./public/css
```

### 🛠️ Troubleshooting

## Permission Issues
```bash
sudo chown -R $USER:$USER ~/.composer/
```
## Command Not Found

```bash
composer global bin deshithemes/css-cleaner install
```

## Windows Users

```bash
# Use full path to composer binaries
php C:\Users\You\AppData\Roaming\Composer\vendor\bin\css-cleaner optimize
```



## ❓ FAQ
<details> <summary><b>🔮 Will this break my JavaScript classes?</b></summary> No! The safelist protects dynamic classes. Test in staging first. </details><details> <summary><b>⏱ How often should I run this?</b></summary> Before every production deployment or after CSS changes. </details><details> <summary><b>⚡ Does this work with Vite?</b></summary> Yes! Works with any build system (Vite, Mix, etc.) </details>


## 💖 Support
<div align="center"> <a href="mailto:roman.civil2019@gmail.com"> <img src="https://img.shields.io/badge/✉️_Email-D14836?style=for-the-badge&logo=gmail&logoColor=white" /> </a> <a href="https://github.com/DeshiThemes/css-cleaner/issues"> <img src="https://img.shields.io/badge/🐞_Issues-181717?style=for-the-badge&logo=github&logoColor=white" /> </a> <a href="https://github.com/DeshiThemes/css-cleaner/stargazers"> <img src="https://img.shields.io/badge/⭐_Star-FFD700?style=for-the-badge&logo=github&logoColor=black" /> </a> </div><p align="center"> <b>✨ Crafted with ❤️ by <a href="https://github.com/DeshiThemes">Ruman</a></b> </p> ```