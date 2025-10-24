document.addEventListener("DOMContentLoaded", function () {
    // Remove initial hidden state (slide-in effect)
    document.body.classList.remove("hidden-before-load");

    // Add slide-out effect before navigation
    const links = document.querySelectorAll("a:not([target='_blank']):not([href^='#'])");
    links.forEach(link => {
      link.addEventListener("click", function (e) {
        const href = this.getAttribute("href");

        // Only animate for internal links
        if (href && (href.startsWith("/") || href.includes(window.location.origin))) {
          e.preventDefault();
          document.body.classList.add("slide-out");
          setTimeout(() => {
            window.location.href = href;
          }, 500); // same duration as CSS transition
        }
      });
    });
  });

