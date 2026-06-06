export function useStreakState(
    getCurrentStreak: () => number,
    getMaxStreak: () => number
) {
    let timeLeftStr = $state('');

    function updateTimeLeft() {
        const now = new Date();
        const endOfDay = new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
            23,
            59,
            59,
            999
        );
        const diffMs = endOfDay.getTime() - now.getTime();
        
        if (diffMs <= 0) {
            timeLeftStr = '00:00:00';
            return;
        }
        
        const hrs = String(Math.floor(diffMs / (1000 * 60 * 60))).padStart(2, '0');
        const mins = String(Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
        const secs = String(Math.floor((diffMs % (1000 * 60)) / 1000)).padStart(2, '0');
        
        timeLeftStr = `${hrs}:${mins}:${secs}`;
    }

    // Automatically manage the timer when this state is used in a component
    $effect(() => {
        updateTimeLeft();
        const interval = setInterval(updateTimeLeft, 1000);
        return () => clearInterval(interval);
    });

    const startDay = $derived(Math.max(1, getCurrentStreak() > 0 ? getCurrentStreak() - 1 : 1));

    const daysToShow = $derived.by(() => {
        const currentStreak = getCurrentStreak();
        return Array.from({ length: 5 }, (_, i) => {
            const dayNum = startDay + i;
            return {
                dayNum,
                isCompleted: dayNum <= currentStreak,
                isCurrentTarget: dayNum === currentStreak + 1,
                isLocked: dayNum > currentStreak + 1,
            };
        });
    });

    return {
        get timeLeftStr() {
            return timeLeftStr;
        },
        get daysToShow() {
            return daysToShow;
        },
        get currentStreak() {
            return getCurrentStreak();
        },
        get maxStreak() {
            return getMaxStreak();
        }
    };
}
