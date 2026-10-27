document.addEventListener("DOMContentLoaded", function () {

    // ------------------------------------------
    // NOTIFICATION / BANNER CLOSE
    // ------------------------------------------
    const closeButtons = document.querySelectorAll(".close");

    closeButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            const notification = button.closest(".notification");

            if (notification) {
                notification.style.display = "none";
            }
        });
    });

    // ------------------------------------------
    // MOBILE MENU
    // ------------------------------------------
    const menuBtn = document.querySelector(".menu-btn");
    const nav = document.querySelector("nav");

    if (menuBtn && nav) {
        menuBtn.addEventListener("click", function () {
            nav.classList.toggle("active");
        });
    }

    // ------------------------------------------
    // DARK / LIGHT THEME
    // ------------------------------------------
    const themeBtn = document.getElementById("themeBtn");

    if (themeBtn) {

        const savedTheme = localStorage.getItem("studentHubTheme");

        if (savedTheme === "dark") {
            document.body.classList.add("dark");
            themeBtn.textContent = "☀️";
        }

        themeBtn.addEventListener("click", function () {

            document.body.classList.toggle("dark");

            if (document.body.classList.contains("dark")) {
                localStorage.setItem("studentHubTheme", "dark");
                themeBtn.textContent = "☀️";
            } else {
                localStorage.setItem("studentHubTheme", "light");
                themeBtn.textContent = "🌙";
            }

        });
    }

    // ------------------------------------------
    // MODAL
    // ------------------------------------------
    const modal = document.querySelector(".modal");

    if (modal) {

        const closeModalBtn = modal.querySelector(".close");

        if (closeModalBtn) {
            closeModalBtn.addEventListener("click", function () {
                modal.style.display = "none";
            });
        }

        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    // ------------------------------------------
    // ESCAPE KEY - CLOSE MODAL
    // ------------------------------------------
    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {

            const modal = document.querySelector(".modal");

            if (modal) {
                modal.style.display = "none";
            }
        }

    });

    // ------------------------------------------
    // SLIDER
    // ------------------------------------------
    setupSlider();

    // ------------------------------------------
    // FAQ
    // ------------------------------------------
    const faqQuestions = document.querySelectorAll(".faq-question");

    faqQuestions.forEach(function (question) {

        question.addEventListener("click", function () {

            const answer = question.nextElementSibling;

            if (!answer) {
                return;
            }

            if (answer.style.display === "block") {
                answer.style.display = "none";
            } else {
                answer.style.display = "block";
            }

        });

    });

    // ------------------------------------------
    // REGISTRATION FORM
    // ------------------------------------------
    const registerForm = document.getElementById("registerForm");

    if (registerForm) {

        registerForm.addEventListener("submit", function (event) {

            event.preventDefault();

            const studentName =
                document.getElementById("name")?.value.trim();

            const studentId =
                document.getElementById("studentId")?.value.trim();

            const email =
                document.getElementById("email")?.value.trim();

            const password =
                document.getElementById("password")?.value;

            const confirmPassword =
                document.getElementById("confirmPassword")?.value;

            if (!studentName || !studentId || !email || !password) {
                alert("Please fill all required fields.");
                return;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return;
            }

            const user = {
                name: studentName,
                studentId: studentId,
                email: email,
                password: password
            };

            localStorage.setItem(
                "studentHubUser",
                JSON.stringify(user)
            );

            alert("Registration successful!");

            window.location.href = "login.html";

        });

    }

    // ------------------------------------------
    // LOGIN FORM
    // ------------------------------------------
    const loginForm = document.getElementById("loginForm");

    if (loginForm) {

        loginForm.addEventListener("submit", function (event) {

            event.preventDefault();

            const studentId =
                document.getElementById("studentId")?.value.trim();

            const password =
                document.getElementById("password")?.value;

            const savedUser =
                JSON.parse(
                    localStorage.getItem("studentHubUser")
                );

            if (!studentId || !password) {
                alert("Please enter Student ID and Password.");
                return;
            }

            if (savedUser) {

                if (
                    studentId === savedUser.studentId &&
                    password === savedUser.password
                ) {

                    localStorage.setItem(
                        "studentHubLoggedIn",
                        "true"
                    );

                    alert("Login successful!");

                    window.location.href = "dashboard.html";

                } else {

                    alert("Invalid Student ID or Password.");

                }

            } else {

                alert("Please register first.");

                window.location.href = "register.html";

            }

        });

    }

    // ------------------------------------------
    // CONTACT FORM
    // ------------------------------------------
    const contactForm = document.getElementById("contactForm");

    if (contactForm) {

        contactForm.addEventListener("submit", function (event) {

            event.preventDefault();

            const name =
                document.getElementById("name")?.value.trim();

            const email =
                document.getElementById("email")?.value.trim();

            const subject =
                document.getElementById("subject")?.value.trim();

            const message =
                document.getElementById("message")?.value.trim();

            if (!name || !email || !message) {
                alert("Please fill all required fields.");
                return;
            }

            const contactMessage = {
                name: name,
                email: email,
                subject: subject,
                message: message,
                date: new Date().toLocaleString()
            };

            let messages =
                JSON.parse(
                    localStorage.getItem("studentHubMessages")
                ) || [];

            messages.push(contactMessage);

            localStorage.setItem(
                "studentHubMessages",
                JSON.stringify(messages)
            );

            alert("Your message has been submitted successfully!");

            contactForm.reset();

        });

    }

    // ------------------------------------------
    // FEEDBACK FORM
    // ------------------------------------------
    const feedbackForm = document.getElementById("feedbackForm");

    if (feedbackForm) {

        feedbackForm.addEventListener("submit", function (event) {

            event.preventDefault();

            const name =
                document.getElementById("name")?.value.trim();

            const email =
                document.getElementById("email")?.value.trim();

            const feedback =
                document.getElementById("feedback")?.value.trim();

            if (!feedback) {
                alert("Please enter your feedback.");
                return;
            }

            const feedbackData = {
                name: name,
                email: email,
                feedback: feedback,
                date: new Date().toLocaleString()
            };

            let feedbackList =
                JSON.parse(
                    localStorage.getItem("studentHubFeedback")
                ) || [];

            feedbackList.push(feedbackData);

            localStorage.setItem(
                "studentHubFeedback",
                JSON.stringify(feedbackList)
            );

            alert("Thank you for your feedback!");

            feedbackForm.reset();

        });

    }

    // ------------------------------------------
    // CURRENT PAGE NAVIGATION
    // ------------------------------------------
    const currentPage =
        window.location.pathname.split("/").pop();

    const navLinks =
        document.querySelectorAll("nav a");

    navLinks.forEach(function (link) {

        const linkPage =
            link.getAttribute("href");

        if (linkPage === currentPage) {
            link.classList.add("active");
        }

    });

    // ------------------------------------------
    // LOAD PROFILE DATA
    // ------------------------------------------
    loadProfileData();

    // ------------------------------------------
    // LOGOUT
    // ------------------------------------------
    const logoutBtn =
        document.getElementById("logoutBtn");

    if (logoutBtn) {

        logoutBtn.addEventListener("click", function () {

            localStorage.removeItem("studentHubLoggedIn");

            alert("You have been logged out.");

            window.location.href = "login.html";

        });

    }

    // ------------------------------------------
    // SCROLL TO TOP
    // ------------------------------------------
    const topButton =
        document.getElementById("scrollTopBtn");

    if (topButton) {

        window.addEventListener("scroll", function () {

            if (window.scrollY > 300) {
                topButton.style.display = "block";
            } else {
                topButton.style.display = "none";
            }

        });

        topButton.addEventListener("click", function () {

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });

        });

    }

});

