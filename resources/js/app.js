

import Alpine from 'alpinejs';

const savedTheme = window.localStorage.getItem('wiki-theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
	document.documentElement.classList.add('dark');
}

window.toggleWikiTheme = function () {
	const isDark = document.documentElement.classList.toggle('dark');
	window.localStorage.setItem('wiki-theme', isDark ? 'dark' : 'light');
	window.dispatchEvent(new CustomEvent('wiki-theme-changed', { detail: { isDark } }));
};

window.Alpine = Alpine;

Alpine.start();
