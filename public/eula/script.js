// Lightweight i18n system for RbWallet EULA
class I18n {
  constructor() {
    this.translations = {};
    this.currentLang = 'vi'; // Default to Vietnamese
    this.supportedLangs = ['en', 'vi'];
  }

  // Load translation file
  async loadTranslations(lang) {
    try {
      const response = await fetch(`lang/${lang}.json`);
      if (!response.ok) {
        throw new Error(`Failed to load ${lang}.json`);
      }
      this.translations[lang] = await response.json();
      return this.translations[lang];
    } catch (error) {
      console.error(`Error loading translations for ${lang}:`, error);
      // Fallback to the other language if current lang fails
      const fallbackLang = lang === 'en' ? 'vi' : 'en';
      if (!this.translations[fallbackLang]) {
        return this.loadTranslations(fallbackLang);
      }
      return this.translations[fallbackLang];
    }
  }

  // Get translation value by key path (e.g., "intro.welcome")
  getTranslation(key, lang = this.currentLang) {
    const keys = key.split('.');
    let value = this.translations[lang];
    
    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k];
      } else {
        // Fallback to the other language if key not found
        const fallbackLang = lang === 'en' ? 'vi' : 'en';
        if (this.translations[fallbackLang]) {
          return this.getTranslation(key, fallbackLang);
        }
        return key; // Return key if translation not found
      }
    }
    
    return value || key;
  }

  // Set language and update UI
  async setLanguage(lang) {
    if (!this.supportedLangs.includes(lang)) {
      console.warn(`Unsupported language: ${lang}`);
      return;
    }

    // Load translation if not already loaded
    if (!this.translations[lang]) {
      await this.loadTranslations(lang);
    }

    this.currentLang = lang;
    
    // Update HTML lang attribute
    document.documentElement.lang = lang;
    
    // Update document title
    document.title = this.getTranslation('meta.title', lang);
    
    // Update all elements with data-i18n attribute
    document.querySelectorAll('[data-i18n]').forEach(element => {
      const key = element.getAttribute('data-i18n');
      const translation = this.getTranslation(key, lang);
      
      if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
        element.value = translation;
      } else {
        element.textContent = translation;
      }
    });

    // Update list items with data-i18n-list
    document.querySelectorAll('[data-i18n-list]').forEach(listElement => {
      const key = listElement.getAttribute('data-i18n-list');
      const items = this.getTranslation(key, lang);
      
      if (Array.isArray(items)) {
        const listItems = listElement.querySelectorAll('li');
        items.forEach((item, index) => {
          if (listItems[index]) {
            listItems[index].textContent = item;
          }
        });
      }
    });

    // Update language switcher buttons
    document.querySelectorAll('.lang-btn').forEach(btn => {
      if (btn.getAttribute('data-lang') === lang) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    // Save language preference
    localStorage.setItem('eula-lang', lang);
  }

  // Get URL parameter value
  getUrlParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }

  // Initialize i18n system
  async init() {
    // Priority: URL param > localStorage > browser lang > fallback to 'vi'
    const urlLang = this.getUrlParam('lang');
    const savedLang = localStorage.getItem('eula-lang');
    const browserLang = navigator.language.split('-')[0];
    
    let initialLang = 'vi'; // Default fallback to Vietnamese
    
    // Check URL parameter first
    if (urlLang && this.supportedLangs.includes(urlLang)) {
      initialLang = urlLang;
    } 
    // Then check localStorage
    else if (savedLang && this.supportedLangs.includes(savedLang)) {
      initialLang = savedLang;
    }
    // Then check browser language
    else if (this.supportedLangs.includes(browserLang)) {
      initialLang = browserLang;
    }

    // Load initial language
    await this.setLanguage(initialLang);

    // Set up language switcher
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const lang = e.target.getAttribute('data-lang');
        await this.setLanguage(lang);
      });
    });
  }
}

// Dark Mode Handler
class DarkMode {
  constructor() {
    this.theme = this.getInitialTheme();
    this.init();
  }

  getUrlParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }

  getStoredTheme() {
    return localStorage.getItem('eula-theme');
  }

  getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  getInitialTheme() {
    // Priority: URL param > localStorage > system preference
    const urlTheme = this.getUrlParam('theme');
    if (urlTheme === 'dark' || urlTheme === 'light') {
      return urlTheme;
    }
    
    const storedTheme = this.getStoredTheme();
    if (storedTheme) {
      return storedTheme;
    }
    
    return this.getSystemTheme();
  }

  setTheme(theme) {
    this.theme = theme;
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('eula-theme', theme);
  }

  toggle() {
    const newTheme = this.theme === 'dark' ? 'light' : 'dark';
    this.setTheme(newTheme);
  }

  init() {
    // Set initial theme
    this.setTheme(this.theme);

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
      // Only auto-switch if user hasn't manually set a preference and no URL param
      if (!localStorage.getItem('eula-theme') && !this.getUrlParam('theme')) {
        this.setTheme(e.matches ? 'dark' : 'light');
      }
    });

    // Set up toggle button
    const toggleBtn = document.getElementById('dark-mode-toggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => this.toggle());
    }
  }
}

// Initialize i18n when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
  const i18n = new I18n();
  await i18n.init();
  
  // Initialize dark mode
  const darkMode = new DarkMode();
  
  // Make i18n and darkMode available globally for debugging if needed
  window.i18n = i18n;
  window.darkMode = darkMode;
});

