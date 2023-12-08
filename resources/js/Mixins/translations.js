export const translations = {
    methods: {
        // Method for translating keys with optional replacements
        __(key, replacements = {}) {
            // Fetch the translation for the provided key or use the key itself if not found
            let translation = window._translations[key] || key;
            return translation; // Return the translated text or the key
        }
    }
}
