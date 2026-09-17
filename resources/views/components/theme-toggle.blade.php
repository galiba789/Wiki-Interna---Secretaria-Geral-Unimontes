<div x-data="{ isDark: document.documentElement.classList.contains('dark') }" @wiki-theme-changed.window="isDark = $event.detail.isDark">
    <button
        type="button"
        @click="window.toggleWikiTheme(); isDark = document.documentElement.classList.contains('dark')"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
        :aria-label="isDark ? 'Ativar tema claro' : 'Ativar tema escuro'"
        :title="isDark ? 'Ativar tema claro' : 'Ativar tema escuro'"
    >
        <span x-show="!isDark" aria-hidden="true">&#9790;</span>
        <span x-show="isDark" aria-hidden="true">&#9728;</span>
        <span x-text="isDark ? 'Claro' : 'Escuro'"></span>
    </button>
</div>