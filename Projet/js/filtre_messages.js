window.addEventListener('load',initfiltre);
window.addEventListener('load',filtreByUsers);
window.addEventListener('load',filtreSelect);


function initfiltre(ev){
  fetchFromJson('services/findMessages.php')
.then(processAnswerMessage)
.then(filtreMsg);}


function filtreMsg(msg){
  let node;
  let cible  = document.querySelector('section#messages');
  cible.textContent='';
  for (let i=0;i<Object.keys(msg).length;i++){
   node = document.createElement('div');
   node.innerHTML=`
   <img src ="" class= ${msg[i].author} id=${msg[i].id}><span class="user"> ${msg[i].author}</span><span class="content"> ${msg[i].content}</span><span class="time"> ${msg[i].datetime}</span>
   `;

   cible.appendChild(node);
   let setAvatar= function(blob){
     if(blob.type.startsWith('image/')){
       let img = document.getElementById(msg[i].id);
       img.src =URL.createObjectURL(blob);
     }
   };
   fetchBlob('services/getAvatar.php?userId='+msg[i].author)
   .then(setAvatar);
 }
}



function processAnswerMessage(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}


function errorfiltre(error){
  let p = document.createElement('p');
  p.textContent = error.message;
  let cible  = document.querySelector('section#recherche_messages');
  cible.appendChild(p);
}







function filtreByUsers(){
  document.forms.form_filtre_msg_users.addEventListener('submit',sendFormFiltreByUser);
}

function sendFormFiltreByUser(ev){ // form event listener
  ev.preventDefault();
  let args = new FormData(this);
  fetchFromJson('services/findMessages.php',
 { method: 'post', body: args, credentials: 'same-origin' })
 .then(processAnswerMessage)
 .then(filtreMsg,errorfiltre);
}




function filtreSelect(){
  document.forms.form_filtre.addEventListener('submit',sendFormFiltreBySelect);
}



function sendFormFiltreBySelect(ev){ // form event listener
  ev.preventDefault();
  let valSelect= document.getElementById('selec').value;
  let lien;
  if(valSelect=="aucun"){
    lien="findMessages.php";
  }
  else{
    lien="findFollowedMessages.php";
  }
  let args = new FormData(this);
  fetchFromJson('services/'+lien,
 { method: 'post', body: args, credentials: 'same-origin' })
 .then(processAnswerMessage)
 .then(filtreMsg, errorfiltre);
}
