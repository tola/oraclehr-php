particlesJS("particles-js", {
    particles: {
        number: { value: 80 },
        color: { value: "#007bff" },
        shape: { type: "circle" },
        opacity: { value: 0.3 },
        size: { value: 3 },
        move: { enable: true, speed: 1 }
    },
    interactivity: {
        events: {
            onhover: { enable: true, mode: "repulse" },
            onclick: { enable: true, mode: "push" }
        },
        modes: {
            repulse: { distance: 100 },
            push: { quantity: 4 }
        }
    }
});