// ==========================================
// SLIDER FUNCTION
// ==========================================

let currentSlide = 0;

function setupSlider() {

    const slides =
        document.querySelectorAll(".slide");

    if (slides.length === 0) {
        return;
    }

    slides.forEach(function (slide, index) {

        slide.classList.remove("active-slide");

        if (index === 0) {
            slide.classList.add("active-slide");
        }

    });

    currentSlide = 0;

    setInterval(function () {
        nextSlide();
    }, 5000);

}

function showSlide(index) {

    const slides =
        document.querySelectorAll(".slide");

    if (slides.length === 0) {
        return;
    }

    if (index >= slides.length) {
        currentSlide = 0;
    }

    if (index < 0) {
        currentSlide = slides.length - 1;
    }

    slides.forEach(function (slide) {
        slide.classList.remove("active-slide");
    });

    slides[currentSlide].classList.add("active-slide");

}

function nextSlide() {

    const slides =
        document.querySelectorAll(".slide");

    if (slides.length === 0) {
        return;
    }

    currentSlide++;

    showSlide(currentSlide);

}

function prevSlide() {

    const slides =
        document.querySelectorAll(".slide");

    if (slides.length === 0) {
        return;
    }

    currentSlide--;

    showSlide(currentSlide);

}

// ==========================================
// MODAL FUNCTIONS
// ==========================================

