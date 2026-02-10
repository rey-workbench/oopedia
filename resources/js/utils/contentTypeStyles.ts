import { Code, BookOpen } from "lucide-svelte";
import type { ComponentType } from "svelte";

type ContentType = string; // "sintaks" | "teori" — kept loose for flexibility

/**
 * Gradient background for content-type headers.
 */
export function getGradientClass(type: ContentType): string {
    return type === "sintaks"
        ? "from-emerald-500 to-teal-600"
        : "from-blue-500 to-indigo-600";
}

/**
 * Text color for content-type labels.
 */
export function getTextClass(type: ContentType): string {
    return type === "sintaks" ? "text-emerald-600" : "text-blue-600";
}

/**
 * Solid background color for badges / pills.
 */
export function getBgClass(type: ContentType): string {
    return type === "sintaks" ? "bg-emerald-600" : "bg-blue-600";
}

/**
 * Shadow color for cards.
 */
export function getShadowClass(type: ContentType): string {
    return type === "sintaks"
        ? "shadow-emerald-900/20"
        : "shadow-blue-900/20";
}

/**
 * Icon component for the content type.
 */
export function getIcon(type: ContentType): ComponentType {
    return type === "sintaks" ? Code : BookOpen;
}

/**
 * Badge label text.
 */
export function getBadgeLabel(type: ContentType): string {
    return type === "sintaks" ? "Sintaks" : "Teori";
}

// ── Additional helpers used in SubMaterials/Show ─────────────

/**
 * Light background for sub-material cards.
 */
export function getSubMaterialBg(type: ContentType): string {
    return type === "sintaks" ? "bg-emerald-50" : "bg-blue-50";
}

/**
 * Text color for sub-material cards.
 */
export function getSubMaterialText(type: ContentType): string {
    return type === "sintaks" ? "text-emerald-600" : "text-blue-600";
}

/**
 * Hover border color for sub-material navigation links.
 */
export function getHoverBorderClass(type: ContentType): string {
    return type === "sintaks"
        ? "hover:border-emerald-500"
        : "hover:border-blue-500";
}

/**
 * Border color for content sections.
 */
export function getBorderClass(type: ContentType): string {
    return type === "sintaks" ? "border-emerald-100" : "border-blue-100";
}

/**
 * Shadow color for sub-material call-to-action buttons.
 */
export function getCtaShadowClass(type: ContentType): string {
    return type === "sintaks"
        ? "shadow-emerald-500/20"
        : "shadow-blue-500/20";
}
