/**
 * App Bundle & Orchestrator
 * Main entry point for the application
 * Loads all necessary utilities, components, and page-specific logic
 */

// Bootstrap dependencies
import './bootstrap';

// Core utilities (makes Http, UI, Tour, DOM, Scrollbar globally available)
import './utils/index.js';

// Auth pages
import './pages/auth/index.js';

// Bundles (these will load their own dependencies)
import './bundles/mahasiswa.js';
import './bundles/admin.js';

console.log('App bundle loaded');
