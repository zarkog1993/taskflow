// Composable za odabir i validaciju slike igrača (max 500 KB), sa live pregledom.
import { ref } from 'vue'

const MAX_PHOTO_SIZE = 500 * 1024

export function usePhotoUpload() {
    const selectedPhoto = ref(null)
    const photoPreview = ref(null)
    const photoError = ref('')

    const handlePhotoSelect = (event) => {
        const file = event.target.files[0]
        photoError.value = ''

        if (!file) {
            selectedPhoto.value = null
            photoPreview.value = null
            return
        }

        if (file.size > MAX_PHOTO_SIZE) {
            photoError.value = 'Slika je prevelika! Maksimalna dozvoljena veličina je 500 KB.'
            event.target.value = ''
            selectedPhoto.value = null
            photoPreview.value = null
            return
        }

        selectedPhoto.value = file
        photoPreview.value = URL.createObjectURL(file)
    }

    const resetPhoto = () => {
        selectedPhoto.value = null
        photoPreview.value = null
        photoError.value = ''
    }

    return {
        selectedPhoto,
        photoPreview,
        photoError,
        handlePhotoSelect,
        resetPhoto
    }
}
