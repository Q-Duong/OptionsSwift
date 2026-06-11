liveChat = document.querySelector(".live-chat-root");
mainChat = document.querySelector(".maindiv");
animate = document.querySelectorAll(".animate");
messageButton = document.querySelector(".message-input-button");
text = document.querySelector(".sendbox textarea");
chatbox = document.querySelector(".chatbox");
closeChat = document.querySelector(".btn-close-chat");
submitInformation = document.querySelector(".submit-information");
chatboxInformation = document.querySelector(".chatbox-information");
received = document.querySelector(".received");
suggestedOptions = document.querySelector(".suggested-options-root");
sendbox = document.querySelector(".sendbox");

document.addEventListener("DOMContentLoaded", function () {
    const nameInput = document.getElementById("name-chat");
    const phoneInput = document.getElementById("phone-chat");
    const submitButton = document.querySelector(".button-submit");

    function validateName(name) {
        return /^[a-zA-Z\s]+$/.test(name) && name.trim() !== "";
    }

    function validatePhone(phone) {
        return /^(0[3|5|7|8|9][0-9]{8}|(\+84|84)[3|5|7|8|9][0-9]{8})$/.test(phone);
    }

    function checkInputs() {
        const nameValid = validateName(nameInput.value);
        const phoneValid = validatePhone(phoneInput.value);

        submitButton.disabled = !(nameValid && phoneValid);

        nameInput.classList.toggle("valid", nameValid);
        nameInput.classList.toggle(
            "invalid",
            !nameValid && nameInput.value !== ""
        );

        phoneInput.classList.toggle("valid", phoneValid);
        phoneInput.classList.toggle(
            "invalid",
            !phoneValid && phoneInput.value !== ""
        );
    }

    nameInput.addEventListener("input", checkInputs);
    phoneInput.addEventListener("input", checkInputs);

    document
        .querySelector(".submit-information")
        .addEventListener("click", function (e) {
            if (
                validateName(nameInput.value) &&
                validatePhone(phoneInput.value)
            ) {
                chatboxInformation.innerHTML = "";
                received.classList.remove("hidden");
                suggestedOptions.classList.remove("hidden");
                sendbox.classList.remove("hidden");
            } else {
                e.preventDefault();
            }
        });
});

// submitInformation.addEventListener("click", function (e) {
//     if (e) {
//         chatboxInformation.innerHTML = "";
//         received.classList.remove("hidden");
//         suggestedOptions.classList.remove("hidden");
//         sendbox.classList.remove("hidden");
//     }
// });

liveChat.addEventListener("click", function (e) {
    mainChat.style.display = "block";
    liveChat.style.display = "none";
    text.focus();
});
closeChat.addEventListener("click", function (e) {
    mainChat.style.display = "none";
    liveChat.style.display = "block";
});
text.addEventListener("keyup", function (e) {
    if (text.value.trim() !== "") {
        messageButton.innerHTML = `
          <button class="submit">
              <svg viewBox="0 0 12 13" width="20" height="20" fill="currentColor" class="xfx01vb x1lliihq x1tzjh5l x1k90msu x2h7rmj x1qfuztq" style="--color: var(--primary-icon);">
                  <g fill-rule="evenodd" transform="translate(-450 -1073)">
                      <path d="m458.371 1079.75-6.633.375a.243.243 0 0 0-.22.17l-.964 3.255c-.13.418-.024.886.305 1.175a1.08 1.08 0 0 0 1.205.158l8.836-4.413c.428-.214.669-.677.583-1.167-.06-.346-.303-.633-.617-.79l-8.802-4.396a1.073 1.073 0 0 0-1.183.14c-.345.288-.458.77-.325 1.198l.963 3.25c.03.097.118.165.22.17l6.632.375s.254 0 .254.25-.254.25-.254.25"></path>
                  </g>
              </svg>
          </button>`;
        text.style.width = "82%";
        const submit = document.querySelector(".submit");
        if (submit) {
            submit.addEventListener("click", sendmessage);
        }
        if (e.key === "Enter" || e.keyCode === 13) {
            sendmessage();
        }
    } else {
        text.style.width = "100%";
        messageButton.innerHTML = "";
    }
});
text.addEventListener("input", function () {
    this.value = this.value.replace(/\n/g, "");
});
start_animation();

function start_animation() {
    for (let i = 0; i < animate.length; i++) {
        setTimeout(function () {
            animate[i].classList.add("animated");
        }, 300 * i + 300);
    }
}

function sendmessage() {
    messageButton.innerHTML = "";
    text.style.width = "100%";
    data = text.value.trim();
    if (data != "")
        chatbox.innerHTML +=
            '<div class="eachmessage sent animated"><p>' + data + "</p></div>";
    text.value = "";
    chatbox.scrollTop = chatbox.scrollHeight;
}
