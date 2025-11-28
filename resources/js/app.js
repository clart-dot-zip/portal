import './bootstrap';

// Import Fluent UI Web Components
import {
    provideFluentDesignSystem,
    fluentButton,
    fluentTextField,
    fluentCard,
    fluentBadge,
    fluentDialog,
    fluentMenu,
    fluentMenuItem,
    fluentDivider,
    fluentCheckbox,
    fluentRadio,
    fluentSelect,
    fluentOption,
    fluentTextArea,
    fluentSwitch,
    fluentProgressRing,
    fluentTooltip,
    fluentDataGrid,
    fluentDataGridRow,
    fluentDataGridCell,
} from '@fluentui/web-components';

// Register Fluent UI Web Components
provideFluentDesignSystem()
    .register(
        fluentButton(),
        fluentTextField(),
        fluentCard(),
        fluentBadge(),
        fluentDialog(),
        fluentMenu(),
        fluentMenuItem(),
        fluentDivider(),
        fluentCheckbox(),
        fluentRadio(),
        fluentSelect(),
        fluentOption(),
        fluentTextArea(),
        fluentSwitch(),
        fluentProgressRing(),
        fluentTooltip(),
        fluentDataGrid(),
        fluentDataGridRow(),
        fluentDataGridCell(),
    );

// Import Alpine.js for reactive components
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Alpine.js Global Stores and Data
Alpine.store('navigation', {
    sidebarExpanded: window.innerWidth > 768,
    toggle() {
        this.sidebarExpanded = !this.sidebarExpanded;
    }
});

Alpine.start();

// Fluent UI Helper Functions
window.FluentUI = {
    // Show toast notification (Fluent UI style)
    showToast(message, type = 'info', duration = 4000) {
        const toastContainer = document.getElementById('fluent-toast-container') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `fluent-toast fluent-toast-${type} fluent-fade-in`;
        toast.innerHTML = `
            <div class="fluent-toast-icon">
                ${this.getToastIcon(type)}
            </div>
            <div class="fluent-toast-message">${message}</div>
            <button class="fluent-toast-close" aria-label="Close">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M6.707 6l4.647-4.646a.5.5 0 10-.708-.708L6 5.293 1.354.646a.5.5 0 10-.708.708L5.293 6 .646 10.646a.5.5 0 00.708.708L6 6.707l4.646 4.647a.5.5 0 00.708-.708L6.707 6z"/>
                </svg>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        const closeBtn = toast.querySelector('.fluent-toast-close');
        closeBtn.addEventListener('click', () => this.removeToast(toast));
        
        setTimeout(() => this.removeToast(toast), duration);
    },
    
    getToastIcon(type) {
        const icons = {
            success: '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z"/></svg>',
            error: '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zm.707 4.293a.5.5 0 00-.708 0L8 6.293 6.854 5.146a.5.5 0 10-.708.708L7.293 7 6.146 8.146a.5.5 0 00.708.708L8 7.707l1.146 1.147a.5.5 0 00.708-.708L8.707 7l1.147-1.146a.5.5 0 000-.708z"/></svg>',
            warning: '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zM7.5 5v4a.5.5 0 001 0V5a.5.5 0 00-1 0zm.5 6a.75.75 0 110 1.5.75.75 0 010-1.5z"/></svg>',
            info: '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a7 7 0 110 14A7 7 0 018 1zm0 3a.75.75 0 110 1.5.75.75 0 010-1.5zm0 3a.5.5 0 01.5.5v3a.5.5 0 01-1 0v-3A.5.5 0 018 7z"/></svg>',
        };
        return icons[type] || icons.info;
    },
    
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'fluent-toast-container';
        container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(container);
        return container;
    },
    
    removeToast(toast) {
        toast.classList.add('fluent-fade-out');
        setTimeout(() => toast.remove(), 200);
    },
    
    // Show loading overlay
    showLoading(message = 'Loading...') {
        const overlay = document.createElement('div');
        overlay.id = 'fluent-loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-20 flex items-center justify-center z-50';
        overlay.innerHTML = `
            <div class="fluent-card p-6 flex flex-col items-center gap-4">
                <div class="fluent-spinner"></div>
                <div class="text-sm text-fluent-neutral-30">${message}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    },
    
    hideLoading() {
        const overlay = document.getElementById('fluent-loading-overlay');
        if (overlay) overlay.remove();
    }
};

// Initialize Fluent UI enhancements on DOM load
document.addEventListener('DOMContentLoaded', () => {
    // Add Fluent UI toast styles if not present
    if (!document.getElementById('fluent-toast-styles')) {
        const style = document.createElement('style');
        style.id = 'fluent-toast-styles';
        style.textContent = `
            .fluent-toast {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 6.4px 14.4px 0 rgba(0, 0, 0, 0.132), 0 1.2px 3.6px 0 rgba(0, 0, 0, 0.108);
                min-width: 300px;
                max-width: 400px;
                border-left: 4px solid;
            }
            .fluent-toast-success { border-left-color: #107c10; }
            .fluent-toast-error { border-left-color: #d13438; }
            .fluent-toast-warning { border-left-color: #ffc83d; }
            .fluent-toast-info { border-left-color: #0078d4; }
            .fluent-toast-icon { flex-shrink: 0; }
            .fluent-toast-success .fluent-toast-icon { color: #107c10; }
            .fluent-toast-error .fluent-toast-icon { color: #d13438; }
            .fluent-toast-warning .fluent-toast-icon { color: #ca7f00; }
            .fluent-toast-info .fluent-toast-icon { color: #0078d4; }
            .fluent-toast-message { flex: 1; font-size: 14px; color: #323130; }
            .fluent-toast-close {
                background: none;
                border: none;
                color: #605e5c;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .fluent-toast-close:hover { background: #f3f2f1; }
            .fluent-fade-out { animation: fluentFadeOut 200ms ease-out forwards; }
            @keyframes fluentFadeOut {
                to { opacity: 0; transform: translateX(20px); }
            }
        `;
        document.head.appendChild(style);
    }
});
