import { defineConfig } from 'vite'
import path from 'path'

export default defineConfig({
    plugins: [],
    build: {
        lib: {
            entry: path.resolve(__dirname, 'index.js'),
            name: 'MyLib',
            fileName: (format) => `utils.${format}.js`,
        },
    },
})