function openModal() {

    const modal =
        document.querySelector(".modal");

    if (modal) {
        modal.style.display = "flex";
    }

}

function closeModal() {

    const modal =
        document.querySelector(".modal");

    if (modal) {
        modal.style.display = "none";
    }

}

// ==========================================
// CLOSE BANNER
// ==========================================

function closeBanner() {

    const banner =
        document.querySelector(".notification");

    if (banner) {
        banner.style.display = "none";
    }

}

// ==========================================
// MOBILE MENU FUNCTION
// ==========================================

function toggleMenu() {

    const nav =
        document.querySelector("nav");

    if (nav) {
        nav.classList.toggle("active");
    }

}

// ==========================================
// SCROLL TO TOP
// ==========================================

function scrollTopPage() {

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

}

// ==========================================
// GO TO PAGE
// ==========================================

function goToPage(page) {

    window.location.href = page;

}

// ==========================================
// PROFILE DATA
// ==========================================

function loadProfileData() {

    const savedUser =
        JSON.parse(
            localStorage.getItem("studentHubUser")
        );

    if (!savedUser) {
        return;
    }

    const nameElements =
        document.querySelectorAll(".profile-name");

    nameElements.forEach(function (element) {
        element.textContent = savedUser.name;
    });

    const idElements =
        document.querySelectorAll(".profile-id");

    idElements.forEach(function (element) {
        element.textContent = savedUser.studentId;
    });

    const emailElements =
        document.querySelectorAll(".profile-email");

    emailElements.forEach(function (element) {
        element.textContent = savedUser.email;
    });

}

// ==========================================
// EVENT REGISTRATION
// ==========================================

function registerEvent(eventName) {

    if (!eventName) {
        eventName = "Event";
    }

    let events =
        JSON.parse(
            localStorage.getItem("studentHubEvents")
        ) || [];

    if (events.includes(eventName)) {

        alert("You are already registered for this event.");

        return;
    }

    events.push(eventName);

    localStorage.setItem(
        "studentHubEvents",
        JSON.stringify(events)
    );

    alert(
        "Successfully registered for " +
        eventName +
        "!"
    );

}

// ==========================================
// CHECK LOGIN
// ==========================================

function checkLogin() {

    const loggedIn =
        localStorage.getItem("studentHubLoggedIn");

    if (loggedIn !== "true") {

        window.location.href = "login.html";

    }

}

// ==========================================
// LOGOUT FUNCTION
// ==========================================

function logout() {

    localStorage.removeItem("studentHubLoggedIn");

    window.location.href = "login.html";

}

// ==========================================
// PREVENT EMPTY LINKS
// ==========================================

document.addEventListener("click", function (event) {

    const link = event.target.closest("a");

    if (!link) {
        return;
    }

    const href = link.getAttribute("href");

    if (href === "#") {

        event.preventDefault();

    }

});
