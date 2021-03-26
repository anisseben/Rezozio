window.addEventListener('load',initpost);

function initpost(){
  document.forms.form_post_msg.addEventListener('submit',sendFormpost);

}

function sendFormpost(ev){ // form event listener
  ev.preventDefault();
  let args = new FormData(this);
  fetchFromJson('services/postMessage.php',
 { method: 'post', body: args, credentials: 'same-origin' })
 .then(processAnswerPost)
 .then(postmessage, errorpost);
}


function postmessage(msg){
    let node;
    node = document.createElement('div');
    message= msg.args.content;
    node.textContent= message;
    let cible  = document.querySelector('section#messages');
    cible.appendChild(node);
    initfiltre();
}

function errorpost(error){
  let p = document.createElement('p');
  p.textContent = error.message;
  let cible  = document.querySelector('section#post_message');
  cible.appendChild(p);
}


function processAnswerPost(answer){
  if (answer.status == "ok")
    return answer;
  else
    throw new Error(answer.message);
}
