export const xpAnimationState = $state({
    active: false,
    startX: 0,
    startY: 0,
    amount: 0,

    trigger(x: number, y: number, amount: number = 5) {
        this.startX = x;
        this.startY = y;
        this.amount = Math.min(amount, 10); // Cap for performance
        this.active = true;

        // Reset after animation duration
        setTimeout(() => {
            this.active = false;
        }, 2500);
    },
});
