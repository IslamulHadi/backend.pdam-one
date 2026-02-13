import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js"
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
    resolve: {
        alias: {
            jquery: "jquery/dist/jquery.min.js",
        },
    },
    optimizeDeps: {
        include: ["jquery", "select2"],
        esbuildOptions: {
            define: {
                global: "globalThis",
            },
        },
    },
    define: {
        global: "globalThis",
    },
});
