window.addEventListener('load',initState);
window.addEventListener('load',initLog);

function processAnswer(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}
var currentUser = null; //objet "personne" de l'utilisateiur connecté

function initState(){ // initialise l'état de la page
  if(document.body.dataset.personne!= null){
    etatConnecte(JSON.parse(document.body.dataset.personne));
  }
  else{
    etatDeconnecte();
  }
}

function etatDeconnecte() { // passe dans l'état 'déconnecté'
    // cache ou montre les éléments
    for (let elt of document.querySelectorAll('.connecte'))
       elt.hidden=true;
    for (let elt of document.querySelectorAll('.deconnecte'))
       elt.hidden=false;
    // nettoie la partie personnalisée :
    currentUser = null;
    delete(document.body.dataset.personne);

    document.querySelector('#avatar').src='';
    document.querySelector('#goCreateUser').addEventListener('click',openCreateUser);
}
function openCreateUser(){
  document.querySelector('#secnew').hidden=false;
  document.forms.createUser.addEventListener('submit',createUser);
}

function etatConnecte(personne) { // passe dans l'état 'connecté'
    currentUser = personne;
    // cache ou montre les éléments
    for (let elt of document.querySelectorAll('.deconnecte'))
       elt.hidden=true;
    for (let elt of document.querySelectorAll('.connecte'))
       elt.hidden=false;

    // personnalise le contenu
    document.querySelector('#titre_connecte').innerHTML = `${currentUser.login} ${currentUser.pseudo}`;
    document.querySelector('#letsUpload').addEventListener('click',openUpdate);
    updateAvatar();
}
function openUpdate(){
  let typeUpload = document.getElementById('sltup').value;
  if (typeUpload=='setpro') {
    document.querySelector('#Setprofile').hidden=false;
    document.forms.stpf.addEventListener('submit',changeProfil);
  }
  if (typeUpload=='uploadphoto') {
    document.querySelector('#UploadAvatare').hidden=false;
    document.forms.uat.addEventListener('submit',changeAvatare)
  }
}
function changeAvatare(ev){
  ev.preventDefault();
  let args = new FormData(this);
  let url = 'services/uploadAvatar.php';
  let options = {method: 'post' , body : args , credentials:'same-origin'};
  fetchFromJson(url,options)
  .then(processAnswer)
  .then(uploadAvatar,errorProfile);
}
function uploadAvatar(ev){
  if (ev) {
    document.querySelector('#here').textContent='';
    document.querySelector('#here').innerHTML='Photo bien mise';
    updateAvatar();
  }
}
function initLog(){ // mise en place des gestionnaires sur le formulaire de login et le bouton logout
  document.forms.form_login.addEventListener('submit',sendLogin); // envoi
  document.forms.form_login.addEventListener('input',function(){this.message.value='';}); // effacement auto du message
  document.querySelector('#logout').addEventListener('click',sendLogout);
}

function updateAvatar() {
    let changeAvatar = function(blob) {
      if (blob.type.startsWith('image/')){ // le mimetype est celui d'une image
        let img = document.getElementById('avatar');
        img.src = URL.createObjectURL(blob);
      }
    };
  fetchBlob('services/getAvatar.php?userId='+currentUser.login)
    .then(changeAvatar);
}


function sendLogin(ev){ // gestionnaire de l'évènement submit sur le formulaire de login
  ev.preventDefault();
  let url = 'services/login.php';
  let args = new FormData(this);
  let options = {method : 'post' , body : args , credentials:'same-origin'};
  fetchFromJson(url,options)
 .then(processAnswer)
 .then(etatConnecte, errorLogin);
}

function sendLogout(ev){ // gestionnaire de l'évènement click sur le bouton logout
  ev.preventDefault();
  fetchFromJson("services/logout.php",
{method : "post", credentials: "same-origin"})
.then(processAnswer)
.then(etatDeconnecte);
}

function errorLogin(error) {
   // affiche error.message dans l'élément OUTPUT.
  document.forms.form_login.message.value = 'échec : ' + error.message;
}
function changeProfil(ev){
  ev.preventDefault();
  let args = new FormData(this);
  let url = 'services/setProfile.php';
  let options = {method: 'post' , body : args , credentials:'same-origin'};
  fetchFromJson(url,options)
  .then(processAnswer)
  .then(updateProfile,errorProfile);
}
function updateProfile(res){
  let p = document.createElement('p');
  p.innerHTML =
    `<span>Id : ${res.login}</span>
     <span>Pseudo : ${res.pseudo}</span>
     `;
     let cible = document.querySelector('#here');
     cible.textContent='';
     cible.appendChild(p);
     document.querySelector('#Setprofile').hidden=true;
     document.querySelector('#titre_connecte').innerHTML = `${currentUser.login} ${res.pseudo}`;

}
function errorProfile(error){
  document.querySelector('#here').innerHTML=error;
}

function createUser(ev){
  ev.preventDefault();
  let args = new FormData(this);
  let url = 'services/createUser.php';
  let options = {method: 'post', body : args , credentials:'same-origin'};
  fetchFromJson(url,options)
  .then(processAnswer)
  .then(newUser,errorUser);
}
function newUser(identit){
  document.querySelector('#doneUser').innerHTML = `${identit.login} ${identit.pseudo}`;
}

function errorUser(error) {
  document.forms.createUser.message.value = 'échec : ' + error.message;
}
