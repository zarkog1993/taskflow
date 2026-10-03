<template>
    <div ref="root" class="relative min-w-0">
        <button
            ref="trigger"
            :id="attrs.id"
            type="button"
            role="combobox"
            :aria-expanded="isOpen"
            :aria-controls="listboxId"
            :aria-activedescendant="isOpen && activeOption ? optionId(activeIndex) : undefined"
            :aria-label="attrs['aria-label']"
            :disabled="attrs.disabled"
            :class="attrs.class"
            :style="attrs.style"
            class="max-w-full"
            @click="toggle"
            @keydown="onKeydown"
        >
            <span class="block min-w-0 truncate text-left">{{ selectedLabel || placeholder }}</span>
        </button>

        <select
            ref="nativeSelect"
            :name="attrs.name"
            :form="attrs.form"
            :required="attrs.required"
            :disabled="attrs.disabled"
            :value="selectedValue"
            tabindex="-1"
            aria-hidden="true"
            class="pointer-events-none absolute h-px w-px overflow-hidden opacity-0"
        >
            <option
                v-for="(option, index) in options"
                :key="index"
                :value="option.value"
                :disabled="option.disabled"
            >
                {{ option.label }}
            </option>
        </select>

        <Teleport to="body">
            <div
                v-if="isOpen"
                :id="listboxId"
                role="listbox"
                :aria-label="attrs['aria-label'] || selectedLabel || placeholder"
                class="z-[100] overflow-y-auto rounded-xl border border-gray-700 bg-gray-900 py-1 text-white shadow-2xl"
                :style="menuStyle"
            >
                <button
                    v-for="(option, index) in options"
                    :id="optionId(index)"
                    :key="index"
                    type="button"
                    role="option"
                    :aria-selected="isSelected(option)"
                    :aria-disabled="option.disabled"
                    :disabled="option.disabled"
                    class="block w-full px-3 py-2 text-left text-xs hover:bg-gray-700 focus:bg-gray-700 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                    :class="isSelected(option) ? 'bg-indigo-900/70 text-white' : 'text-gray-200'"
                    @click="selectOption(option)"
                >
                    <span class="block truncate">{{ option.label }}</span>
                </button>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, ref, useAttrs, useSlots } from 'vue'

defineOptions({ inheritAttrs: false })

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Object, Array],
        default: undefined
    },
    value: {
        type: [String, Number, Boolean, Object, Array],
        default: undefined
    },
    placeholder: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'change'])
const attrs = useAttrs()
const slots = useSlots()
const root = ref(null)
const trigger = ref(null)
const nativeSelect = ref(null)
const isOpen = ref(false)
const activeIndex = ref(-1)
const menuStyle = ref({})
const listboxId = `app-select-${Math.random().toString(36).slice(2)}`
const selectedValue = computed(() => props.modelValue !== undefined ? props.modelValue : (props.value ?? ''))

const textContent = (node) => {
    if (typeof node === 'string' || typeof node === 'number') return String(node)
    if (Array.isArray(node)) return node.map(textContent).join('')
    if (!node || typeof node !== 'object') return ''
    if (typeof node.children === 'string' || typeof node.children === 'number') return String(node.children)
    return textContent(node.children)
}

const collectOptions = (nodes, result = []) => {
    for (const node of nodes || []) {
        if (typeof node === 'string' || typeof node === 'number') continue
        if (!node || typeof node !== 'object') continue
        if (node.type === 'option') {
            const label = textContent(node.children).trim()
            const value = node.props && Object.prototype.hasOwnProperty.call(node.props, 'value')
                ? node.props.value
                : label
            result.push({
                value,
                label,
                disabled: Boolean(node.props?.disabled)
            })
        } else if (Array.isArray(node.children)) {
            collectOptions(node.children, result)
        }
    }
    return result
}

const options = computed(() => collectOptions(slots.default?.() || []))
const isSelected = (option) =>
    Object.is(option.value, selectedValue.value) ||
    (option.value != null && selectedValue.value != null && String(option.value) === String(selectedValue.value))
const selectedLabel = computed(() => options.value.find(isSelected)?.label || '')
const activeOption = computed(() => options.value[activeIndex.value])

const optionId = (index) => `${listboxId}-option-${index}`

const positionMenu = () => {
    const rect = trigger.value?.getBoundingClientRect()
    if (!rect) return

    const margin = 8
    const gap = 4
    const width = Math.min(rect.width, window.innerWidth - margin * 2)
    const left = Math.max(margin, Math.min(rect.left, window.innerWidth - width - margin))
    const below = window.innerHeight - rect.bottom - margin - gap
    const above = rect.top - margin - gap
    const openAbove = below < 180 && above > below
    const availableHeight = Math.max(80, openAbove ? above : below)

    menuStyle.value = {
        position: 'fixed',
        left: `${left}px`,
        width: `${width}px`,
        maxHeight: `${Math.min(256, availableHeight)}px`,
        ...(openAbove
            ? { bottom: `${window.innerHeight - rect.top + gap}px` }
            : { top: `${rect.bottom + gap}px` })
    }
}

const open = async () => {
    if (attrs.disabled) return
    isOpen.value = true
    const selectedIndex = options.value.findIndex(isSelected)
    activeIndex.value = selectedIndex >= 0 ? selectedIndex : options.value.findIndex(option => !option.disabled)
    await nextTick()
    positionMenu()
}

const close = () => {
    isOpen.value = false
}

const toggle = () => isOpen.value ? close() : open()

const selectOption = (option) => {
    if (option.disabled) return
    emit('update:modelValue', option.value)
    if (nativeSelect.value) {
        nativeSelect.value.value = option.value == null ? '' : String(option.value)
        const event = new Event('change', { bubbles: true })
        nativeSelect.value.dispatchEvent(event)
        emit('change', event)
    }
    close()
    trigger.value?.focus()
}

const onKeydown = (event) => {
    if (event.key === 'Escape') {
        close()
        return
    }

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault()
        if (!isOpen.value) {
            open()
            return
        }

        const step = event.key === 'ArrowDown' ? 1 : -1
        let index = activeIndex.value
        for (let attempt = 0; attempt < options.value.length; attempt += 1) {
            index = (index + step + options.value.length) % options.value.length
            if (!options.value[index]?.disabled) {
                activeIndex.value = index
                break
            }
        }
        return
    }

    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault()
        if (!isOpen.value) open()
        else if (activeOption.value) selectOption(activeOption.value)
    }
}

const onOutsideClick = (event) => {
    if (!root.value?.contains(event.target) && !event.target.closest?.(`#${listboxId}`)) close()
}

const onViewportChange = () => {
    if (isOpen.value) positionMenu()
}

document.addEventListener('pointerdown', onOutsideClick)
window.addEventListener('resize', onViewportChange)
window.addEventListener('scroll', onViewportChange, true)

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onOutsideClick)
    window.removeEventListener('resize', onViewportChange)
    window.removeEventListener('scroll', onViewportChange, true)
})
</script>
