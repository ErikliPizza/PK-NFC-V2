import { router } from "@inertiajs/vue3";

// Service function to change the language
export const useChangeLanguage = (language) => {
    return router.post('/change-language', {
        language: language
    }, {
        onSuccess: () => { window.location.reload() }
    });
};
