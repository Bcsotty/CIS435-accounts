function openTab(event, tabName) {
    var i, tabContent, tabLinks;

    tabContent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    tabLinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tabLinks.length; i++) {
        tabLinks[i].className = tabLinks[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    event.currentTarget.className += " active";
}

function validatePassword() {
    var password = document.getElementById("new_password").value;
    var confirm_password = document.getElementById("password_confirm").value;
    var incorrect_input = document.getElementById("invalid_input");
    if (password != confirm_password) {
        incorrect_input.innerHTML = 'Passwords do not match!';
        document.getElementById("register_submit").disabled = true;
    } else {
        incorrect_input.innerHTML = '';
        document.getElementById("register_submit").disabled = false;
    }
}

function validateEmail(address) {
    if (/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(address)) {
        return true;
    } else {
        return false;
    }
}

async function register() {
    var username = document.getElementById("new_username").value;
    var password = document.getElementById("new_password").value;
    var email = document.getElementById("email").value;

    var data = {
        username: username,
        password: password,
        email: email
    }

    if (!validateEmail(email)) {
        alert("Invalid email address");
        return;
    } else {
        const response = await fetch("register.php", 
            { 
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data),
            });
        
        if (response.ok) {
            var contentType = response.headers.get("Content-Type");
            if (contentType && contentType.includes("application/json")) {
                response.json().then((data) => {
                    alert(data.errors);
                });
            } else {
                window.location.href = response.url;
            }
        }
    }
}

let registerForm = document.getElementById("register_form");
registerForm.addEventListener("submit", (e) => {
    e.preventDefault();
    register();
})