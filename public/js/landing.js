window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    const liquidBg = document.getElementById("liquidBg");

    if (liquidBg) {
        liquidBg.style.transform = `
            translateY(${scrollY * 0.12}px)
            rotate(${scrollY * 0.01}deg)
            scale(${1 + scrollY * 0.0003})
        `;
    }

    document.querySelectorAll(".fade-up").forEach(el => {
        const top = el.getBoundingClientRect().top;
        if (top < window.innerHeight - 100) {
            el.classList.add("show");
        }
    });
});
