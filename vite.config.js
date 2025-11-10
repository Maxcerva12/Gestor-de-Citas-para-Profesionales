import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/odontogram-professional.css",
                "resources/css/filament/admin/theme.css",
                "resources/css/filament/client/theme.css",
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    let extType = assetInfo.name.split(".").at(1);
                    if (/css|scss/.test(extType)) {
                        return `assets/[name]-[hash][extname]`;
                    }
                    return `assets/[name]-[hash][extname]`;
                },
            },
        },
    },
});
