window.addEventListener('load',initFollow);






function initFollow(){
  fetchFromJson('services/getFollowers.php')
 .then(processAnswerFollower)
 .then(gestionButton);
}



function gestionButton(followers){
  users=processAnswerUsers();
  users_=getElementsByClassName("users_")
  for(let i=0;Object.keys(users).length;i++){
    for (let j=0;j<Object.keys(followers).length;j++){
      if(users[i].userId==followers[j].userId){
        users_[i].getElementsByTagName("BUTTON").textContent='unfollow';
      }
      else{
        users_[i].getElementsByTagName("BUTTON").textContent='follow';
      }

    }
  }
}



function processAnswerFollower(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
  }
