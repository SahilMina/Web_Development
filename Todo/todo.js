const STORAGE_KEY = 'todoList'; 

let todoList = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];


document.querySelector('#add-btn').addEventListener('click', addTodo);


displayItems();

function addTodo() {
  const inputElement = document.querySelector('#todo-input');
  const dateElement = document.querySelector('#todo-date');
  const todoItem = inputElement.value.trim();
  const todoDate = dateElement.value;

  if (!todoItem || !todoDate) {
    alert('Please enter a valid todo and date.');
    return;
  }

  todoList.push({ item: todoItem, dueDate: todoDate });

  
  saveToLocalStorage();

  inputElement.value = '';
  dateElement.value = '';

  displayItems();
}

function displayItems() {
  const containerElement = document.querySelector('.todo-container');

   if (todoList.length === 0) {
      containerElement.style.display = 'none';
      return;
    } else {
      containerElement.style.display = 'grid'; 
    }
  
  containerElement.innerHTML = todoList
    .map((todo, index) => `
      <span>${todo.item}</span>
      <span>${todo.dueDate}</span>
      <button class='btn-delete' onclick="deleteTodo(${index})">Delete</button>
    `)
    .join('');
}

 

function deleteTodo(index) {
  todoList.splice(index, 1);

 
  saveToLocalStorage();

  displayItems();
}

function saveToLocalStorage() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(todoList));
}


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