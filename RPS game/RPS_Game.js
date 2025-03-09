let score = JSON.parse(localStorage.getItem('Score')) || { win: 0, lost: 0, tie: 0 };

function updateScore() {
  document.querySelector('#score').innerText =
    `Score: Won: ${score.win}, Lost: ${score.lost}, Tie: ${score.tie}`;
}

function generateComputerChoice() {
  const choices = ['Rock', 'Paper', 'Scissor'];
  return choices[Math.floor(Math.random() * 3)];
}

function getResult(userMove, computerMove) {
  if (userMove === computerMove) {
    score.tie++;
    return "It's a tie!";
  }
  
  const winConditions = {
    Rock: "Scissor",
    Paper: "Rock",
    Scissor: "Paper"
  };

  if (winConditions[userMove] === computerMove) {
    score.win++;
    return "You Won!";
  } else {
    score.lost++;
    return "Computer Won!";
  }
}

function playGame(userMove) {
  const computerMove = generateComputerChoice();
  const result = getResult(userMove, computerMove);

  localStorage.setItem('Score', JSON.stringify(score));

  document.querySelector('#user-move').innerText = `You chose: ${userMove}`;
  document.querySelector('#computer-move').innerText = `Computer chose: ${computerMove}`;
  document.querySelector('#result').innerText = result;
  updateScore();
}

function resetScore() {
  score = { win: 0, lost: 0, tie: 0 };
  localStorage.setItem('Score', JSON.stringify(score));
  updateScore();
}

// Initialize score on page load
updateScore();

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
};