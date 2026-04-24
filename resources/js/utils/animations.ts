export function scrollReveal(node: HTMLElement, options: { 
    delay?: number; 
    duration?: number; 
    y?: number; 
    x?: number;
    once?: boolean;
    scale?: number;
} = {}) {
    const { 
        delay = 0, 
        duration = 1000, 
        y = 40, 
        x = 0,
        once = true,
        scale = 1
    } = options;

    node.style.opacity = '0';
    node.style.transform = `translate3d(${x}px, ${y}px, 0) scale(${scale})`;
    node.style.transition = `opacity ${duration}ms cubic-bezier(0.21, 0.47, 0.32, 0.98), transform ${duration}ms cubic-bezier(0.21, 0.47, 0.32, 0.98)`;
    node.style.transitionDelay = `${delay}ms`;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    node.style.opacity = '1';
                    node.style.transform = `translate3d(0, 0, 0) scale(1)`;
                    if (once) observer.unobserve(node);
                } else if (!once) {
                    node.style.opacity = '0';
                    node.style.transform = `translate3d(${x}px, ${y}px, 0) scale(${scale})`;
                }
            });
        },
        { threshold: 0.1 }
    );

    observer.observe(node);

    return {
        destroy() {
            observer.disconnect();
        }
    };
}
