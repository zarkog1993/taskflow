# Vue 3 + Vite

This template should help get you started developing with Vue 3 in Vite. The template uses Vue 3 `<script setup>` SFCs, check out the [script setup docs](https://v3.vuejs.org/api/sfc-script-setup.html#sfc-script-setup) to learn more.

Learn more about IDE Support for Vue in the [Vue Docs Scaling up Guide](https://vuejs.org/guide/scaling-up/tooling.html#ide-support).

## Themes

The theme toggle applies the `dark` class to `<html>`, including menus and dialogs
teleported to `<body>`. Light mode is the default for component styles; pair light
background, text, border, and interaction utilities with `dark:` variants rather
than using fixed dark colors. Keep white text on solid colored action buttons.
Native form controls follow the theme through `color-scheme`.

Explicit theme choices are saved in `localStorage`. Until a choice is made, the
app follows the system theme, including changes while the app is open.
