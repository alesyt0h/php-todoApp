(function(){
    // Shows/hides the password inputs with a transition

    const passwordChanger = document.querySelector('#passwordChanger');
    const trickster = document.querySelector('#trickster');
    
    document.querySelector('#changePasswordBtn').addEventListener('click', () => {
        trickster.style.height = trickster.offsetHeight + 'px';
        passwordChanger.classList.toggle('hidden');
        trickster.style.height = passwordChanger.offsetHeight + 'px';
    });
})();