<<<<<<< HEAD
let currentDisplay = '';
document.querySelector('#display').value = currentDisplay;


                      // Animation for the title


    window.onload = function () {
let title = document.getElementById("animated-title");
let text = title.innerText;
title.innerHTML = ""; 

for (let i = 0; i < text.length; i++) {
let span = document.createElement("span");
span.innerText = text[i];
span.style.animationDelay = i * 0.1 + "s";
span.classList.add("animate-letter");
title.appendChild(span);
}
=======
let currentDisplay = '';
document.querySelector('#display').value = currentDisplay;


                      // Animation for the title


    window.onload = function () {
let title = document.getElementById("animated-title");
let text = title.innerText;
title.innerHTML = ""; 

for (let i = 0; i < text.length; i++) {
let span = document.createElement("span");
span.innerText = text[i];
span.style.animationDelay = i * 0.1 + "s";
span.classList.add("animate-letter");
title.appendChild(span);
}
>>>>>>> 14b960ae6fb5ae2b11d028430dd301ca2c6010cf
};