window.addEventListener('load',searchUsers);
window.addEventListener('load',searchFollowers);



function searchUsers(){
  document.forms.form_find_user.addEventListener('submit',sendFormSearchUsers);
}

function searchFollowers(){
  document.forms.get_followers.addEventListener('submit',sendFormSearchFollower);
}

function sendFormSearchUsers(ev){ // form event listener
  ev.preventDefault();
  let args = new FormData(this);
  fetchFromJson('services/findUsers.php',
 { method: 'post', body: args, credentials: 'same-origin' })
 .then(processAnswerUsers)
 .then(filtreUsers,errorUser);
}


function sendFormSearchFollower(ev){ // form event listener
  ev.preventDefault();
  let valSelect= document.getElementById('select_filtre').value;
  let lien;
  if(valSelect=="subcribers"){
    lien="getSubscriptions.php";
  }
  else{
    lien="getFollowers.php";
  }
  let args = new FormData(this);
  fetchFromJson('services/'+lien,
 { method: 'post', body: args, credentials: 'same-origin' })
 .then(processAnswerUsers)
 .then(filtreUsers, errorUser);
}









function filtreUsers(user){
  let node;
  let cible  = document.querySelector('section#filter_result');
  cible.textContent='';
  for (let i=0;i<Object.keys(user).length;i++){
   node = document.createElement('div');
   node.innerHTML=`
   <span class="user_"> ${user[i].login} ${user[i].pseudo}</span>
   <button type="submit" name='valid' value="envoyer">Follow</button>
   `;

   cible.appendChild(node);

}
}
function errorUser(error){
    let p = document.createElement('p');
    p.textContent = error.message;
    let cible  = document.querySelector('section#filtre_erreur');
    cible.appendChild(p);

}


function processAnswerUsers(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}
