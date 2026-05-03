const fs = require('fs');
const path = require('path');

function copyDir(src, dest) {
    if (!fs.existsSync(src)) { console.warn('SKIP (not found):', src); return; }
    fs.mkdirSync(dest, { recursive: true });
    for (const entry of fs.readdirSync(src, { withFileTypes: true })) {
        const srcPath  = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);
        entry.isDirectory() ? copyDir(srcPath, destPath) : fs.copyFileSync(srcPath, destPath);
    }
}

function copyFile(src, dest) {
    if (!fs.existsSync(src)) { console.warn('SKIP (not found):', src); return; }
    fs.mkdirSync(path.dirname(dest), { recursive: true });
    fs.copyFileSync(src, dest);
}

const nm  = path.resolve(__dirname, '..', 'node_modules');
const pub = path.resolve(__dirname, '..', 'public');
const pl  = path.join(pub, 'plugins');

console.log('Copying AdminLTE assets...');

// AdminLTE dist
copyDir(path.join(nm, 'admin-lte', 'dist'), path.join(pub, 'dist'));

// jQuery
copyDir(path.join(nm, 'jquery', 'dist'), path.join(pl, 'jquery'));

// Bootstrap
copyDir(path.join(nm, 'bootstrap', 'dist'), path.join(pl, 'bootstrap'));

// FontAwesome
copyDir(path.join(nm, '@fortawesome', 'fontawesome-free', 'css'),    path.join(pl, 'fontawesome-free', 'css'));
copyDir(path.join(nm, '@fortawesome', 'fontawesome-free', 'webfonts'),path.join(pl, 'fontawesome-free', 'webfonts'));

// OverlayScrollbars
copyDir(path.join(nm, 'overlayscrollbars', 'js'),  path.join(pl, 'overlayScrollbars', 'js'));
copyDir(path.join(nm, 'overlayscrollbars', 'css'), path.join(pl, 'overlayScrollbars', 'css'));

// Chart.js
copyFile(path.join(nm, 'chart.js', 'dist', 'Chart.bundle.min.js'), path.join(pl, 'chart.js', 'Chart.bundle.min.js'));

// DataTables
copyDir(path.join(nm, 'datatables.net', 'js'),     path.join(pl, 'datatables', 'js'));
copyDir(path.join(nm, 'datatables.net-bs4', 'js'), path.join(pl, 'datatables-bs4', 'js'));
copyDir(path.join(nm, 'datatables.net-bs4', 'css'),path.join(pl, 'datatables-bs4', 'css'));

// Select2
copyDir(path.join(nm, 'select2', 'dist'), path.join(pl, 'select2'));

// SweetAlert2
copyDir(path.join(nm, 'sweetalert2', 'dist'), path.join(pl, 'sweetalert2'));

console.log('Done.');