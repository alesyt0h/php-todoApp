(function(){
    // Redirects to a middle page for to acknowledge the user the account was created correctly
    // Variables are already declared in register.phtml
    if(created){
        const form = document.querySelector('form');
        form.innerHTML = successMsg;

        setTimeout(() => {
            if(tempUser){
                window.location.href = webRoot + '/todo/assign';
            } else {
                window.location.href = webRoot;
            }
        }, 2000);
    }
})();