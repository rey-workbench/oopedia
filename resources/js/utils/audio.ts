/**
 * Audio utility for playing sound effects
 */

const SOUND_PATHS = {
    correct: '/sound/correct.mp3',
    wrong: '/sound/wrong.mp3',
    completed: '/sound/completed.mp3',
} as const;

type SoundType = keyof typeof SOUND_PATHS;

class AudioPlayer {
    private static instance: AudioPlayer;
    private audioCache: Map<string, HTMLAudioElement> = new Map();

    private constructor() {}

    public static getInstance(): AudioPlayer {
        if (!AudioPlayer.instance) {
            AudioPlayer.instance = new AudioPlayer();
        }
        return AudioPlayer.instance;
    }

    /**
     * Play a sound by type
     */
    public play(type: SoundType) {
        const path = SOUND_PATHS[type];
        if (!path) return;

        try {
            let audio = this.audioCache.get(path);
            if (!audio) {
                audio = new Audio(path);
                this.audioCache.set(path, audio);
            }

            // Reset and play
            audio.currentTime = 0;
            audio.play().catch((err) => {
                console.warn('[AudioPlayer] Failed to play sound:', err);
            });
        } catch (err) {
            console.error('[AudioPlayer] Error playing sound:', err);
        }
    }
}

export const playSound = (type: SoundType) => AudioPlayer.getInstance().play(type);
