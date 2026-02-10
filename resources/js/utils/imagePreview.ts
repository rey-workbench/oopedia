/**
 * Handle a file-input change event: bind the selected file to a form field
 * and generate a data-URL preview via FileReader.
 *
 * @param event      - The native `change` event from an `<input type="file">`
 * @param form       - The Inertia `useForm` store value (`$form`)
 * @param fieldName  - The form field to assign the file to (e.g. "cover_image")
 * @param setPreview - Callback that receives the data-URL string for preview
 */
export function handleImagePreview(
    event: Event,
    form: Record<string, any>,
    fieldName: string,
    setPreview: (dataUrl: string) => void
): void {
    const input = event.target as HTMLInputElement;
    const file = input?.files?.[0];

    if (file) {
        form[fieldName] = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            const result = (e.target as FileReader).result;
            if (typeof result === "string") {
                setPreview(result);
            }
        };
        reader.readAsDataURL(file);
    }
}
